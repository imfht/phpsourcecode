<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('menu', function($table) {
            $table->increments('id', 11);
            $table->INTEGER('pid')->unsigned()->default(0);
            $table->INTEGER('tid')->unsigned()->default(1);
            $table->string('title', 32);
            $table->string('description', 256)->nullable();
            $table->string('url', 256);
            $table->INTEGER('weight')->unsigned()->default(0);
        });
        //添加数据
        DB::table('menu')->insert(array(
            'id' => '1',
            'pid' => '0',
            'tid' => '1',
            'title' => '首页',
            'description' => '顶部首页链接',
            'url' => '/',
            'weight' => '0',
        ));
        DB::table('menu')->insert(array(
            'id' => '2',
            'pid' => '0',
            'tid' => '1',
            'title' => '关于我',
            'description' => '关于我的描述',
            'url' => '/node/1',
            'weight' => '0',
        ));
        DB::table('menu')->insert(array(
            'id' => '3',
            'pid' => '0',
            'tid' => '2',
            'title' => '首页',
            'description' => '底部首页链接',
            'url' => '/',
            'weight' => '0',
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('menu');
    }

}
