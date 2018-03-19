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

class ListProjects
{
    private $clientFactory;

    public function __construct(FreshbooksFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }

    public function headers()
    {
        return ['Project ID', 'Project Name'];
    }

    public function rows()
    {
        $client = $this->clientFactory->build();

        $businessId = config('freshbooks-importer.freshbooks.business-id');

        $response = new JsonResponseDecorator($client->get("projects/business/$businessId/projects"));

        $body = $response->getBody();

        $projects = $body->projects;

        return collect($projects)->map(function($project) {
            return [$project->id, $project->title];
        })->all();
    }
}