<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model {
    use HasFactory;

    protected $fillable = [ 'name','email','last_name', 'mobile', 'phone', 'state', 'city', 'address', 'postcode', 'description' ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function carts(){
        return $this->hasMany(Cart::class);
    }

}
