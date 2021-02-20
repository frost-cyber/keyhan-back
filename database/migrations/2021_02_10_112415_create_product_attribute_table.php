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
			$table->id();
			$table->foreignId( 'product_id' )->index();
			$table->foreignId( 'attribute_id' )->index();
			$table->foreignId( 'attribute_value_id' )->index();
			
			$table->string( 'group_name' )->index()->nullable();
			$table->integer( 'number' );
			
			$table->foreign( 'product_id' )->references( 'id' )->on( 'products' )->cascadeOnDelete()->cascadeOnUpdate();
			$table->foreign( 'attribute_id' )->references( 'id' )->on( 'attributes' )->cascadeOnDelete()->cascadeOnUpdate();
			$table->foreign( 'attribute_value_id' )->references( 'id' )->on( 'attribute_values' )->cascadeOnDelete()->cascadeOnUpdate();
		} );
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down () {
		Schema::dropIfExists( 'product_attribute' );
	}
	
}
