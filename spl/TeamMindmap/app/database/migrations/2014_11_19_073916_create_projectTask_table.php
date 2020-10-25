<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectTaskTable extends Migration {

	/**
	 * Run the migrations.
	 * 此数据表用于记录项目的任务
	 * @return void
	 */
	public function up()
	{
		Schema::create('projectTasks', function(Blueprint $table)
		{
            $table->increments('id');  //表内主键

            $table->string('name', 50);  //任务名称
            $table->string('description', 255);  //任务简介

            $table->integer('parent_id')->unsigned()->nullable();  //父任务的id
            $table->foreign('parent_id')->references('id')->on('projectTasks')->onDelete('cascade');
            $table->integer('project_id')->unsigned();  //所属项目的id
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->integer('creater_id')->unsigned();  //任务的创建者的id
            $table->foreign('creater_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('last_man')->unsigned();  //最后编辑的人的id
            $table->foreign('last_man')->references('id')->on('users')->onDelete('cascade');
            $table->integer('handler_id')->unsigned();  //任务的负责人的id
            $table->foreign('handler_id')->references('id')->on('users')->onDelete('cascade');


            $table->unsignedInteger('status_id')->comment('状态id')->default(1);
            $table->foreign('status_id')->references('id')->on('projectTaskStatus');


            $table->timestamp('expected_at')->default(DB::raw('CURRENT_TIMESTAMP'));;  //期望的任务完成时间
            $table->timestamp('finished_at')->nullable();;  //任务实际的完成时间
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
		Schema::drop('projectTasks');
	}

}
