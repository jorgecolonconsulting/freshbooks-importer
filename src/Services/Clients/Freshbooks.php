<?php
/**
 * Created by PhpStorm.
 * User: x2UP_Media
 * Date: 3/18/18
 * Time: 7:51 PM
 */
namespace _2UpMedia\FreshbooksImporter\Services\Clients;


use GuzzleHttp\Client;

class Freshbooks
{
    const BASE_API_URL = 'https://api.freshbooks.com';
    const OAUTH_TOKEN_URL = self::BASE_API_URL.'/auth/oauth/token';

    /**
     * @var Client
     */
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function get($uri, $options = [])
    {
        return $this->client->get($this->uri($uri), $options);
    }

    public function post($uri, $options = [])
    {
        return $this->client->post($this->uri($uri), $options);
    }

    public function put($uri, $options = [])
    {
        return $this->client->put($uri, $options);
    }

    public function delete($uri, $options = [])
    {
        return $this->client->delete($uri, $options);
    }

    private function uri($uri)
    {
        return self::BASE_API_URL.'/'.$uri;
    }

    public function __call($methodName, $args)
    {
        return call_user_func_array([$this->client, $methodName], $args);
    }
}