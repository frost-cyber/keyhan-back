<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class StoreCategoryController extends Controller {
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index () {
		return Category::whereType( Category::TYPE_STORE )->get();
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param CategoryRequest $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store ( CategoryRequest $request ) {
		return \response( [
		 'message'  => 'Create Category Successfully' ,
		 'category' => $this->save( $request->all() ) ,
		] , 200 );
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param \App\Models\Category $category
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show ( Category $category ) {
		return $category;
	}
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param CategoryRequest $request
	 * @param \App\Models\Category $category
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update ( CategoryRequest $request , Category $category ) {
		$category = Category::findOrFail( $id );
		
		return \response( [
		 'message'  => 'Update Category Successfully' ,
		 'category' => $this->save( $request->all() , $category ) ,
		] , 200 );
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param \App\Models\Category $category
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy ( Category $category ) {
		$category->delete();
		
		return \response( [
		 'message'  => 'Delete Category Successfully' ,
		 'category' => $category ,
		] , 200 );
	}
	
	protected function save ( array $data , Category $category = NULL ) {
		//IF Create Category
		if ( ! $category ) {
			$category = new Category();
		}
		
		$category->name = $data[ 'name' ];
		$category->slug = \Str::slug( $data[ 'slug' ] );
		$category->parent_id = $data[ 'parent_id' ] ?? NULL;
		$category->type = Category::TYPE_STORE;
		$category->save();
		
		return $category;
	}
	
}
