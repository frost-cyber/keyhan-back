<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customization extends Model
{
    use HasFactory;
    protected $fillable=[
    	'name',
	    'contact',
	    'status',
	    'product_id',
	    'discription'
    ];
    public function product(){
    	return $this->belongsTo(Product::class);
    }
}
