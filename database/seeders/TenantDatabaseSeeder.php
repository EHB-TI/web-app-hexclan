<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\Event;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $collection = Tenant::select('tenancy_admin_email')
            ->where('id', tenant('id'))
            ->get();
        $array = $collection->pluck('tenancy_admin_email');
        $adminEmail = $array[0];
        User::create([
            'id' => (string) Str::uuid(),
            'email' => $adminEmail,
            'ability' => '*'
        ]);

        $bankAccount = BankAccount::factory()->create();

        Event::factory(2)
            ->has(User::factory()->count(1))
            ->for($bankAccount)
            ->create();
    }
}
