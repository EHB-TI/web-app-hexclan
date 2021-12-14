<?php

namespace Tests\Feature\Tenant;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TenantTestCase;

class UserControllerTest extends TenantTestCase
{
    /**
     * @test
     */
    public function getEvents()
    {

        $domain = static::$domain;
        $response = $this->json('GET', "{$domain}/api/users");

        //$response->dump();
    }
}
