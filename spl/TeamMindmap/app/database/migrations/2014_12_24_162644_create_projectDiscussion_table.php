<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateProjectDiscussionTable
 *
 * 此迁移类用于创建 项目-讨论 的数据库表
 */
class CreateProjectDiscussionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->tableName, function(Blueprint $table){
			$table->increments('id')->comment('主键id');

			$table->string('title', 60)->comment('标题');
			$table->text('content')->comment('内容');
			$table->boolean('open')->default(1)->comment('讨论是否处于开启状态');

			$table->unsignedInteger('creater_id')->comment('讨论的创建者');
			$table->foreign('creater_id')->references('id')->on('users')->onDelete('cascade');

			$table->unsignedInteger('project_id')->comment('讨论所在的项目');
			$table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');

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
		Schema::drop($this->tableName);
	}

	private $tableName = 'projectDiscussions';
}
