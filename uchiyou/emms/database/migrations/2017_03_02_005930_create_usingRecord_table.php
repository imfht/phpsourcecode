<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsingRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	//
    	Schema::create('mm_using_record', function (Blueprint $table) {
    		$table->increments('id');
    		$table->integer('material_id');
    		$table->integer('user_id');
    		$table->integer('tree_trunk_id');
    		$table->string('company_id');
    		$table->string('description');
    		$table->tinyInteger('has_deliverd');
    		$table->tinyInteger('delete')->default(1);// 表示当前记录被各种角色删除的记录
    	/*	
    	 * 借用设备后，设置 material 表中状态。 
    	 * $table->tinyInteger("use_status");
    	 */	
    		$table->timestamps('startTime');
    		$table->timestamps('deadline');
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
