<?php

namespace Tests;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TenantTestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseTransactions;

    protected static $setUpHasRunOnce = false;
    protected $domainWithScheme;

    public function setUp(): void
    {
        parent::setUp();
        if (!static::$setUpHasRunOnce) {
            $this->artisan('custom:drop');
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
