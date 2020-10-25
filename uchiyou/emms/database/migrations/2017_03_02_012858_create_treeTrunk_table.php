<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTreeTrunkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::create('mm_tree_trunk', function (Blueprint $table) {
    		$table->increments('id');
    		$table->string('name');
    		$table->smallInteger('type');// 可以是部门，也可以是仓库名字，也可是物资种类，也可以是自定义种类
    		$table->string('number');
    		$table->integer('parent_id');
    		$table->integer('company_id');
    		$table->tinyInteger('sort');
    		$table->tinyInteger('delete')->default(1);
    		$table->timestamps();
    		$table->string('contacts',20);
    		$table->string('contacts_phone',11);
    		$table->string('description');
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
