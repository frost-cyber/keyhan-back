<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{

    public $timestamps = FALSE;
    protected $fillable = [
        'attribute_id' ,
        'purchase_price' ,
        'selling_price' ,
        'discounted_price' ,
        'wholesale_price' ,
        'minimum_wholesale' ,
        'inventory' ,
    ];


    const RELATIONS = [
        'attribute',
        'product',
        'files',
    ];

    public static function ALL_RELATIONS() {
        $relations = static::RELATIONS;

        foreach ( Attribute::ALL_RELATIONS() as $item ) {
            $relations[] = 'attribute.' . $item;
        }

        return $relations;
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class , 'attribute_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function files()
    {
        return $this->morphToMany(File::class , 'fileable' , 'fileables');
    }

}
