<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $collection = DB::connection(config('tenancy.database.central_connection'))
        ->table('tenants')
        ->select('id')
        ->where('id', tenant('id'))
        ->get();

        $array = $collection->pluck('id');
        $id = $array[0];
        return [
            'event_id' => $id,
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween($min = 5, $max = 40),
        ];
    }
}
