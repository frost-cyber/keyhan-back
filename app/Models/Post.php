<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable=[
    	'name',
	    'states',
	    'weight',
	    'is_free',
    ];
    protected $casts=['weight'=>'json','states'=>'json'];
}
