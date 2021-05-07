<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'name',
        'email',
        'body',
        'confirmed',
        'user_id',
        'parent_id',
    ];
    use HasFactory;
    const RELATIONS = [
        'children' ,
        'childrenRecursive' ,
        'parent' ,
        'parentRecursive' ,
        'user'
    ];

	public static function ALL_RELATIONS() {
        $relations = static::RELATIONS;

        foreach ( User::ALL_RELATIONS() as $item ) {
            $relations[] = 'user.' . $item;
        }

        foreach ( Product::ALL_RELATIONS() as $item ) {
            $relations[] = 'product.' . $item;
        }

        foreach ( Article::ALL_RELATIONS() as $item ) {
            $relations[] = 'article.' . $item;
        }

        return $relations;
	}

	public function parent(){
        return $this->belongsTo(Comment::class , 'parent_id');
    }

    public function children(){
        return $this->hasMany(Comment::class , 'parent_id');
    }

    public function childrenRecursive(){
        return $this->children()->with('childrenRecursive', );
    }

    public function parentRecursive(){
        return $this->children()->with('parentRecursive', );
    }
    public function commentable(){
        return $this->morphTo();
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
