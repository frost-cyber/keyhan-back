<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    use HasFactory;

    const TYPE_STORE = 1;
    const TYPE_BLOG = 2;
    const RELATIONS = [
        'children' ,
        'childrenRecursive' ,
        'parent' ,
        'parentRecursive' ,
    ];
    public $timestamps = FALSE;

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive' ,);
    }

    public function children()
    {
        return $this->hasMany(Category::class , 'parent_id');
    }

    public function parentRecursive()
    {
        return $this->parent()->with('parentRecursive' ,);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class , 'parent_id');
    }
}
