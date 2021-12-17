<?php

namespace Tests;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TenantTestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $domainWithScheme;

    public function setUp(): void
    {
        parent::setUp();

        $tenant = Tenant::with('domains')->first();
        $this->domainWithScheme = 'https://' . $tenant->domains->first()->domain;

        tenancy()->initialize($tenant);
    }
}
