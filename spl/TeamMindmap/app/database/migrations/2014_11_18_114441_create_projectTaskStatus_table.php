<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectTaskStatusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('projectTaskStatus', function(Blueprint $table)
		{
			$table->increments('id')->comment('自增的主键id');

            $table->string('name', 30)->comment('状态的命名，用于内部使用');
            $table->string('label', 30)->comment('状态的名称，用于外部显示');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('projectTaskStatus');
	}

}
