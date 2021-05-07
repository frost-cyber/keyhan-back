<?php

namespace App\Models;

use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
    use HasFactory, Notifiable, HasApiTokens, MustVerifyEmail;

    const RELATIONS = [
        'productsWishlist',
        'carts',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'avatar',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at'  => 'datetime',
        'mobile_verified_at' => 'datetime',
    ];

    public static function ALL_RELATIONS() {
        $relations = static::RELATIONS;

        foreach ( Product::ALL_RELATIONS() as $item ) {
            $relations[] = 'productsWishlist.' . $item;
        }

        foreach ( Cart::ALL_RELATIONS() as $item ) {
            $relations[] = 'carts.' . $item;
        }

        return $relations;
    }

    public function productsWishlist() {
        return $this->morphedByMany( Product::class, 'wishable', 'wishlist' );
    }

    public function carts(){
        return $this->hasMany(Cart::class);
    }

    public function currentCart(): Cart{
        return $this->carts()->where('status', 0)->firstOrNew();
    }
}
