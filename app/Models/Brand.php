<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model {

	use HasFactory , SoftDeletes;

    protected $appends = ['logo'];

	protected $fillable = [
	 'name' ,
	 'slug',
	];

	public function products(){
		return $this->hasMany(Product::class);
	}

	public function files(){
	    return $this->morphToMany(File::class , 'fileable')->withPivot(['default' , 'number' , 'description']);
    }

    public function getLogoAttribute(){
	    return $this->files()->wherePivot('default' , TRUE)->first();
    }

    public function setLogoAttribute($value){
	    $this->files()->sync([
            $value['id'] => ['default' => true]
            ]);
    }
}
