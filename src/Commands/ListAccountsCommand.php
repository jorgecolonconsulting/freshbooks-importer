<?php
/**
 * Created by PhpStorm.
 * User: x2UP_Media
 * Date: 10/17/17
 * Time: 3:23 AM
 */

namespace _2UpMedia\FreshbooksImporter\Commands;

use _2UpMedia\FreshbooksImporter\Services\Freshbooks\ListAccounts;
use Illuminate\Console\Command;

class ListAccountsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'freshbooks-importer:list-accounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List Freshbooks Accounts';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(ListAccounts $listAccounts)
    {
        $this->table($listAccounts->headers(), $listAccounts->rows());
    }
}
