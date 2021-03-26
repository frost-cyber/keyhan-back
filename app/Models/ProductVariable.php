<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariable extends Model {

	protected $fillable = [
    'variable_id',
    'variable_value_id',
	 'purchase_price' ,
	 'selling_price' ,
	 'discounted_price' ,
	 'wholesale_price' ,
	 'minimum_wholesale' ,
	 'inventory' ,
	];
    protected $table = 'product_variable';

     public $timestamps = false;
	public function variable(){
	    return $this->belongsTo(AttributeValue::class , 'variable_value_id');
    }
}
