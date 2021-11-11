<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->company();
        return [
            'name' => $name,
            'date' => $this->faker->date('Y-m-d'),
            'beneficiary_name' => $name,
            'bic' => $this->faker->swiftBicNumber(),
            'iban' => $this->faker->iban('BE', '', 16)
        ];
    }
}
