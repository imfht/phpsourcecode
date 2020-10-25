<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFieldTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('field', function($table) {
            $table->string('type', 32); //字段类型
            $table->string('name', 256); //字段类型展示名字
            $table->string('description', 256)->nullable(); //描述
            $table->text('data'); //字段类型初始化配置数据

            $table->primary(array('type'));
        });
        //添加数据
        DB::table('field')->insert(array(
            'type' => 'category',
            'name' => '分类术语',
            'description' => '添加分类到你所属的内容类型，将分类与内容绑定',
            'data' => '',
        ));
        DB::table('field')->insert(array(
            'type' => 'text',
            'name' => '文本框',
            'description' => '一个普通的文本框',
            'data' => '',
        ));
        DB::table('field')->insert(array(
            'type' => 'textarea',
            'name' => '文本域',
            'description' => '一个普通的文本域，可以填写非常多的内容',
            'data' => '',
        ));
        DB::table('field')->insert(array(
            'type' => 'checkbox',
            'name' => '复选框',
            'description' => '设置一个或者多个复选框',
            'data' => '',
        ));
        DB::table('field')->insert(array(
            'type' => 'radio',
            'name' => '单选框',
            'description' => '单选框用于从多个选项中只选择一个',
            'data' => '',
        ));
        DB::table('field')->insert(array(
            'type' => 'select',
            'name' => '下拉列表',
            'description' => '一个下拉列表选择框',
            'data' => '',
        ));
        DB::table('field')->insert(array(
            'type' => 'image',
            'name' => '图片上传',
            'description' => '图片上传功能',
            'data' => '',
        ));
        DB::table('field')->insert(array(
            'type' => 'file',
            'name' => '文件上传',
            'description' => '文件上传功能',
            'data' => '',
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        //
    }

}
