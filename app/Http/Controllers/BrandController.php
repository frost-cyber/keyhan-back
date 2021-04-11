<?php

namespace App\Http\Controllers;

use App\Http\Requests\BrandRequest;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller {

	public function index () {
		return Brand::all();
	}

	public function store ( BrandRequest $request ) {

		return \response( [
		 'message'  => 'Craete Brand Successfully' ,
		 'brand' => $this->save( $request->all() ) ,
		] , 200 );
	}

	public function show ( Brand $brand ) {
		return $brand;
	}

	public function update ( BrandRequest $request , Brand $brand ) {
		return \response( [
		 'message'  => 'Update Brand Successfully' ,
		 'brand' => $this->save( $request->all() , $brand ) ,
		] , 200 );
	}

	public function destroy ( Brand $brand ) {
		$brand->forceDelete();

		return \response( [
		 'message'  => 'Delete Brand Successfully' ,
		 'brand' => $brand ,
		] , 200 );
	}

	protected function save ( array $data , Brand $brand = NULL ) {
		if ( $brand == NULL ) {
			$brand = new Brand();
		}

		$brand->name = $data[ 'name' ];
		$brand->slug = $data[ 'slug' ];
		$brand->save();
        $brand->logo = $data['logo'];

		return $brand;
	}

}
