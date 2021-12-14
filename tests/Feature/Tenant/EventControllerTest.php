<?php

namespace Tests\Feature\Tenant;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TenantTestCase;

class EventControllerTest extends TenantTestCase
{
    /**
     * @dataProvider getAbilities
     * @test
     */
    public function getEvents($ability)
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ["{$ability}"]
        );

        $response = $this->json('GET', "{$this->domain}/api/events");

        if ($ability == 'admin') {
            $response->assertOk();
        } else if ($ability == 'write') {
            $response->assertOk();
        }
    }

    public function getAbilities()
    {
        return [
            'admin' => ['admin'],
            'write' => ['write'],
            //'self' => ['self'/*, $expected*/]
        ];
    }
}
