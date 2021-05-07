<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    const RELATIONS = [
        'categories',
        'files',
        'comments',
    ];

    protected $appends=[ 'thumbnail'];
    protected $fillable=[
        'title',
        'body',
        'slug',
        'image_id',
        'tags',
        'comments_count',
        'description',
        'visit_count',
        'publish_at',
        'category_id',
        'condition'
    ];
    protected $casts=['tags'=>'array'];
    protected $table = 'articles';

	public static function ALL_RELATIONS() {
	    $relations = static::RELATIONS;

        foreach ( Category::ALL_RELATIONS() as $item ) {
            $relations[] = 'categories.' . $item;
        }

        foreach ( Comment::ALL_RELATIONS() as $item ) {
            $relations[] = 'comments.' . $item;
        }

        return $relations;
	}

	public function getThumbnailAttribute(){
        return $this->files[0]??[];
    }

    public  function files(){
        return $this->morphToMany(File::class,'fileable' , 'fileables')->withPivot(['default' , 'description' , 'number']);
    }
    public function categories(){
        return $this->morphToMany(Category::class,'categorizable' , 'categorizables');
    }
    public function comments(){
        return $this->morphMany(Comment::class,'commentable');
    }
}
