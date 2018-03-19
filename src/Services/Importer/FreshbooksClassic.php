<?php
/**
 * Created by PhpStorm.
 * User: x2UP_Media
 * Date: 10/17/17
 * Time: 3:34 AM
 */

namespace _2UpMedia\FreshbooksImporter\Importer;

use _2UpMedia\FreshbooksImporter\Commands\ListProjectsCommand;
use _2UpMedia\FreshbooksImporter\Commands\ListTasksCommand;
use Freshbooks\FreshBooksApi;
use Illuminate\Console\Command;
use League\Csv\Reader;

class FreshbooksClassic implements HarvestImporterAdapterInterface
{
    /**
     * @var FreshBooksApi
     */
    private $fbApi;

    /**
     * @var array
     */
    private $unknownTasks = [];

    /**
     * @var array
     */
    private $unknownProjects = [];

    /**
     * @var ListProjectsCommand
     */
    private $listProjectsCommand;

    /**
     * @var ListTasksCommand
     */
    private $listTasksCommand;

    public function __construct(ListProjectsCommand $listProjectsCommand, ListTasksCommand $listTasksCommand)
    {
        $this->projectMappings = config('freshbooks-importer.freshbooks-classic.harvest.projects-mappings');
        $this->tasksMapping = config('freshbooks-importer.freshbooks-classic.harvest.tasks-mappings');
        $this->listProjectsCommand = $listProjectsCommand;
        $this->listTasksCommand = $listTasksCommand;
    }

    /**
     * @param string $csvPath
     * @param Command $console
     */
    public function consume(string $csvPath, Command $console)
    {
        $this->connectApi();

        $records = $this->getCsvRecords($csvPath);

        $timeEntriesMapped = [];
        foreach ($records as $record) {
            $timeEntriesMapped[] = $this->mapRecord($record);
        }

        $this->checkIfUnknownProjectsAndTasks($console);

        if ($this->unknownProjects || $this->unknownTasks) {
            $message = 'An unknown Project or Task was encountered. Please add these mappings to the '
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

    public function listTasksHeaders()
    {
        return ['Task ID', 'Task Name'];
    }

    public function listTasks()
    {
        $this->connectApi();

        $this->fbApi->setMethod('task.list');

        $this->fbApi->post([
            'per_page' => 100
        ]);

        $this->fbApi->request();

        $response = $this->fbApi->getResponse();

        return collect($response['tasks']['task'])
            ->map(function($value) {
                return array_only($value, ['task_id', 'name']);
            })
            ->toArray();
    }

    public function listProjectsHeaders()
    {
        return ['Project ID', 'Project Name'];
    }

    public function listProjects()
    {
        $this->connectApi();

        $this->fbApi->setMethod('project.list');

        $this->fbApi->post([
            'per_page' => 100
        ]);

        $this->fbApi->request();

        $response = $this->fbApi->getResponse();

        return collect($response['projects']['project'])
            ->map(function($value) {
                return array_only($value, ['project_id', 'name']);
            })
            ->toArray();
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
     * @return FreshBooksApi
     */
    private function connectApi()
    {
        $fbApi = $this->initializeApiConnection();

        // set default method
        $fbApi->setMethod('time_entry.create');

        $this->fbApi = $fbApi;
    }

    /**
     * @param $timeEntryMapped
     *
     * @return array
     */
    private function createTimeEntry($timeEntryMapped): array
    {
        $this->fbApi->post([
            'time_entry' => $timeEntryMapped
        ]);

        $this->fbApi->request();

        return $this->fbApi->getResponse();
    }

    /**
     * @return FreshBooksApi
     */
    private function initializeApiConnection(): FreshBooksApi
    {
        $domain = config('freshbooks-importer.freshbooks-classic.subdomain'); // Do not include the URL scheme (https://). It will be added automatically
        $token = config('freshbooks-importer.freshbooks-classic.authentication-token'); // your api token found in your account
        $fb = new FreshBooksApi($domain, $token);

        return $fb;
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

    /**
     * @param $record
     * @return array
     */
    private function mapRecord($record): array
    {
        $projectName = $record['Project'];
        $taskName = $record['Task'];

        if (!isset($this->projectMappings[$projectName])) {
            $this->unknownProjects[] = $projectName;
        }

        if (!isset($this->tasksMapping[$taskName])) {
            $this->unknownTasks[] = $taskName;
        }

        return [
            'project_id' => $this->projectMappings[$projectName] ?? null,
            'task_id' => $this->tasksMapping[$taskName] ?? null,
            'hours' => $record['Hours'],
            'date' => $record['Date'],
            'notes' => $record['Notes']
        ];
    }

    /**
     * @param Command $console
     */
    private function checkIfUnknownProjectsAndTasks(Command $console)
    {
        $wrapWithArray = function ($item) {
            return [$item];
        };

        if ($this->unknownProjects) {
            $console->table(['Unknown Project'], array_map($wrapWithArray, array_unique($this->unknownProjects)));
            $console->info("Here's the list of projects in Freshbooks");
            $console->call($this->listProjectsCommand->getName());
        }

        if ($this->unknownTasks) {
            $console->table(['Unknown Tasks'], array_map($wrapWithArray, array_unique($this->unknownTasks)));
            $console->info("Here's the list of tasks in Freshbooks");
            $console->call($this->listTasksCommand->getName());
        }
    }
}