<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LayerAddGeojson extends Migration
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
            $table->string('geojson_geomtype')->nullable();
            $table->string('geojson_attributes')->nullable();
            $table->longText('geojson_features')->nullable();
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
            $table->dropColumn('geojson_features');
            $table->dropColumn('geojson_attributes');
            $table->dropColumn('geojson_geomtype');
        });
    }
}
