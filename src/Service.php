<?php
/**
 * Created by PhpStorm.
 * User: x2UP_Media
 * Date: 10/17/17
 * Time: 4:27 AM
 */

namespace _2UpMedia\FreshbooksImporter;

use _2UpMedia\FreshbooksImporter\Commands\ConfigurePackageCommand;

class Service
{
    private $configureCommand;

    public function __construct(ConfigurePackageCommand $command)
    {
        $this->configureCommand = $command;
    }

    public function checkInstall()
    {

    }
}
