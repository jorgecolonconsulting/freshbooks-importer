<?php
/**
 * Created by PhpStorm.
 * User: x2UP_Media
 * Date: 10/17/17
 * Time: 3:23 AM
 */

namespace _2UpMedia\FreshbooksImporter\Commands;

use _2UpMedia\FreshbooksImporter\Importer\FreshbooksClassic;
use _2UpMedia\FreshbooksImporter\Service;
use _2UpMedia\FreshbooksImporter\Services\Freshbooks\ListClients;
use Illuminate\Console\Command;

class ListClientsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'freshbooks-importer:list-clients';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List Freshbooks Clients';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(FreshbooksClassic $freshbooksClassic, ListClients $listClients)
    {
        if (config('freshbooks-importer.freskbooks-version') === Service::VERSION_FRESHBOOKS) {
            $this->table($listClients->headers(), $listClients->rows());
        } else {
            $this->table($freshbooksClassic->listProjectsHeaders(), $freshbooksClassic->listProjects());
        }
    }
}
