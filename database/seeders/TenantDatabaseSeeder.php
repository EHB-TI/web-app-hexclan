<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\Category;
use App\Models\Event;
use App\Models\Item;
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

        // Everything hereunder to be commented out in production.
        $bankAccount = BankAccount::factory()->create();

        $events = Event::factory(2)
            ->for($bankAccount)
            ->has(User::factory()->count(1))
            ->create();

        $categories = collect();
        foreach ($events as $event) {
            $categories->push(Category::factory(2)
                ->for($event)
                ->create());
        }
        $flattenedCategories = $categories->flatten();

        foreach ($flattenedCategories as $category) {
            Item::factory(5)
                ->for($category)
                ->create();
        }
    }
}
