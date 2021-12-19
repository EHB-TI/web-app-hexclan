<?php

require_once dirname(__DIR__, 1) . '/vendor/composer/autoload_real.php';

use App\Models\Tenant;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\CreatesApplication;

(new class()
{
    use CreatesApplication;
})->createApplication();

Artisan::call('custom:drop'); // This call is required because app erroneously determines that db hexclan_test already exists.
DB::statement('CREATE DATABASE hexclan_test');
config(['database.connections.mysql.database' => 'hexclan_test']); // This call + 2 following are required because .env.testing seems to be ignored. This issue is linked to config caching.
DB::connection('mysql')->setDatabaseName('hexclan_test');
DB::reconnect();
Artisan::call('migrate:fresh --seed');
Artisan::call('tenants:seed');
$tenant = Tenant::with('domains')->first();
