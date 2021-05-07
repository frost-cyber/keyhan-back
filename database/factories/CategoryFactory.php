<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;
    protected array $categoryTypes = [Category::TYPE_STORE, Category::TYPE_BLOG];
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'slug' => $this->faker->unique()->word,
            'type' => $this->faker->randomElement($this->categoryTypes),
        ];
    }

    public function type(int $type)
    {
        in_array($type, $this->categoryTypes) ?: $type = Category::TYPE_STORE;
        return $this->state(function ($attributes) use ($type) {
            return ['type' => $type];
        });
    }

    public function typeStore()
    {
        return $this->type(Category::TYPE_STORE);
    }

    public function typeBlog()
    {
        return $this->type(Category::TYPE_BLOG);
    }
}
