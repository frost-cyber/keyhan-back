<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttributeRequest;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use function React\Promise\all;

class AttributeController extends Controller {
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index () {
		return Attribute::with( 'values' )->get();
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param AttributeRequest $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store ( AttributeRequest $request ) {
		return [
		 'message'    => 'Create Attribute Successfully' ,
		 'attribuite' => $this->saveAttribute( $request->all() ) ,
		];
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param Attribute $attribute
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show ( Attribute $attribute ) {
		return $attribute->load( 'values' );
	}
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param AttributeRequest $request
	 * @param Attribute $attribute
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update ( AttributeRequest $request , Attribute $attribute ) {
		return [
		 'message'    => 'Update Attribute Successfully' ,
		 'attribuite' => $this->saveAttribute( $request->all() , $attribute ) ,
		];
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Attribute $attribute
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy ( Attribute $attribute ) {
		$attribute = $attribute->load('values');
		$attribute->delete();
		
		return [
		 'message'    => 'Delete Attribute Successfully' ,
		 'attribuite' => $attribute ,
		];
	}
	
	public function saveAttribute ( array $data , Attribute $attribute = NULL ) {
		//If Create Attribute
		if ( $attribute == NULL ) {
			$attribute = new Attribute();
			
			$attribute->name = $data[ 'name' ];
			$attribute->type = $data[ 'type' ];
			$attribute->is_variable = $data[ 'is_variable' ];
			
			switch ( $data[ 'type' ] ) {
				case Attribute::TYPE_SIMPLE:
					break;
				case Attribute::TYPE_COLOR:
					break;
				case Attribute::TYPE_UNIT:
					$attribute->unit = $data[ 'unit' ];
					break;
			}
			
		} else {
		
		}
		
		$attribute->save();
		$this->saveAttribiteValues( $data[ 'values' ] , $attribute );
		
		return $attribute->load( 'values' );
	}
	
	protected function saveAttribiteValues ( array $values , Attribute $attribute ) {
		foreach ( $values as $value ) {
			
			$attributeValue = new AttributeValue();
			$attributeValue->attribute_id = $attribute->id;
			
			$this->saveAttribiteValue( $value , $attributeValue , $attribute );
		}
	}
	
	protected function saveAttribiteValue ( array $value , AttributeValue $attributeValue , Attribute $attribute ) {
		
		switch ( $attribute->type ) {
			
			case Attribute::TYPE_SIMPLE:
				$attributeValue->value = $value[ 'value' ];
				break;
			
			case Attribute::TYPE_COLOR:
				$attributeValue->value = $value[ 'name' ];
				$attributeValue->code = $value[ 'code' ];
				
				break;
			
			case Attribute::TYPE_UNIT:
				$attributeValue->value = $value[ 'value' ];
				break;
			
		}
		
		$attributeValue->save();
		
		return $attributeValue;
	}
	
}
