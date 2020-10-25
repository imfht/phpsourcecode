<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * 分享数据库迁移表
 * Class CreateSharingsTable
 */
class CreateSharingsTable extends Migration
{
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up()
		{
				Schema::create('sharings', function(Blueprint $table)
				{
						$table->increments('id');
						$table->string('name', 50)->comment('分享名');
						$table->text('content')->comment('分享描述内容');
						$table->unsignedInteger('creater_id')->comment('外键，分享者id');
						$table->unsignedInteger('project_id')->comment('外键，分享所属项目id');
						$table->timestamps();
				});

				Schema::table('sharings', function($table)
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
				Schema::drop('sharings');
		}

}
