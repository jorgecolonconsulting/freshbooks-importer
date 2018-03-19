<?php
namespace _2UpMedia\FreshbooksImporter\Services\Clients\Freshbooks;

class RefreshTokenRequiredException extends \Exception {
    public function __construct()
    {
        parent::__construct('Existing token was not persisted and refresh token value was not supplied.');
    }
}