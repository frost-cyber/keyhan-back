<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest {
	
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize () : bool {
		return TRUE;
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules () : array {
		return [
		 'name'      => 'required|min:2|max:20' ,
		 'slug'      => 'required|min:2|max:20|unique:categories' ,
		 'parent_id' => 'nullable|integer|min:1' ,
		];
	}
	
	public function validationData () {
		$data = $this->all();
		
		if ( isset( $data[ 'slug' ] ) ) {
			$data[ 'slug' ] = \Str::Slug( $data[ 'slug' ] );
		}
		
		return $data;
	}
	
}