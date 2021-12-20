<?php

namespace Tests\Feature\Tenant;

use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TenantTestCase;

class UserActionsTest extends TenantTestCase
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
     * @test
     * @covers \App\Http\Controllers\UserController
     * @dataProvider getAbilities
     */
    public function getUsers_WhenAdminOrWrite_Returns200($ability)
    {
        Sanctum::actingAs(
            User::factory()->makeOne(),
            ["{$ability}"]
        );

        $response = $this->json('GET', "{$this->domainWithScheme}/api/users");

        if ($ability == 'admin' || $ability == 'write') {
            $response->assertJson(
                fn (AssertableJson $json) =>
                $json->has(
                    'data.0',
                    fn ($json) =>
                    $json->hasAll('id', 'name', 'email')
                        ->etc()
                )
            )->assertOk();
        } else if ($ability == 'self') {
            $response->assertForbidden();
        }
    }
}
