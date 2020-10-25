<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * 资源外链表数据库迁移
 * Class CreateResourceLinksTable
 */
class CreateResourceLinksTable extends Migration
{
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up()
		{
				Schema::create('resourceLinks', function(Blueprint $table)
				{
						$table->increments('id');
						$table->string('description', 50)->comment('起源外链描述');
						$table->string('resource_ids', 50)->comment('起源id集合，采用|分割转化为各资源id');
						$table->string('link', 255)->comment('外链链接');
						$table->string('fetch_code', 32)->comment('提取码');
				});
		}

		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down()
		{
				Schema::drop('resourceLinks');
		}

}
