<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateProjectDiscussionFollower
 *
 * 此表用于记录 项目-讨论 数据中，被请求关注的用户, 属于枢纽表
 */
class CreateProjectDiscussionFollower extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->tableName, function(Blueprint $table){
			$table->increments('id')->comment('主键id');

			$table->unsignedInteger('projectDiscussion_id')->comment('关联的讨论');
			$table->foreign('projectDiscussion_id')->references('id')->on('projectDiscussions')->onDelete('cascade');

			$table->unsignedInteger('follower_id')->comment('关键的被请求关注用户');
			$table->foreign('follower_id')->references('id')->on('users')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop($this->tableName);
	}

	private $tableName = 'projectDiscussion_follower';

}
