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
	const RELATIONS = [
	    'attributes',
        'variants',
        'categories',
        'brand',
        'files'
    ];

	protected $casts = [
	    'published_at' => 'datetime'
    ];

	public function attributes () {
		$pivots = [ 'group_name' , 'number' ];

		return $this->belongsToMany( Attribute::class , 'product_attribute')->withPivot( $pivots );
	}
	public function variants () {
		return $this->hasMany( ProductVariant::class );
	}

	public function categories () {
		return $this->morphToMany( Category::class , 'categorizable' );
	}

	public function brand () {
		return $this->belongsTo( Brand::class );
	}

	public function files() {
	    return $this->morphToMany( File::class , 'fileable' , 'fileables');
    }

}
