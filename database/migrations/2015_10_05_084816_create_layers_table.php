<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('layers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('content_id')->unsigned();
            $table->integer('srid');
            $table->string('type');
            $table->string('bing_key')->nullable();
            $table->string('bing_imageryset')->nullable();
            $table->string('mapquest_layer')->nullable();
            $table->string('wms_servertype')->nullable();
            $table->string('wms_url')->nullable();
            $table->string('wms_layers')->nullable();
            $table->boolean('wms_tiled')->nullable();
            $table->string('wms_version')->nullable();
            $table->string('wfs_url')->nullable();
            $table->string('wfs_typename')->nullable();
            $table->string('feature_info_template')->nullable();
            $table->string('wfs_version')->nullable();
            $table->string('ol_style_static_icon')->nullable();
            $table->string('ol_style_static_fill_color')->nullable();
            $table->string('ol_style_static_stroke_color')->nullable();
            $table->string('ol_style_static_stroke_width')->nullable();
            $table->string('ol_style_field_icon')->nullable();
            $table->string('ol_style_field_fill_color')->nullable();
            $table->string('ol_style_field_stroke_color')->nullable();
            $table->string('ol_style_field_stroke_width')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('content_id')->references('id')->on('contents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('layers');
    }
}
