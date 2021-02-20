<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductVariable extends Pivot {
	
	protected $fillable = [
	 'purchase_price' ,
	 'selling_price' ,
	 'discounted_price' ,
	 'wholesale_price' ,
	 'minimum_wholesale' ,
	 'inventory' ,
	];
	
}
