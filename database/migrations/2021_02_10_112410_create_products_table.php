<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration {
	
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up () {
		Schema::create( 'products' , function( Blueprint $table ) {
			$table->id();
			$table->string( 'name' );
			$table->string( 'slug' )->unique();
			$table->string( 'sku' )->unique()->comment( "کد محصول" );
			$table->text( 'short_review' );
			$table->text( 'description' );
			$table->longText( 'review' );
			$table->longText( 'default_data' )->nullable();
			$table->text( 'extra_data' )->nullable();
			$table->boolean( 'is_downloadable' )->default( FALSE );
			$table->bigInteger( 'brand_id' )->nullable();
		} );
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down () {
		Schema::dropIfExists( 'products' );
	}
	
}
