<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        //创建表
        Schema::create('roles', function($table) {
            $table->increments('id', 11);
            $table->string('title', 32);
            $table->string('description', 256);
        });
        //添加数据
        DB::table('roles')->insert(array(
            'id' => '1',
            'title' => '匿名用户',
            'description' => '未登陆的用户',
        ));
        DB::table('roles')->insert(array(
            'id' => '2',
            'title' => '注册用户',
            'description' => '通过网站注册的用户',
        ));
        DB::table('roles')->insert(array(
            'id' => '3',
            'title' => '管理员',
            'description' => '后台管理员用户',
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('roles');
    }

}
