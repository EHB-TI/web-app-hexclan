<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\Category;
use App\Models\Event;
use App\Models\Item;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
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
        $resultSet = Tenant::select('name', 'tenancy_admin_email')
            ->where('id', tenant('id'))
            ->get();
        $resultSetObject = $resultSet->first();
        $tenantName = $resultSetObject->name;
        $adminEmail = $resultSetObject->tenancy_admin_email;

        User::create([
            'id' => (string) Str::uuid(),
            'email' => $adminEmail,
            'ability' => 'admin'
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

        $items = collect();
        foreach ($flattenedCategories as $category) {
            $items->push(Item::factory(5)
                ->for($category)
                ->create());
        }

        $flattenedItems = $items->flatten();

        $users = User::where('email', '!=', 'admin@' . "{$tenantName}" . '.' . config('tenancy.central_domains.0'))->get();
        foreach ($users as $user) {
            $randomItems = $flattenedItems->random(5);

            Transaction::factory(2)
                ->for($user)
                ->for($user->events->random())
                ->hasAttached($randomItems, ['quantity' => rand(1, 10), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()])
                ->create();
        }
    }
}
