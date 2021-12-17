<?php

declare(strict_types=1);

namespace Tests\Extensions;

use App\Models\Tenant;
use PHPUnit\Runner\AfterLastTestHook;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use PHPUnit\Runner\BeforeFirstTestHook;
use Tests\CreatesApplication;

final class RunnerExtension implements BeforeFirstTestHook, AfterLastTestHook
{
    use CreatesApplication;

    public function executeBeforeFirstTest(): void
    {
        // phpunit --testsuite Unit
        //echo sprintf("testsuite: %s\n", $this->getPhpUnitParam("testsuite"));

        // phpunit --filter CreateCompanyTest
        //echo sprintf("filter: %s\n", $this->getPhpUnitParam("filter"));

        $this->createApplication();
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
    }

    public function executeAfterLastTest(): void
    {
        $this->createApplication();
        Artisan::call('custom:drop');
    }

    /**
     * @return string|null
     */
    protected function getPhpUnitParam(string $paramName): ?string
    {
        global $argv;
        $k = array_search("--$paramName", $argv);
        if (!$k) return null;
        return $argv[$k + 1];
    }
}
