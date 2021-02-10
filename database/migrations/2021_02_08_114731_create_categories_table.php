<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration {
	
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up () {
		Schema::create( 'categories' , function( Blueprint $table ) {
			$table->id();
			$table->foreignId( 'parent_id' )->index()->nullable();
			
			$table->string( 'name' );
			$table->string( 'slug' )->unique();
			$table->integer( 'type' )->index();
			
			$table->foreign( 'parent_id' )->references( 'id' )->on( 'categories' );
		} );
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down () {
		Schema::dropIfExists( 'categories' );
	}
	
}
