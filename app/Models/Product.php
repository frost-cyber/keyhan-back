<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model {

	use HasFactory , SoftDeletes;

	protected $fillable = [
	 'name' ,
	 'slug' ,
	 'sku' ,
	 'short_review' ,
	 'description' ,
	 'review' ,
	 'is_virtual' ,
	];

	public function attributes () {
		$pivots = [ 'group_name' , 'number' ];

		$args = [ 'product_attribute' , 'product_id' , 'attribute_value_id' ];

		return $this->belongsToMany( AttributeValue::class , ...$args )->using( ProductAttribute::class )->withPivot( $pivots );
	}

	public function variables () {
		return $this->hasMany( ProductVariable::class );
	}

	public function categories () {
		return $this->morphToMany( Category::class , 'categorizable' );
	}

	public function brand () {
		return $this->belongsTo( Brand::class );
	}

}
