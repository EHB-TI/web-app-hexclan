<?php

namespace Tests\Feature\Tenant;

use App\Models\BankAccount;
use App\Models\Event;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TenantTestCase;

class EventControllerTest extends TenantTestCase
{
    public function getAbilities()
    {
        return [
            'admin' => ['admin'],
            'write' => ['write'],
            'self' => ['self']
        ];
    }

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

        if ($ability == 'admin' || $ability == 'write') {
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

    /**
     * @dataProvider getAbilities
     * @test
     */
    public function postEvent($ability)
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ["{$ability}"]
        );

        $tenant = tenant();

        $event = Event::factory()
            ->for(BankAccount::first(['id']))
            ->make();
        $domain = static::$domain;
        $response = $this->json('POST', "{$domain}/api/events", []);

        if ($ability == 'admin' || $ability == 'write') {
            $response->assertJson(
                fn (AssertableJson $json) =>
                $json->has(
                    'data.0',
                    fn ($json) =>
                    $json->hasAll('id', 'name', 'date')
                        ->etc()
                )
            )->assertCreated();
        }
    }
}
