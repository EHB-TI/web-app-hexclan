<?php

namespace Tests\Feature\Tenant;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Tests\TenantTestCase;

class RegisterActionTest extends TenantTestCase
{
    use WithFaker;
    /**
     * @test
     */
    public function register_WithValidInput_Returns200()
    {
        $user = User::inRandomOrder()->first();
        Event::fake();
        DB::beginTransaction();
        $response = $this->postJson("{$this->domainWithScheme}/api/register", [
            'data' => [
                'name' => $this->faker->name(),
                'email' => $user->email,
                'password' => 'password'
            ]
        ]);
        DB::commit();
        $user = User::find($user->id); // Necessary to retrieve the model again after transaction is committed.

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        Event::assertDispatched(Registered::class);

        $this->assertTrue(isset($user->pin_code));
        $this->assertTrue(isset($user->pin_code_timestamp));
        DB::rollBack();
    }
}
