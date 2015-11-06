<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('lang', 5);
            $table->string('title', 120);
            $table->string('seo_slug', 120)->unique();
            $table->string('seo_title')->nullable();
            $table->string('seo_description')->nullable();
            $table->string('seo_keywords')->nullable();
            $table->string('seo_author')->nullable();
            $table->string('seo_image')->nullable();
            $table->longText('content')->nullable();
            $table->date('publish_start')->nullable();
            $table->date('publish_end')->nullable();
            $table->enum('role_permission', ['NONE', 'ROLE', 'USER']);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('contents');
    }
}
