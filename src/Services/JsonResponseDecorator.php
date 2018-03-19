<?php
/**
 * Created by PhpStorm.
 * User: x2UP_Media
 * Date: 3/18/18
 * Time: 11:21 PM
 */
namespace _2UpMedia\FreshbooksImporter\Services;


use Psr\Http\Message\ResponseInterface;

class JsonResponseDecorator
{
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function getBody()
    {
        return json_decode((string) $this->response->getBody());
    }

    public function __call($methodName, $args)
    {
        return call_user_func_array([$this->response, $methodName], $args);
    }
}