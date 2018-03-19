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

class ListAccounts
{
    private $clientFactory;

    public function __construct(FreshbooksFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }

    public function headers()
    {
        return ['Business Name', 'Business ID', 'Account ID'];
    }

    public function rows()
    {
        $client = $this->clientFactory->build();

        $response = new JsonResponseDecorator($client->get("auth/api/v1/users/me"));

        $body = $response->getBody();

        $businesses = $body->response->business_memberships;

        return collect($businesses)->map(function($membership) {
            return [$membership->business->name, $membership->business->id, $membership->business->account_id];
        })->all();
    }
}