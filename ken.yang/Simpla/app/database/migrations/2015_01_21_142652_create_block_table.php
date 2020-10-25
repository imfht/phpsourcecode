<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlockTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        //创建表
        Schema::create('block', function($table) {
            $table->increments('id', 11);
            $table->INTEGER('baid')->unsigned(); //区块区域ID
            $table->string('machine_name', 64); //机器名字
            $table->string('title', 32); //区块标题
            $table->string('description', 256); //区块描述
            $table->longText('body'); //区块内容
            $table->enum('type', array('system', 'model', 'customer'))->default('customer'); //区块所属系统标志
            $table->string('callback', 64); //如果type为system和model，则这应该有值
            $table->string('format', 64); //格式化样式
            $table->string('theme', 64); //所属主题
            $table->enum('status', array('0', '1'))->default(1); //状态，1为开启，0为结束
            $table->INTEGER('weight')->unsigned()->default(0); //区块位置
            $table->string('pages', 64); //在哪些页面显示
            $table->INTEGER('cache')->unsigned()->default(0); //是否缓存
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('block');
    }

}
