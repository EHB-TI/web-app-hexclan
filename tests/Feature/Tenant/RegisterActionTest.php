<?php

namespace Tests\Feature\Tenant;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TenantTestCase;

class RegisterActionTest extends TenantTestCase
{
    use WithFaker;
    /**
     * @test
     */
    public function register_WithValidInput_Returns200()
    {
        $user = User::first();

        $response = $this->postJson("{$this->domainWithScheme}/api/register", [
            'data' => [
                'name' => $this->faker->name(),
                'email' => $user->email,
                'password' => Hash::make('password')
            ]
        ]);
    }
}
