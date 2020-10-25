<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreDocsUsersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('docs_users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('bookid')->nullable()->default(0)->index('IDEX_bookid')->comment('项目ID');
			$table->string('username', 100)->nullable()->default('')->index('IDEX_username')->comment('关系用户名');
			$table->bigInteger('indate')->nullable()->default(0)->comment('添加时间');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('docs_users');
	}
}
