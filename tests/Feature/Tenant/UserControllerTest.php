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

        $response = $this->json('GET', "{$this->domain}/api/users");

        $response->dump();
    }
}
