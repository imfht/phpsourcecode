<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleLabelsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('article_labels', function(Blueprint $table)
		{

			$table->increments('id');
			$table->integer('articleid')->unsigned();
			$table->string('label');
			$table->timestamps();
		});

		Schema::table('article_labels', function(Blueprint $table)
		{
			$table->foreign('articleid')->references('id')->on('articles')->onDelete('cascade');
		});

		Schema::table('article_labels', function(Blueprint $table)
		{
			$table->foreign('label')->references('name')->on('labels')->onDelete('cascade');
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('article_labels');
	}

}
