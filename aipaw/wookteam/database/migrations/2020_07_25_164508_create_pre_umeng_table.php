<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreUmengTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('umeng', function(Blueprint $table)
		{
			$table->string('token', 64)->default('')->unique('IDEX_token');
			$table->string('username', 100)->nullable()->default('')->index('IDEX_username');
			$table->string('platform', 50)->nullable()->default('');
			$table->bigInteger('update')->nullable()->default(0);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('umeng');
	}
}
