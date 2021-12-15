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

        $response = $this->json('GET', "{$this->domainWithScheme}/api/events");

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

        $event = Event::factory()
            ->for(BankAccount::first(['id']))
            ->make();

        $response = $this->json('POST', "{$this->domainWithScheme}/api/events", [
            'data' => [
                'name' => $event->name,
                'date' => $event->date,
                'bank_account_id' => $event->bank_account_id
            ]
        ]);

        if ($ability == 'admin' || $ability == 'write') {
            $response->assertJson(
                fn (AssertableJson $json) =>
                $json->has(
                    'data',
                    fn ($json) =>
                    $json->hasAll('id', 'name', 'date', 'bank_account_id')
                        ->etc()
                )
            )->assertCreated();
        } else if ($ability == 'self') {
            $response->assertForbidden();
        }
    }
}
