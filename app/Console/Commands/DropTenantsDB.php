<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class DropTenantsDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:drop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop all databases for tenant(s)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (App::environment() == 'local') {
            $dbs = DB::select('SHOW DATABASES LIKE "tenant_%_local"');
            foreach ($dbs as $db) {
                $db = array_values((array) $db)[0];

                DB::select("DROP DATABASE `$db`");
            }

            $this->info('Dropped tenant(s) database(s) succesfully'); // TODO return different message if no dbs to drop.
        } else if (App::environment() == 'testing') {
            foreach (DB::select('SHOW DATABASES LIKE "tenant_%_test"') as $db) {
                $db = array_values((array) $db)[0];

                DB::select("DROP DATABASE `$db`");
            }

            $this->info('Dropped tenant(s) database(s) succesfully');
        }
    }
}
