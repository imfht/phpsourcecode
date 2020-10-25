<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableFriendLinks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('friend_links', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('网站名称');
            $table->string('url')->comment('网站网址');
            $table->integer('sort')->comment('排序');
            $table->integer('views')->comment('浏览量');
            $table->integer('is_open')->comment('是否开放浏览');
            $table->string('cover')->comment('封面');
            $table->string('thumb')->comment('封面微缩图');
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
        Schema::dropIfExists('friend_links');
    }
}
