<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSummaryToArticlesTable extends Migration {

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
			$table->text('bodyhtml')->nullable();
			$table->text('summaryhtml')->nullable();
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
			$table->dropColumn('bodyhtml');
			$table->dropColumn('summaryhtml');
		});

	}

}
