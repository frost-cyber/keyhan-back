<?php

namespace Database\Factories;

use App\Models\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttributeFactory extends Factory {
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Attribute::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() {
        return [
            'name'        => $this->faker->word,
            'type'        => 1,
            'is_variable' => FALSE,
            'value'       => $this->faker->word,
        ];
    }

    public function variable() {
        return $this->state( [ 'is_variable' => TRUE ] );
    }

    public function typeColor() {
        return $this->state( [
            'name'       => 'رنگ',
            'type'       => 2,
            'value'      => $this->faker->safeColorName,
            'extra_data' => [
                'code' => $this->faker->hexColor,
            ],
        ] );
    }

    public function typeUnit() {
        return $this->state( [
            'name'       => 'اندازه',
            'type'       => 2,
            'value'      => $this->faker->numberBetween(10 , 100),
            'extra_data' => [
                'unit' => 'سانتی متر',
            ],
        ] );
    }
}
