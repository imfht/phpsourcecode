<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAdimages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adimages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('广告名称');
            $table->string('url')->comment('广告网址');
            $table->integer('adspace_id')->comment('广告位置');
            $table->integer('sort')->comment('排序');
            $table->integer('views')->comment('浏览量');
            $table->integer('is_open')->comment('是否开放浏览');
            $table->string('cover')->comment('广告图片');
            $table->string('thumb')->comment('图片微缩图');
            $table->string('hash')->comment('hash');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adimages');
    }
}
