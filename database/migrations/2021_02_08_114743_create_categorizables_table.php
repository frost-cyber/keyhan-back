<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategorizablesTable extends Migration {
	
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up () {
		Schema::create( 'categorizables' , function( Blueprint $table ) {
			$table->foreignId( 'category_id' )->index();
			$table->foreignId( 'categorizable_id' )->index();
			$table->string( 'categorizable_type' );
			
			$table->foreign( 'category_id' )->references( 'id' )->on( 'categories' )->cascadeOnDelete()->cascadeOnUpdate();
		} );
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down () {
		Schema::dropIfExists( 'categorizables' );
	}
	
}
