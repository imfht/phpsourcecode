<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryDataTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('category_data', function($table) {
            $table->unsignedInteger('cid');
            $table->foreign('cid')->references('id')->on('category');//分类ID

            $table->unsignedInteger('nid');
            $table->foreign('nid')->integer('nid', 11)->references('id')->on('node');//内容ID

            $table->primary(array('cid', 'nid'));
        });
        //添加数据
        DB::table('category_data')->insert(array(
            'cid' => '1',
            'nid' => '2',
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
