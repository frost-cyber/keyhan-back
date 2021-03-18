<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest {

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
		$rules = [
		 'name'                          => 'required' ,
		 'slug'                          => 'required|unique:products' ,
		 'sku'                           => 'required|regex:/^A[A-Z0-9]{6}/i|unique:products' ,
		 'short_review'                  => 'required' ,
		 'description'                   => 'required' ,
		 'review'                        => 'required' ,
		 'is_virtual'                    => 'required|boolean' ,
		 'brand_id'                      => 'nullable|integer|min:1' ,
		 'attributes'                    => 'required|array' ,
		 'attributes.*.id'               => 'required|integer|min:1' ,
		 'attributes.*.group_name'       => 'nullable' ,
		 'variables'                     => 'required|array' ,
		 'variables.*.id'                => 'nullable|integer|min:1' ,
		 'variables.*.purchase_price'    => 'nullable|integer|min:1' ,
		 'variables.*.selling_price'     => 'required|integer|min:1' ,
		 'variables.*.discounted_price'  => 'nullable|integer|min:1' ,
		 'variables.*.wholesale_price'   => 'nullable|integer|min:1' ,
		 'variables.*.minimum_wholesale' => 'nullable|integer|min:1' ,
		 'variables.*.inventory'         => 'required|integer|min:0' ,
		];
		return $rules;
	}
	public function validationData () {
		$data = $this->all();
		if ( $this->has( 'slug' ) ) {
			$data[ 'slug' ] = \Str::slug( $data[ 'slug' ] );
		}
		return $data;
	}
}
