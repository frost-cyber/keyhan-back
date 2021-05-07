<?php

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Address::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'mobile' => $this->faker->mobileNumber,
            'phone' => $this->faker->phoneNumber,
            'state' => $this->faker->cityPrefix(),
            'city' => $this->faker->city,
            'address' => $this->faker->building(),
            'postcode' => $this->faker->postcode,
            'description' => $this->faker->realText()
        ];
    }
}
