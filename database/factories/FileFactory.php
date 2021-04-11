<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\File;

class FileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = File::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $file = $this->faker->image(storage_path('app\public') , 640 , 480);
        $path = storage_path('app\public') . '\\' . \File::basename($file);
        return [
            'path' => $path,
            'name' => \File::basename($file),
            'extension' => \File::extension($file),
            'link' => \Storage::url(\File::basename($file)),
            'type' => $this->faker->randomElement([1,2,3,4])
        ];
    }
}
