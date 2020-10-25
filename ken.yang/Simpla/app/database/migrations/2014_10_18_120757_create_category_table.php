<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('category', function($table) {
            $table->increments('id', 11);
            $table->INTEGER('pid')->unsigned()->default(0);
            $table->INTEGER('tid')->unsigned()->default(1);
            $table->string('title', 64);
            $table->string('description', 256)->nullable();
            $table->INTEGER('weight')->unsigned()->default(0);
        });
        //添加数据
        DB::table('category')->insert(array(
            'id' => '1',
            'pid' => '0',
            'tid' => '1',
            'title' => '未分类',
            'description' => '未分类栏目，所有未分类的文章都放在这里',
            'weight' => '0',
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('category');
    }

}
