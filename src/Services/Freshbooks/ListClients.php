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

class ListClients
{
    private $clientFactory;

    public function __construct(FreshbooksFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }

    public function headers()
    {
        return ['Client ID', 'Client Name'];
    }

    public function rows()
    {
        $client = $this->clientFactory->build();

        $accoundId = config('freshbooks-importer.freshbooks.account-id');

        $response = new JsonResponseDecorator($client->get("accounting/account/$accoundId/users/clients"));

        $body = $response->getBody();

        $clients = $body->response->result->clients;

        return collect($clients)->map(function($client) {
            return [$client->id, $client->organization];
        })->all();
    }
}