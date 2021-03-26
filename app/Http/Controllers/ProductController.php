<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Null_;
use Str;

class ProductController extends Controller {

	protected $product;

	public function __construct ( Product $product ) {
		$this->product = $product;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index () {
		return Product::with( [ 'variables' , 'attributes' , 'brand' ] )->get();
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param ProductRequest $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store ( ProductRequest $request ) {
		return \response( [
		 'message'  => 'Create Product Successfully' ,
		 'category' => $this->save( $request->all() ) ,
		] , 200 );
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  Product $product
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show (  Product $product) {
		return $product->load(['variables' , 'attributes' , 'brand']);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param ProductRequest $request
	 * @param Product $product
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update ( ProductRequest $request , Product $product ) {

		return \response( [
		 'message'  => 'Update Product Successfully' ,
		 'category' => $this->save( $request->all() , $product ) ,
		] , 200 );
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy ( Product $product ) {
		$product->delete();

		return \response( [
		 'message'  => 'Delete Product Successfully' ,
		 'category' => $product ,
		] , 200 );
	}

	protected function save ( array $data , Product $product = NULL ) {
		//If Create Product
		if ( $product == NULL ) {
			$product = new Product();
		}

		$product->name = $data[ 'name' ];
		$product->slug = Str::slug( $data[ 'slug' ] );
		$product->sku = $data[ 'sku' ];
		$product->review = $data[ 'review' ];
		$product->short_review = $data[ 'short_review' ];
		$product->description = $data[ 'description' ] ?? NULL;
		$product->is_virtual = $data[ 'type' ] == 2;
		$product->brand_id = $data[ 'brand_id' ] ?? NULL;

		$product->save();

		$this->syncAttributes( $data[ 'attributes' ] , $product );
		$this->syncVariables( $data[ 'variables' ] , $product );

		$product->refresh();

		return $product;
	}

	protected function syncAttributes ( array $attributesData , Product $product ) {
		$syncData = [];
		foreach ( $attributesData as $i => $attribute){
            foreach ( $attribute['values'] as $index => $attributeData ) {
                $attributeValue = AttributeValue::find( $attributeData );
                if ( $attributeValue ) {
                    $syncData[ $attributeValue->id ] = [
                        'attribute_id' => $attributeValue->attribute->id ,
                        'group_name'   => $attributeData[ 'group_name' ] ?? NULL ,
                        'number'       => $i ,
                    ];
                }
            }
        }

		$product->attributes()->sync( $syncData );
	}

	protected function syncVariables ( array $variablesData , Product $product ) {
		$issetVariables=[];

		foreach ( $variablesData as $index => $variableData ) {
			$variableValue = AttributeValue::find( $variableData[ 'variable_id' ]??0 );

            $syncData = [
                'purchase_price'    => $variableData[ 'purchase_price' ] ?? NULL ,
                'selling_price'     => $variableData[ 'selling_price' ] ?? NULL ,
                'discounted_price'  => $variableData[ 'discounted_price' ] ?? NULL ,
                'wholesale_price'   => $variableData[ 'wholesale_price' ] ?? NULL ,
                'minimum_wholesale' => $variableData[ 'minimum_wholesale' ] ?? NULL ,
                'inventory'         => $variableData[ 'inventory' ] ?? NULL ,
            ];

            if ($variableValue){
                $syncData['variable_value_id'] = $variableValue->id;
                $syncData['variable_id']=$variableValue->attribute->id;
            }

            if ($variableData['id']??false){
                $issetVariables[]=$variableData['id'];

                $product->variables->find($variableData['id'])->update($syncData);
            }

            $product->variables()->create($syncData);
		}

		$product->variables()->whereNotIn('id' , $issetVariables)->delete();

	}
}
