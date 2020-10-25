<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * 分享资源数据库迁移表
 * Class CreateResourcesTable
 */
class CreateResourcesTable extends Migration
{
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up()
		{
				Schema::create('resources', function(Blueprint $table)
				{
						$table->increments('id');
						$table->unsignedInteger('creater_id')->comment('外键，资源提供者id');
						$table->unsignedInteger('project_id')->comment('外键，资源所属项目id');
						$table->string('filename', 100)->comment('资源文件名');
						$table->text('mime')->comment('文件mime类型');
						$table->string('origin_name', 100)->comment('原始资源文件名');
						$table->text('ext_name')->comment('资源文件扩展名');
						$table->timestamps();
				});

				Schema::table('resources', function($table)
				{
						$table->foreign('creater_id')->references('id')->on('users');
						$table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
				});
		}

		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down()
		{
				Schema::drop('resources');
		}
}
