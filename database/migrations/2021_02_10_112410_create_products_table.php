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
			$table->foreignId( 'brand_id' )->nullable();

			$table->string( 'name' );
			$table->string( 'slug' )->unique();
			$table->string( 'sku' )->unique()->comment( "کد محصول" );
			$table->smallInteger('condition')->default(0);
			$table->text( 'short_review' );
			$table->text( 'description' );
			$table->longText( 'review' );
			$table->boolean( 'is_virtual' )->default(FALSE);
			$table->longText( 'extra_data' )->nullable();
			$table->longText( 'default_data' )->nullable()->comment( "اطلاعات پیشفرض محصول جهت لود سریعتر محصول" );
            $table->timestamp('published_at')->nullable();$table->timestamps();
			$table->softDeletes();

			$table->foreign('brand_id')->references('id')->on('brands')->nullOnDelete()->cascadeOnUpdate();
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
