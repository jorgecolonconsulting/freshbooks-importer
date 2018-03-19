<?php
/**
 * Created by PhpStorm.
 * User: x2UP_Media
 * Date: 10/17/17
 * Time: 4:09 AM
 */

namespace _2UpMedia\FreshbooksImporter\Providers;

use _2UpMedia\FreshbooksImporter\Commands\ImportHarvestCommand;
use _2UpMedia\FreshbooksImporter\Commands\ListClientsCommand;
use _2UpMedia\FreshbooksImporter\Commands\ListProjectsCommand;
use _2UpMedia\FreshbooksImporter\Commands\ListAccountsCommand;
use _2UpMedia\FreshbooksImporter\Commands\ListTasksCommand;
use _2UpMedia\FreshbooksImporter\Services\Clients\Freshbooks;

use Illuminate\Support\ServiceProvider;

class FreshbooksImporterServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/freshbooks-importer.php' => config_path('freshbooks-importer.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ImportHarvestCommand::class,
                ListAccountsCommand::class,
                ListTasksCommand::class,
                ListProjectsCommand::class,
                ListClientsCommand::class,
            ]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/freshbooks-importer.php', 'freshbooks-importer');

        $this->app->bind(
            Freshbooks::class,
            function ($app) {
                $clientFactory = new Freshbooks\ClientFactory();

                return new Freshbooks($clientFactory->build());
            }
        );
    }
}
