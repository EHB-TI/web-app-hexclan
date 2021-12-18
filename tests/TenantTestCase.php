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

    // Preferable to initialize tenant here rather than in bootstrap or extension because abstract class is different from central app tests.
    public function setUp(): void
    {
        parent::setUp();

        $this->domainWithScheme = 'https://' . $GLOBALS['domain'];

        tenancy()->initialize($GLOBALS['tenant']);
    }
}
