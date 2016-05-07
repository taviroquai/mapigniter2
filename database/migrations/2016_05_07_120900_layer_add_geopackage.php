<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LayerAddGeopackage extends Migration
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
            $table->string('geopackage_filename')->nullable();
            $table->string('geopackage_table')->nullable();
            $table->string('geopackage_field')->nullable();
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
            $table->dropColumn('geopackage_filename');
            $table->dropColumn('geopackage_table');
            $table->dropColumn('geopackage_field');
        });
    }
}
