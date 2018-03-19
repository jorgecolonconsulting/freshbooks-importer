<?php
/**
 * Created by PhpStorm.
 * User: x2UP_Media
 * Date: 10/17/17
 * Time: 3:23 AM
 */

namespace _2UpMedia\FreshbooksImporter\Commands;

use _2UpMedia\FreshbooksImporter\Importer\Freshbooks;
use _2UpMedia\FreshbooksImporter\Importer\FreshbooksClassic;
use _2UpMedia\FreshbooksImporter\Service;
use Illuminate\Console\Command;

class ImportHarvestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'freshbooks-importer:import-harvest {csvPath}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push CSV Harvest Time Entries to Freshbooks';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(FreshbooksClassic $freshbooksClassic, Freshbooks $freshbooks)
    {
        $csvPath = $this->argument('csvPath');

        if (config('freshbooks-importer.freskbooks-version') === Service::VERSION_FRESHBOOKS) {
            $freshbooks->consume($csvPath, $this);
        } else {
            $freshbooksClassic->consume($csvPath, $this);
        }

        $this->comment("Import done");
    }
}
