<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateTableProjectTaskPriority
 *
 * 此表用于对应项目中任务的优先级
 */
class CreateTableProjectTaskPriority extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

		Schema::create($this->tableName, function(Blueprint $table){
			$table->increments('id')->comment('主键Id');
			$table->string('name', 40)->comment('优先级名称， 内部时使用');
			$table->string('label', 40)->commenbt('优先级名称，外部显示');
		});

		//修改项目-任务表，添加优先级字段
		$projectTaskPriorityTableName = $this->tableName;
		Schema::table('projectTasks', function(Blueprint $table)use($projectTaskPriorityTableName){
			$table->unsignedInteger('priority_id')->default(1)->comment('外键，描述任务的优先级');
			$table->foreign('priority_id')->references('id')->on($projectTaskPriorityTableName);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//移除掉添加到项目-任务表中的附加外键约束
		Schema::table('projectTasks', function(Blueprint $table){
			$table->dropForeign('projectTasks_priority_id_foreign');
			$table->dropColumn('priority_id');
		});

		Schema::drop($this->tableName);
	}

	private $tableName = 'projectTaskPriorities';
}
