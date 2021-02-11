<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model {
	
	use HasFactory;
	
	public $timestamps = FALSE;
	
	protected $appends = [
	 'code' ,
	];
	
	protected $hidden = [
	 'extra_data' ,
	];
	
	protected $casts = [
	 'extra_data' => 'array' ,
	];
	
	public function attribute () {
		return $this->belongsTo( Attribute::class );
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
	
	protected function getCodeAttribute () {
		return $this->ExtraDataGet( 'code' );
	}
	
	protected function setCodeAttribute ( $val ) {
		$this->ExtraDataSet( 'code' , $val );
	}
	
}
