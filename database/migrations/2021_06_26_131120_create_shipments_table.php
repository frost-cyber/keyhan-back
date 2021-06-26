<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
	        $table->foreignId('order_id');
	        $table->foreignId('address_id');

            $table->string('tracking_code');
            $table->string('status');
	        $table->timestamp('shipments_date');
	        $table->timestamps();

	        $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
	        $table->foreign('address_id')->references('id')->on('addresses')->restrictOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipments');
    }
}
