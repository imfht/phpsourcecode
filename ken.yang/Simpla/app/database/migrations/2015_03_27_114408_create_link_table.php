<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinkTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('link', function($table) {
            $table->increments('id', 11);
            $table->string('title', 32); //标题
            $table->string('url', 256); //连接
            $table->string('description', 128)->nullable(); //描述
            $table->string('image', 128)->nullable(); //图片
            $table->INTEGER('weight')->unsigned()->default(0); //位置
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('link');
    }

}
