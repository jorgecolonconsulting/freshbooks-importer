<?php
/**
 * Created by PhpStorm.
 * User: x2UP_Media
 * Date: 10/17/17
 * Time: 3:23 AM
 */

namespace _2UpMedia\FreshbooksImporter\Commands;

use _2UpMedia\FreshbooksImporter\Importer\FreshbooksClassic;
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
    public function handle(FreshbooksClassic $freshbooksClassic)
    {
        $csvPath = $this->argument('csvPath');

        $freshbooksClassic->consume($csvPath, $this);

        $this->comment("Import done");
    }
}
