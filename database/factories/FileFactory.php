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
        return $this->createFile(storage_path('app/public'));
    }

    private function createFile($path, $width = 640, $height = 480)
    {
        $file = $this->faker->image($path, $width, $height);
        $path = storage_path('app\public') . '\\' . \File::basename($file);
        return [
            'path'      => $path,
            'name'      => \File::basename($file),
            'extension' => \File::extension($file),
            'link'      => \Storage::url(\File::basename($file)),
            'type'      => $this->faker->randomElement([1, 2, 3, 4]),
        ];
    }

    public function changeSize($width , $height , $path = null){
        $path = $path?:storage_path('app/public');
        return $this->state($this->createFile($path , $width, $height));
    }
}
