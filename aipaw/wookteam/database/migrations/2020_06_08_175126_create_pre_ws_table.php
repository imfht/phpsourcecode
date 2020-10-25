<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreWsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ws', function(Blueprint $table)
		{
			$table->string('key', 50)->default('')->unique('IDEX_key');
			$table->string('fd', 50)->nullable()->default('');
			$table->string('username', 100)->nullable()->default('')->index('IDEX_username');
			$table->string('channel', 50)->nullable()->default('');
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
		Schema::drop('ws');
	}
}
