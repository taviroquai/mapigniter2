<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LayerAddPostgis extends Migration
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
            $table->string('postgis_schema')->nullable();
            $table->string('postgis_table')->nullable();
            $table->string('postgis_field')->nullable();
            $table->string('postgis_attributes')->nullable();
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
            $table->dropColumn('postgis_attributes');
            $table->dropColumn('postgis_field');
            $table->dropColumn('postgis_table');
            $table->dropColumn('postgis_schema');
        });
    }
}
