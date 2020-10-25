<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNodeFieldTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('node_field', function($table) {
            $table->unsignedInteger('nid');
            $table->foreign('nid')->references('id')->on('node'); //内容ID
            $table->string('field_name', 64)->references('field_name')->on('field_config'); //字段名字
            //$table->string('field_name', 64); //字段名字
            $table->longtext('value'); //数据
            $table->integer('weight')->default(0);

            $table->primary(array('nid', 'field_name'));
        });
        //添加数据
        DB::table('node_field')->insert(array(
            'nid' => '2',
            'field_name' => 'category',
            'value' => '1',
            'weight' => '0',
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('node_field');
    }

}
