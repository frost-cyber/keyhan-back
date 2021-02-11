<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model {
	
	use HasFactory;
	
	public function attributes () {
		return $this->belongsToMany( Attribute::class , 'product_attribute' );
	}
	
	public function variables () {
		return $this->belongsToMany( Attribute::class , 'product_variable' );
	}
	
	public function category(){
		return $this->morphToMany(Category::class , 'categorizable');
	}
}
