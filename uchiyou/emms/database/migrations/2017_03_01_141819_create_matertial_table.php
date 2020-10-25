<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMatertialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::create('mm_material', function (Blueprint $table) {
    		$table->increments('id');
    		$table->string('name');
    		$table->string('material_number');
    		$table->integer("tree_trunk_id");
    		$table->tinyInteger('sort',false,true);
    		$table->text('description');
    		$table->string('main_type');//固定资产或非固定资产
    		$table->text('picture_url');
    		$table->string('type');// 
    		$table->double('price');
    		$table->tinyInteger('status');// 有 1待使用，2使用中，3有缺陷，4已报废等状态-
    		$table->tinyInteger('delete')->default(1);// 有 1待使用，2使用中，3有缺陷，4已报废等状态-
       		$table->timestamps();
    	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
