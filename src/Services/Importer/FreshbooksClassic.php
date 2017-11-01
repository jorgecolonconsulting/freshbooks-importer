<?php
/**
 * Created by PhpStorm.
 * User: x2UP_Media
 * Date: 10/17/17
 * Time: 3:34 AM
 */

namespace _2UpMedia\FreshbooksImporter\Importer;

use Freshbooks\FreshBooksApi;
use Illuminate\Console\Command;
use League\Csv\Reader;

class FreshbooksClassic
{
    /**
     * @var FreshBooksApi
     */
    private $fbApi;

    public function __construct()
    {
        $this->projectMappings = config('freshbooks-importer.freshbooks-classic.harvest.projects-mappings');
        $this->tasksMapping = config('freshbooks-importer.freshbooks-classic.harvest.tasks-mappings');
    }

    public function consume(string $csvPath, Command $console)
    {
        $this->connectApi();

        $records = $this->getCsvRecords($csvPath);

        foreach ($records as $record) {
            $timeEntryMapped = $this->mapRecord($record);

            $console->comment($this->varExport($timeEntryMapped));

            $response = $this->createTimeEntry($timeEntryMapped);

            $console->comment($this->varExport($response));
        }

        $console->comment('done');
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
        $domain = '2upmedia'; // Do not include the URL scheme (https://). It will be added automatically
        $token = 'e35b9984c6355b45da77855c67730c2b'; // your api token found in your account
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
        return [
            'project_id' => $this->projectMappings[$record['Project']],
            'task_id' => $this->tasksMapping[$record['Task']],
            'hours' => $record['Hours'],
            'date' => $record['Date'],
            'notes' => $record['Notes']
        ];
    }
}