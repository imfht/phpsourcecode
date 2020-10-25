<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlockAreaTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        //创建表
        Schema::create('block_area', function($table) {
            $table->increments('id', 11);
            $table->string('title', 32);
            $table->string('description', 256)->nullable();
            $table->string('machine_name', 64);
            $table->INTEGER('weight')->unsigned()->default(0);
        });
        //添加数据
        DB::table('block_area')->insert(array(
            'id' => '1',
            'title' => '未选择',
            'description' => '未选择',
            'machine_name' => 'noarea',
            'weight' => '9999',
        ));
        DB::table('block_area')->insert(array(
            'id' => '2',
            'title' => '头部区域',
            'description' => '头部区域',
            'machine_name' => 'header',
            'weight' => '1',
        ));
        DB::table('block_area')->insert(array(
            'id' => '3',
            'title' => '侧边栏左边区域',
            'description' => '侧边栏左边区域',
            'machine_name' => 'sidebar_left',
            'weight' => '2',
        ));
        DB::table('block_area')->insert(array(
            'id' => '4',
            'title' => '侧边栏右边区域',
            'description' => '侧边栏右边区域',
            'machine_name' => 'sidebar_right',
            'weight' => '3',
        ));
        DB::table('block_area')->insert(array(
            'id' => '5',
            'title' => '中间顶部区域',
            'description' => '中间顶部区域',
            'machine_name' => 'content_top',
            'weight' => '4',
        ));
        DB::table('block_area')->insert(array(
            'id' => '6',
            'title' => '中间底部区域',
            'description' => '中间底部区域',
            'machine_name' => 'content_bottom',
            'weight' => '5',
        ));
        DB::table('block_area')->insert(array(
            'id' => '7',
            'title' => '底部区域',
            'description' => '底部区域',
            'machine_name' => 'footer',
            'weight' => '6',
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('block_area');
    }

}
