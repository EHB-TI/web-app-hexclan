<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\Category;
use App\Models\Event;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds on a tenant.
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

        // To be commented out in production.
        $bankAccount = BankAccount::factory()->create();

        // To be commented out in production.
        Event::factory(2)
            ->for($bankAccount)
            ->has(User::factory()->count(1))
            ->has(Category::factory()->count(2))
            ->create();
    }
}
