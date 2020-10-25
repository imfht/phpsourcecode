<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectTaskMemberTable extends Migration {

	/**
	 * Run the migrations.
	 * 此数据表用于记录项目任务的成员
	 * @return void
	 */
	public function up()
	{
		Schema::create('projectTask_member', function(Blueprint $table)
		{
			$table->increments('id');  //表内主键

            $table->integer('task_id')->unsigned();  //任务的id
            $table->foreign('task_id')->references('id')->on('projectTasks')->onDelete('cascade');
            $table->integer('member_id')->unsigned();  //成员的id
            $table->foreign('member_id')->references('id')->on('users')->onDelete('cascade');


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
		Schema::drop('projectTask_member');
	}

}
