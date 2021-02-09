<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model {
	
	use HasFactory;
	
	const TYPE_STORE = 1;
	const TYPE_BLOG = 2;
	
	public $timestamps = FALSE;
	
}
