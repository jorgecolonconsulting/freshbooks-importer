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
use _2UpMedia\FreshbooksImporter\Services\Freshbooks\ListProjects;
use Illuminate\Console\Command;

class ListProjectsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'freshbooks-importer:list-projects';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List Freshbooks Projects';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(FreshbooksClassic $freshbooksClassic, ListProjects $listProjects)
    {
        if (config('freshbooks-importer.freskbooks-version') === Service::VERSION_FRESHBOOKS) {
            $this->table($listProjects->headers(), $listProjects->rows());
        } else {
            $this->table($freshbooksClassic->listProjectsHeaders(), $freshbooksClassic->listProjects());
        }
    }
}
