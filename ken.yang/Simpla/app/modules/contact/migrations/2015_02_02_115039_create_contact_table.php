<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        //创建表
        Schema::create('contact', function($table) {
            $table->increments('id', 11);
            $table->string('title', 32); //标题
            $table->string('people', 32); //联系人
            $table->string('contact', 256); //联系方式
            $table->longText('body'); //内容
            $table->timestamps('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('contact');
    }

}
