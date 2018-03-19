<?php
/**
 * Created by PhpStorm.
 * User: x2UP_Media
 * Date: 3/15/18
 * Time: 9:47 PM
 */

namespace _2UpMedia\FreshbooksImporter\Importer;


use _2UpMedia\FreshbooksImporter\Commands\ListClientsCommand;
use _2UpMedia\FreshbooksImporter\Commands\ListProjectsCommand;
use _2UpMedia\FreshbooksImporter\Services\Clients\FreshbooksFactory;
use _2UpMedia\FreshbooksImporter\Services\JsonResponseDecorator;
use Illuminate\Console\Command;
use League\Csv\Reader;

class Freshbooks implements HarvestImporterAdapterInterface
{
    /**
     * @var array
     */
    private $unknownClients = [];

    /**
     * @var array
     */
    private $unknownProjects = [];

    /**
     * @var ListProjectsCommand
     */
    private $listProjectsCommand;

    /**
     * @var ListClientsCommand
     */
    private $listClientsCommand;

    /**
     * @var array
     */
    private $projectsMappings;

    /**
     * @var array
     */
    private $clientsMapping;

    /**
     * @var FreshbooksFactory
     */
    private $clientFactory;

    public function __construct(ListProjectsCommand $listProjectsCommand, ListClientsCommand $listClientsCommand, FreshbooksFactory $clientFactory)
    {
        $this->projectsMappings = config('freshbooks-importer.freshbooks.harvest.projects-mappings');
        $this->clientsMapping = config('freshbooks-importer.freshbooks.harvest.clients-mappings');
        $this->listProjectsCommand = $listProjectsCommand;
        $this->listClientsCommand = $listClientsCommand;
        $this->clientFactory = $clientFactory;
    }

    public function consume(string $csvPath, Command $console)
    {
        $records = $this->getCsvRecords($csvPath);

        $timeEntriesMapped = [];
        foreach ($records as $record) {
            $timeEntriesMapped[] = $this->mapRecord($record);
        }

        $this->checkIfUnknownProjectsAndClients($console);

        if ($this->unknownProjects || $this->unknownClients) {
            $message = 'An unknown Project or Client was encountered. Please add these mappings to the '
                . 'freshbooks-importer config file and then re-run the import.';

            return $console->error($message);
        }

        foreach ($timeEntriesMapped as $timeEntryMapped) {
            $console->comment($this->varExport($timeEntryMapped));
            $response = $this->createTimeEntry($timeEntryMapped);
            $console->comment($this->varExport($response));
        }

        $console->comment('Time entries were uploaded');
    }

    /**
     * @param $timeEntryMapped
     *
     * @return array
     */
    private function createTimeEntry($timeEntryMapped): \stdClass
    {
        $client = $this->clientFactory->build();

        $businessId = config('freshbooks-importer.freshbooks.business-id');

        $response = new JsonResponseDecorator($client->post(
            "timetracking/business/$businessId/time_entries",
            [
                'json' => [
                    'time_entry' => $timeEntryMapped
                ],
                'headers' => [
                    'Api-Version' => 'alpha'
                ]
            ]
        ));

        return $response->getBody();
    }

    /**
     * @param $record
     * @return array
     */
    private function mapRecord($record): array
    {
        $projectName = $record['Project'];
        $clientName = $record['Client'];
        $taskName = $record['Task'];

        if (!isset($this->projectsMappings[$projectName])) {
            $this->unknownProjects[] = $projectName;
        }

        if (!isset($this->clientsMapping[$clientName])) {
            $this->unknownClients[] = $clientName;
        }

        $SECONDS = 60;
        $MINUTES = 60;

        return [
            'is_logged' => true,
            'duration' => (integer) ceil($record['Hours'] * $MINUTES * $SECONDS),
            'note' => "{$taskName} - {$record['Notes']}",
            'started_at' => (new \DateTime($record['Date']))->format('Y-m-d\TH:i:s\Z'),
            'client_id' => $this->clientsMapping[$clientName] ?? null,
            'project_id' => $this->projectsMappings[$projectName] ?? null,
        ];
    }

    /**
     * @param Command $console
     */
    private function checkIfUnknownProjectsAndClients(Command $console)
    {
        $wrapWithArray = function ($item) {
            return [$item];
        };

        if ($this->unknownProjects) {
            $console->table(['Unknown Project'], array_map($wrapWithArray, array_unique($this->unknownProjects)));
            $console->info("Here's the list of projects in Freshbooks");
            $console->call($this->listProjectsCommand->getName());
        }

        if ($this->unknownClients) {
            $console->table(['Unknown Clients'], array_map($wrapWithArray, array_unique($this->unknownClients)));
            $console->info("Here's the list of clients in Freshbooks");
            $console->call($this->listClientsCommand->getName());
        }
    }

    /**
     * @param $args
     * @return mixed
     */
    private function varExport(...$args)
    {
        $varExports = array_map('var_export', $args, array_fill(0, count($args), true));

        return collect($varExports)->implode("\n");
    }

    /**
     * @param string $csvPath
     * @return \Iterator
     */
    private function getCsvRecords(string $csvPath): \Iterator
    {
        $reader = Reader::createFromPath($csvPath);
        $reader->setHeaderOffset(0);
        $records = $reader->getRecords();

        return $records;
    }
}
