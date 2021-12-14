<?php

namespace Tests\Feature\Tenant;

use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
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

        $domain = static::$domain;
        $response = $this->json('GET', "{$domain}/api/events");

        if ($ability == 'admin') {
            $response->assertJson(
                fn (AssertableJson $json) =>
                $json->has(
                    'data.0',
                    fn ($json) =>
                    $json->hasAll('id', 'name', 'date')
                        ->etc()
                )
            );
        } else if ($ability == 'write') {
            $response->assertJson(
                fn (AssertableJson $json) =>
                $json->has(
                    'data.0',
                    fn ($json) =>
                    $json->hasAll('id', 'name', 'date')
                        ->etc()
                )
            );
        } else if ($ability == 'self') {
            $response->assertForbidden();
        }
    }

    public function getAbilities()
    {
        return [
            'admin' => ['admin'],
            'write' => ['write'],
            'self' => ['self'/*, $expected*/]
        ];
    }
}
