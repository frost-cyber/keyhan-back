<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class PostDefaultSeeder extends Seeder {

	public function run() {
		Post::query()->truncate();
		Post::create( [
			'name'   => 'پیشفرض',
			'weight' => [],
		] );
    }
}
