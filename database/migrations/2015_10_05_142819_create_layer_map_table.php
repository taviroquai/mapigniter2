<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLayerMapTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('layeritem', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned();
            $table->integer('layer_id')->unsigned();
            $table->integer('map_id')->unsigned();
            $table->boolean('visible');
            $table->integer('displayorder')->unsigned();
            
            $table->timestamps();
            $table->foreign('layer_id')->references('id')->on('layers')->onDelete('cascade');
            $table->foreign('map_id')->references('id')->on('maps')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('layeritem');
    }
}
