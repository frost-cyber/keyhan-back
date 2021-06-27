<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['status' , 'order_number'];

    use HasFactory;

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function shipments(){
        return $this->hasMany(Shipment::class);
    }

    public function payments(){
        return $this->hasMany(Payment::class);
    }

    public function productVariants(){
        return $this->belongsToMany(ProductVariant::class , 'product_order' )
            ->withPivot(['purchase_price','price','price_type','quantity']);
    }
}
