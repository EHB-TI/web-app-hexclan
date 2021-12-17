<?php

namespace Tests;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TenantTestCase extends BaseTestCase
{
    use CreatesApplication;

    protected static $setUpHasRunOnce = false;
    protected $domainWithScheme;

    public function setUp(): void
    {
        parent::setUp();
        if (!static::$setUpHasRunOnce) {
            $this->artisan('custom:drop');
            DB::statement('CREATE DATABASE hexclan_test');
            config(['database.connections.mysql.database' => 'hexclan_test']);
            DB::connection('mysql')->setDatabaseName('hexclan_test');
            DB::reconnect();
            $this->artisan('migrate:fresh');

            $tenant = Tenant::factory()->create();
            $domain = strtolower($tenant->name) . '.' . config('tenancy.central_domains.0');
            $persistedDomain = $tenant->domains()->create(['domain' => $domain])->domain;
            $this->domainWithScheme = 'https://' . $persistedDomain;
            $this->artisan('tenants:seed');

            tenancy()->initialize($tenant);

            static::$setUpHasRunOnce = true;
        } else {
            $tenant = Tenant::with('domains')->first();
            $this->domainWithScheme = 'https://' . $tenant->domains->first()->domain;

            tenancy()->initialize($tenant);
        }
    }
}
