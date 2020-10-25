<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ImageAndStorage extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('image', function (Blueprint $table) {
            $table->increments('id');
            $table->char('sha1', 40);
            $table->smallInteger('copy_count');
            $table->timestamps();
            $table->index('sha1');
        });

        Schema::create('image_copies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('image_id');
            $table->smallInteger('storage_id');
            $table->string('url');
            $table->integer('access_count');
            $table->timestamps();
            $table->tinyInteger('status');
            $table->index('image_id');
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('image');
        Schema::drop('image_copies');
    }
}
