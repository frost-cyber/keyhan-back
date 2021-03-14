<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files' , function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("extension");
            $table->smallInteger("type");
            $table->string("path");
            $table->string("link");
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('fileables' , function (Blueprint $table) {
            $table->morphs("fileable");
            $table->boolean("default")->default(FALSE);
            $table->integer("number")->nullable();
            $table->text("description")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
        Schema::dropIfExists('fileables');
    }
}
