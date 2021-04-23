<?php

namespace App\Http\Controllers;

use App\Http\Requests\BrandRequest;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;

class BrandController extends Controller {

	public function index () {
	    $brands = Brand::query();

	    if(request()->has('category')){
	        $category = Category::where('slug' , request('category'))->first();
	        function getIds($model , $relation){
	            $ids = [$model->id];
	            if (!$model->$relation) return $ids;
	            foreach($model->$relation as $value ){
	                array_push($ids,...getIds($value , $relation));
                }
	            return $ids;
            }
            $brands = $brands->whereHas('products' , function($query) use ($category) {
                return $query->whereHas('categories' , function($query) use ($category) {
                    return $query->whereIn('id' , $category?getIds($category , 'children'):[] );
                });
            });
        }
		return $brands->get();
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
