<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * 分享－标签表数据库迁移
 * Class CreateSharingTagTable
 */
class CreateSharingTagTable extends Migration
{
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up()
		{
				Schema::create('sharing_tag', function(Blueprint $table)
				{
						$table->increments('id');
						$table->unsignedInteger('sharing_id')->comment('外键，分享id');
						$table->unsignedInteger('tag_id')->comment('资源标签id');
				});

				Schema::table('sharing_tag', function($table)
				{
						$table->foreign('sharing_id')->references('id')->on('sharings')->onDelete('cascade');
						$table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
				});
		}

		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down()
		{
				Schema::drop('sharing_tag');
		}

}
