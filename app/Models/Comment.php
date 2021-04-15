<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable=[
        'body',
        'name',
        'email',
        'user_id',
        'parent_id'
    ];
    use HasFactory;
    protected $with = ['commentable','user'];
    public function parent(){
        return $this->belongsTo(Comment::class , 'parent_id');
    }

    public function children(){
        return $this->hasMany(Comment::class , 'parent_id');
    }
    public function commentable(){
        return $this->morphTo();
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
