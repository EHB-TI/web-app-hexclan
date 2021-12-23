<?php

namespace Tests\Feature\Tenant;

use App\Models\BankAccount;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TenantTestCase;

class BankAccountActionsTest extends TenantTestCase
{
    public function getAbilities()
    {
        return [
            'admin' => ['admin'],
            'manager' => ['manager'],
            'seller' => ['seller']
        ];
    }

    /**
     * @test
     * @covers \App\Http\Controllers\EventController
     * @dataProvider getAbilities
     */
    public function getEvent_WhenAdminOrWrite_Returns200($ability)
    {
        Sanctum::actingAs(
            User::inRandomOrder()->first(),
            ["{$ability}"]
        );
        $this->withoutMiddleware([RestrictToAccountableUser::class]);

        $bankAccount = BankAccount::inRandomOrder()->first();

        $response = $this->json('GET', "{$this->domainWithScheme}/api/bankaccounts/{$bankAccount->id}");

        if ($ability == 'admin' || $ability == 'manager') {
            $response->assertJson(
                fn (AssertableJson $json) =>
                $json->has(
                    'data',
                    fn ($json) =>
                    $json->hasAll('id', 'beneficiary_name', 'bic', 'iban')
                        ->etc()
                )
            )->assertOk();
        } else if ($ability == 'seller') {
            $response->assertForbidden();
        }
    }
}
