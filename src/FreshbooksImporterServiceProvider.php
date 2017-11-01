<?php
/**
 * Created by PhpStorm.
 * User: x2UP_Media
 * Date: 10/17/17
 * Time: 4:09 AM
 */

namespace _2UpMedia\FreshbooksImporter;

use _2UpMedia\FreshbooksImporter\Commands\ConfigurePackageCommand;
use _2UpMedia\FreshbooksImporter\Commands\ImportHarvestCommand;
use _2UpMedia\FreshbooksImporter\Commands\ListProjectsCommand;
use _2UpMedia\FreshbooksImporter\Commands\ListTasksCommand;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;

class FreshbooksImporterServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/freshbooks-importer.php' => config_path('freshbooks-importer.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ImportHarvestCommand::class,
                ListTasksCommand::class,
                ListProjectsCommand::class
            ]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/freshbooks-importer.php', 'freshbooks-importer');
    }
}
