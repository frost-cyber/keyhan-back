<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller {
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index () {
		return Brand::all();
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store ( Request $request ) {
		
		return \response( [
		 'message'  => 'Craete Brand Successfully' ,
		 'category' => $this->save( $request->all() ) ,
		] , 200 );
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param \App\Models\Brand $brand
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show ( Brand $brand ) {
		return $brand;
	}
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \App\Models\Brand $brand
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update ( Request $request , Brand $brand ) {
		return \response( [
		 'message'  => 'Update Brand Successfully' ,
		 'category' => $this->save( $request->all() , $brand ) ,
		] , 200 );
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param \App\Models\Brand $brand
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy ( Brand $brand ) {
		$brand->delete();
		
		return \response( [
		 'message'  => 'Delete Brand Successfully' ,
		 'category' => $brand ,
		] , 200 );
	}
	
	protected function save ( array $data , Brand $brand = NULL ) {
		if ( $brand == NULL ) {
			$brand = new Brand();
		}
		
		$brand->name = $data[ 'name' ];
		$brand->logo = $data[ 'logo' ];
		$brand->save();
		
		return $brand;
	}
	
}
