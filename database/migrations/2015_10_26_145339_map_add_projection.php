<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MapAddProjection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('maps', function($table)
        {
            $table->dropColumn('srid');
        });
        Schema::table('maps', function($table)
        {
            $table->integer('projection_id')->unsigned();
            $table->foreign('projection_id')->references('srid')->on('projections')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('maps', function($table)
        {
            $table->dropForeign('maps_projection_id_foreign');
            $table->dropColumn('projection_id');
        });
    }
}
