<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryTypeTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('category_type', function($table) {
            $table->increments('id', 11);
            $table->string('title', 256);
            $table->string('description', 256)->nullable();
            $table->string('machine_name', 256);
        });
        //添加数据
        DB::table('category_type')->insert(array(
            'id' => '1',
            'title' => '栏目',
            'description' => '系统默认内容分类，该分类可用于网站内容分类栏目',
            'machine_name' => 'category',
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('category_type');
    }

}
