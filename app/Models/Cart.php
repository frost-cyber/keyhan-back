<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    const RELATIONS = [
        'products',
        'productVariants',
    ];

    public static function ALL_RELATIONS() {
        $relations = static::RELATIONS;

        foreach ( Product::ALL_RELATIONS() as $item ) {
            $relations[] = 'products.' . $item;
        }

        foreach ( ProductVariant::ALL_RELATIONS() as $item ) {
            $relations[] = 'productVariants.' . $item;
        }

        return $relations;
    }

    public function products(){
        return $this->belongsToMany(Product::class);
    }
    public function productVariants(){
        return $this->belongsToMany(ProductVariant::class,'cart_products')->withPivot('quantity');
    }
}
