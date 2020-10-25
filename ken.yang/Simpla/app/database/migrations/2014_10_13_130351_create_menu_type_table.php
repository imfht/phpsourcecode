<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuTypeTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('menu_type', function($table) {
            $table->increments('id', 11);
            $table->string('title', 32);
            $table->string('description', 256)->nullable();
            $table->string('machine_name', 64);
        });
        //添加数据
        DB::table('menu_type')->insert(array(
            'id' => '1',
            'title' => '顶部菜单',
            'description' => '网站顶部的菜单栏',
            'machine_name' => 'menu_top',
        ));
        DB::table('menu_type')->insert(array(
            'id' => '2',
            'title' => '底部菜单',
            'description' => '网站底部的菜单栏',
            'machine_name' => 'menu_bottom',
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('menu_type');
    }

}
