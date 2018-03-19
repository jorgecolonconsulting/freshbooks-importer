<?php
/**
 * Created by PhpStorm.
 * User: x2UP_Media
 * Date: 3/18/18
 * Time: 11:08 PM
 */
namespace _2UpMedia\FreshbooksImporter\Services\Freshbooks;


use _2UpMedia\FreshbooksImporter\Services\Clients\FreshbooksFactory;
use _2UpMedia\FreshbooksImporter\Services\JsonResponseDecorator;

class ListTasks
{
    private $clientFactory;

    public function __construct(FreshbooksFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }

    public function headers()
    {
        return ['Task ID', 'Task Name'];
    }

    public function rows()
    {
        $client = $this->clientFactory->build();

        $accoundId = config('freshbooks-importer.freshbooks.account-id');

        $response = new JsonResponseDecorator($client->get("accounting/account/$accoundId/projects/tasks"));

        $body = $response->getBody();

        $tasks = $body->response->result->tasks;

        return collect($tasks)->map(function($task) {
            return [$task->id, $task->name];
        })->all();
    }
}