<?php

namespace Tests;

use App\Models\Tenant;
use Database\Seeders\TenantDatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TenantTestCase extends BaseTestCase
{
    use CreatesApplication/*, DatabaseTransactions*/;

    protected static $setUpHasRunOnce = false;
    protected static $domain;

    public function setUp(): void
    {
        parent::setUp();
        if (!static::$setUpHasRunOnce) {
            $this->artisan('custom:drop');
            $this->artisan('migrate:fresh');
            $this->initializeTenancy(); // TODO
            $this->seed(TenantDatabaseSeeder::class);
            static::$setUpHasRunOnce = true;
        }
    }

    public function initializeTenancy()
    {
        $tenant = Tenant::factory()->create();
        static::$domain = strtolower($tenant->name) . '.' . config('tenancy.central_domains.0');
        $tenant->domains()->create(['domain' => static::$domain]);
        static::$domain = 'https://' . static::$domain;
        tenancy()->initialize($tenant);
    }
}
