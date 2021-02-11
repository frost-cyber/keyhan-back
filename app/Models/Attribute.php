<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model {
	
	use HasFactory;
	
	public $timestamps = FALSE;
	protected $appends = [
	 'unit' ,
	];
	
	protected $hidden = [
	 'extra_data' ,
	];
	
	protected $casts = [
	 'extra_data' => 'array' ,
	];
	const TYPE_SIMPLE = 1;
	const TYPE_COLOR = 2;
	const TYPE_UNIT = 3;
	
	
	public function values () {
		return $this->hasMany( AttributeValue::class );
	}
	
	protected function ExtraDataSet ( $key , $val ) {
		if ( ! $this->extra_data ) {
			$this->extra_data = [];
		}
		
		$data = $this->extra_data;
		$data[ $key ] = $val;
		
		$this->extra_data = $data;
	}
	
	protected function ExtraDataGet ( $key ) {
		return $this->extra_data[ $key ]??NULL;
	}
	protected function getUnitAttribute () {
		return $this->ExtraDataGet( 'unit' );
	}
	
	protected function setUnitAttribute ( $val ) {
		$this->ExtraDataSet( 'unit' , $val );
	}
}
