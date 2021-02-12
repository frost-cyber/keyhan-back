<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVariableTable extends Migration {
	
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up () {
		Schema::create( 'product_variable' , function( Blueprint $table ) {
			$table->increments( 'id' );
			$table->foreignId( 'product_id' );
			$table->foreignId( 'variable_id' )->nullable();
			$table->foreignId( 'variable_value_id' )->nullable();
			
			$table->bigInteger( 'purchase_price' )->nullable();
			$table->bigInteger( 'selling_price' )->nullable();
			$table->bigInteger( 'discounted_price' )->nullable();
			$table->bigInteger( 'wholesale_price' )->nullable();
			$table->bigInteger( 'minimum_wholesale' )->nullable();
			$table->bigInteger( 'inventory' )->default( 0 );
			$table->string( 'unit' )->nullable();
			
			$table->foreign( 'product_id' )->references( 'id' )->on( 'products' )->cascadeOnDelete()->cascadeOnUpdate();
			$table->foreign( 'variable_id' )->references( 'id' )->on( 'attributes' )->cascadeOnDelete()->cascadeOnUpdate();
			$table->foreign( 'variable_value_id' )->references( 'id' )->on( 'attribute_values' )->cascadeOnDelete()->cascadeOnUpdate();
		} );
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down () {
		Schema::dropIfExists( 'product_variable' );
	}
	
}
