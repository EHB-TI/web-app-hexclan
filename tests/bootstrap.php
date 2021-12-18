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

Artisan::call('custom:drop');
DB::statement('CREATE DATABASE hexclan_test');
config(['database.connections.mysql.database' => 'hexclan_test']);
DB::connection('mysql')->setDatabaseName('hexclan_test');
DB::reconnect();
Artisan::call('migrate:fresh');

$tenant = Tenant::factory()->create();
$domain = strtolower($tenant->name) . '.' . config('tenancy.central_domains.0');
$tenant->domains()->create(['domain' => $domain])->domain;
Artisan::call('tenants:seed');
