<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductOrderTable extends Migration {
    public function up() {
        Schema::create( 'product_order', function ( Blueprint $table ) {
            $table->id();
            $table->foreignId( 'product_variant_id' );
            $table->foreignId( 'order_id' );

            $table->bigInteger('purchase_price');
            $table->bigInteger('price');
            $table->string('price_type' , 20);
            $table->integer('quantity');

            $table->timestamps();

            $table->foreign( 'product_variant_id' )->references( 'id' )->on( 'product_variants' )->restrictOnDelete();
            $table->foreign( 'order_id' )->references( 'id' )->on( 'orders' )->cascadeOnDelete();
        } );
    }

    public function down() {
        Schema::dropIfExists( 'product_order' );
    }
}
