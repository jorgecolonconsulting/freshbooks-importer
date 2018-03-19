<?php
/**
 * Created by PhpStorm.
 * User: x2UP_Media
 * Date: 3/18/18
 * Time: 10:04 PM
 */

namespace _2UpMedia\FreshbooksImporter\Services\Clients;


use _2UpMedia\FreshbooksImporter\Services\Clients\Freshbooks\ClientFactory;

class FreshbooksFactory
{
    protected $clientFactory;

    public function __construct(ClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }

    public function build(string $refreshTokenValue = null)
    {
        return new Freshbooks($this->clientFactory->build($refreshTokenValue));
    }
}