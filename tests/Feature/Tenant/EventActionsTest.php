<?php

namespace Tests\Feature\Tenant;

use App\Models\BankAccount;
use App\Models\Event;
use App\Models\User;
use App\Http\Middleware\Authenticate;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Laravel\Sanctum\Sanctum;
use Tests\TenantTestCase;

class EventActionsTest extends TenantTestCase
{
    public function getAbilities()
    {
        return [
            'admin' => ['admin'],
            'write' => ['write'],
            'self' => ['self']
        ];
    }

    public function getData()
    {
        return [

            'invalid name - name not unique' => ['placeholder', '1997-07-10', 1],
            'invalid date - wrong type' => ['test_event_1', 'placeholder', 1],
            'invalid bank_account_id - does not exist' => ['test_event_1', '1997-07-10', 'placeholder']
        ];
    }

    /**
     * @test
     * @covers \App\Http\Controllers\EventController
     * @dataProvider getAbilities
     */
    public function getEvents_WhenAdminOrWrite_Returns200($ability)
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
            )->assertOk();
        } else if ($ability == 'self') {
            $response->assertForbidden();
        }
    }

    /**
     * @test
     * @covers \App\Http\Controllers\EventController
     * @dataProvider getAbilities
     */
    public function postEvent_WithPassingValidation_Returns201($ability)
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ["{$ability}"]
        );

        $event = Event::factory()
            ->for(BankAccount::first(['id']))
            ->make();

        DB::beginTransaction();
        $response = $this->postJson("{$this->domainWithScheme}/api/events", [
            'data' => [
                'name' => $event->name,
                'date' => $event->date,
                'bank_account_id' => $event->bank_account_id
            ]
        ]);
        DB::rollback();

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

    /**
     * @test
     * @covers \App\Http\Controllers\EventController
     * @dataProvider getData
     */
    public function postEvent_WithFailingValidation_Returns422($name, $date, $bank_account_id)
    {
        $this->withoutMiddleware([Authenticate::class, CheckForAnyAbility::class]);

        // Hack is required because dataproviders are run before setup.
        $args = collect(get_defined_vars());
        $arg = $args->search('placeholder');

        $invalidName = Event::first()->name;
        $invalidBankAccountId = 2;

        switch ($arg) {
            case 'name':
                $name = $invalidName;
                break;
            case 'date':
                $date = 'invalid date';
                break;
            case 'bank_account_id':
                $bank_account_id = $invalidBankAccountId;
                break;
            default:
                break;
        }

        $response = $this->json('POST', "{$this->domainWithScheme}/api/events", [
            'data' => [
                'name' => $name,
                'date' => $date,
                'bank_account_id' => $bank_account_id
            ]
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertDatabaseCount('events', 2); // Assumes that only 2 events are seeded during testing.
    }

    /**
     * @test
     * @covers \App\Http\Controllers\EventController
     */
    public function getEvent_Returns200()
    {
        $this->withoutMiddleware([Authenticate::class, CheckForAnyAbility::class]);

        $firstEvent = Event::first();
        $response = $this->json('GET', "{$this->domainWithScheme}/api/events/{$firstEvent->id}");

        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->has(
                'data',
                fn ($json) =>
                $json->where('id', $firstEvent->id)
                    ->where('name', $firstEvent->name)
                    ->where('date', $firstEvent->date)
                    ->where('bank_account_id', $firstEvent->bank_account_id)
                    ->etc()
            )
        )->assertOk();
    }

    /**
     * bank_account_id unchanged.
     * @test
     * @covers \App\Http\Controllers\EventController
     */
    public function patchEvent_WithPassingValidation_Returns200()
    {
        $this->withoutMiddleware([Authenticate::class, CheckForAnyAbility::class]);

        $firstEvent = Event::first();
        $tenantName = tenant('name');
        $updatedName = "{$tenantName}_event_3";
        $updatedDate = Carbon::now()->toDateString();
        DB::beginTransaction();
        $response = $this->patchJson("{$this->domainWithScheme}/api/events/{$firstEvent->id}", [
            'data' => [
                'name' => $updatedName,
                'date' => $updatedDate,
                'bank_account_id' => $firstEvent->bank_account_id
            ]
        ]);
        DB::rollback();

        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->has(
                'data',
                fn ($json) =>
                $json->where('id', $firstEvent->id)
                    ->where('name', $updatedName)
                    ->where('date', $updatedDate)
                    ->where('bank_account_id', $firstEvent->bank_account_id)
                    ->etc()
            )
        )->assertOk();
    }

    /**
     * @test
     * @covers \App\Http\Controllers\EventController
     * @dataProvider getAbilities
     */
    public function deleteEvent_WhenAdmin_Returns204($ability)
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ["{$ability}"]
        );

        $firstEvent = Event::first();
        DB::beginTransaction();
        $response = $this->deleteJson("{$this->domainWithScheme}/api/events/{$firstEvent->id}");
        DB::rollback();

        if ($ability == 'admin') {
            $response->assertNoContent();
        } else if ($ability == 'write') {
            $response->assertForbidden();
        } else if ($ability == 'self') {
            $response->assertForbidden();
        }
    }
}
