<?php

namespace App\Http\Requests;

use App\Models\Attribute;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttributeRequest extends FormRequest {
	
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize () {
		return TRUE;
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules () {
		$roles = [
		 'name'        => 'required' ,
		 'is_variable' => 'required|boolean' ,
		 'values'      => 'required|array' ,
		];
		
		$roles[ 'type' ] = [
		 'required' ,
		 Rule::in( [ Attribute::TYPE_SIMPLE , Attribute::TYPE_COLOR , Attribute::TYPE_UNIT ] ) ,
		];
		
		if ( $this->has( 'type' ) ) {
			switch ( $this->input( 'type' ) ) {
				case Attribute::TYPE_SIMPLE:
					$roles[ 'values.*.value' ] = 'required';
					break;
				case Attribute::TYPE_COLOR:
					$roles[ 'values.*.name' ] = 'required';
					$roles[ 'values.*.code' ] = 'required';
					break;
				case Attribute::TYPE_UNIT:
					$roles[ 'values.*.value' ] = 'required';
					$roles[ 'unit' ] = 'required';
					break;
			}
		}
		
		return $roles;
	}
	
}
