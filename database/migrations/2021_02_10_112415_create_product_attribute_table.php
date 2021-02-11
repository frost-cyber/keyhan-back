<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductAttributeTable extends Migration {
	
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up () {
		Schema::create( 'product_attribute' , function( Blueprint $table ) {
			$table->increments( 'id' );
			$table->foreignId( 'product_id' )->index();
			$table->foreignId( 'property_id' )->index();
			$table->foreignId( 'property_value_id' )->index();
			
			$table->string( 'group_name' )->index()->nullable();
			$table->integer( 'number' );
			
			$table->foreign( 'product_id' )->references( 'id' )->on( 'products' )->cascadeOnDelete()->cascadeOnUpdate();
			$table->foreign( 'property_id' )->references( 'id' )->on( 'products' )->cascadeOnDelete()->cascadeOnUpdate();
			$table->foreign( 'property_value_id' )->references( 'id' )->on( 'products' )->cascadeOnDelete()->cascadeOnUpdate();
		} );
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down () {
		Schema::dropIfExists( 'product_attributes' );
	}
	
}
