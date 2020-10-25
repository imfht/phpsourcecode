<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url')->comment('访问路径');
            $table->string('view')->comment('对应模板');
            $table->string('openurl')->comment('外链网址');
            $table->integer('views')->comment('浏览量');
            $table->integer('is_open')->comment('是否开放浏览');
            $table->string('cover')->comment('封面');
            $table->string('thumb')->comment('封面微缩图');

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
        Schema::dropIfExists('pages');
    }
}
