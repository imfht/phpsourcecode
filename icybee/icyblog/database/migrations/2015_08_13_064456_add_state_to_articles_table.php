<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStateToArticlesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::table('articles',function(Blueprint $table)
		{
			$table->enum('state', array('posted', 'draft'))->default('posted');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
		Schema::table('articles', function(Blueprint $table)
		{
			$table->dropColumn('state');
		});
	}

}
