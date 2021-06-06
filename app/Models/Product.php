<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class Product extends Model {

    use HasFactory, SoftDeletes;

    const PRODUCT_TYPE_SIMPLE = 1;

    const PRODUCT_TYPE_VARIANT = 2;

    const PRODUCT_TYPE_VIRTUAL = 3;

    protected $fillable = [ 'name', 'slug', 'sku', 'short_review', 'description', 'review', 'is_virtual', ];

    const RELATIONS = [ 'attributes', 'variants', 'categories', 'comments', 'brand', 'files', 'links', ];

    protected $casts = [ 'published_at' => 'datetime' ];

    public function comments() {
        return $this->morphMany( Comment::class, 'commentable' );
    }

    public function attributes() {
        $pivots = [ 'group_name', 'number' ];

        return $this->belongsToMany( Attribute::class, 'product_attribute' )->withPivot( $pivots );
    }

    public function variants() {
        return $this->hasMany( ProductVariant::class );
    }

    public function categories() {
        return $this->morphToMany( Category::class, 'categorizable' );
    }

    public function brand() {
        return $this->belongsTo( Brand::class );
    }

    public function files() {
        return $this->morphToMany( File::class, 'fileable', 'fileables' )->withPivot(['default']);
    }

    public function links() {
        return $this->hasMany( ProductLink::class );
    }

    public static function ALL_RELATIONS(): array {

        $relations = static::RELATIONS;

        foreach ( Category::ALL_RELATIONS() as $item ) {
            $relations[] = 'categories.' . $item;
        }

        foreach ( Comment::ALL_RELATIONS() as $item ) {
            $relations[] = 'comments.' . $item;
        }

        foreach ( ProductVariant::ALL_RELATIONS() as $item ) {
            $relations[] = 'variants.' . $item;
        }

        return $relations;
    }

    /**
     * Scope a query to only include active users.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  bool                                $desc
     *
     * @return \Illuminate\Database\Query\Builder
     */

    public function scopeOrderByPrice( Builder $query, bool $desc = FALSE ): Builder {
        return $query->orderBy(
            ProductVariant::select( 'selling_price' )
                          ->where( 'inventory', '<=>', 0 )
                          ->whereColumn( 'product_id', 'product.id' )
                          ->orderBy( 'selling_price', $desc ? 'asc' : 'desc' )
                          ->limit( 1 )
            , $desc ? 'asc' : 'desc' );
    }

    /**
     * Scope a query to only include active users.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  bool                                $desc
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeOrderByPopularity( Builder $query, bool $desc = FALSE ): Builder {
        return $query->orderBy(
            DB::table( 'wishlist' )
              ->where( 'wishable_type', 'App\Models\Product' )
              ->whereColumn( 'wishable_id', 'product.id' )
              ->count( 'wishable_id' )
            , $desc ? 'asc' : 'desc' );
    }
}
