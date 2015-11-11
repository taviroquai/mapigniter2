<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LayerAddShapefile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('layers', function($table)
        {
            $table->string('shapefile_filename')->nullable();
            $table->string('shapefile_geomtype')->nullable();
            $table->string('shapefile_wmsurl')->nullable();
            $table->longText('shapefile_msclass')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('layers', function($table)
        {
            $table->dropColumn('shapefile_msclass');
            $table->dropColumn('shapefile_filename');
            $table->dropColumn('shapefile_geomtype');
            $table->dropColumn('shapefile_wmsurl');
        });
    }
}
