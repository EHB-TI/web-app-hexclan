<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'id' => (string) Str::uuid(),
            'email' => 'mathieu.developer@protonmail.com',
            'ability' => '*'
        ]);
        Tenant::factory()->create()->domains()->create(['domain' => 'demo.hexclan.test']);
    }
}
