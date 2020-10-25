<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * 分享－资源关联表数据库迁移
 * Class CreateSharingResourceTable
 */
class CreateSharingResourceTable extends Migration
{
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up()
		{
				Schema::create('sharing_resource', function(Blueprint $table)
				{
						$table->increments('id');
						$table->unsignedInteger('sharing_id')->comment('外键，分享id');
						$table->unsignedInteger('resource_id')->comment('外键，资源id');
				});

				Schema::table('sharing_resource', function($table)
				{
						$table->foreign('sharing_id')->references('id')->on('sharings')->onDelete('cascade');
						$table->foreign('resource_id')->references('id')->on('resources')->onDelete('cascade');
				});
		}

		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down()
		{
				Schema::drop('sharing_resource');
		}

}
