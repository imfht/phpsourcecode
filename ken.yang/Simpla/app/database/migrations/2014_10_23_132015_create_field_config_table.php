<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFieldConfigTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('field_config', function($table) {
            $table->increments('id', 11); // ID
            $table->string('node_type', 32); //内容类型
            $table->string('label', 32); //字段名字别称
            $table->string('field_name', 32); //字段名字
            $table->string('field_type', 32); //字段类型
            $table->text('config_data'); //字段配置数据
            $table->INTEGER('weight')->unsigned()->default(0);//排序位置
        });
        //添加数据
        DB::table('field_config')->insert(array(
            'node_type' => 'article',
            'label' => '分类',
            'field_name' => 'category',
            'field_type' => 'category',
            'config_data' => '{"category":"category","type":"category"}',
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
