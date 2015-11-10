<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LayerAddPostgisConnection extends Migration
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
            $table->string('postgis_host')->nullable();
            $table->string('postgis_port')->nullable();
            $table->string('postgis_user')->nullable();
            $table->string('postgis_pass')->nullable();
            $table->string('postgis_dbname')->nullable();
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
            $table->dropColumn('postgis_dbname');
            $table->dropColumn('postgis_pass');
            $table->dropColumn('postgis_user');
            $table->dropColumn('postgis_port');
            $table->dropColumn('postgis_host');
        });
    }
}
