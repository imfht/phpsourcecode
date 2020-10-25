<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * 分享标签表数据库迁移
 * Class CreateTagsTable
 */
class CreateTagsTable extends Migration
{

		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up()
		{
				Schema::create('tags', function(Blueprint $table)
				{
						$table->increments('id');
						$table->string('name', 30)->comment('资源标签名');
						$table->unsignedInteger('project_id')->comment('外键，项目id');

						$table->timestamps();
				});


				Schema::table('tags', function(Blueprint $table)
				{
						$table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
						$table->unique(['id', 'name'], 'project_tag');
						$table->index('name');
				});
		}

		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down()
		{
				Schema::drop('tags');
		}

}
