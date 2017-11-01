<?php
/**
 * Created by PhpStorm.
 * User: x2UP_Media
 * Date: 10/17/17
 * Time: 4:32 AM
 */

namespace _2UpMedia\FreshbooksImporter\Commands;

use Illuminate\Console\Command;

class ConfigurePackageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'freshbooks-importer:configure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configure Freshbooks Importer';

    public function handle()
    {
        if (! config('freshbooks-importer.configured')) {
            $subDomain = $this->ask('What is your Freshbooks sub-domain');
            $secret = $this->ask('What is your Freshbooks API secret');

            config(['freshbooks-importer.freshbooks-classic.authentication-token' => $secret]);
            config(['freshbooks-importer.freshbooks-classic.subdomain' => $subDomain]);

            config('freshbooks-importer.configured', true);
        };
    }
}