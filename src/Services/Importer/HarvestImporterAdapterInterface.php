<?php
/**
 * Created by PhpStorm.
 * User: x2UP_Media
 * Date: 3/15/18
 * Time: 9:44 PM
 */

namespace _2UpMedia\FreshbooksImporter\Importer;


use Illuminate\Console\Command;

interface HarvestImporterAdapterInterface
{
    public function consume(string $csvPath, Command $console);
}
