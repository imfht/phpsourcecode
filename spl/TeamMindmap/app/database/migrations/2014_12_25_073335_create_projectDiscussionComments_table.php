<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectDiscussionCommentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('projectDiscussionComments', function(Blueprint $table)
		{
			$table->increments('id');

            $table->unsignedInteger('projectDiscussion_id');
            $table->foreign('projectDiscussion_id')->references('id')->on('projectDiscussions')->onDelete('cascade');
            $table->string('content', 255);
            $table->unsignedInteger('creater_id');
            $table->foreign('creater_id')->references('id')->on('users')->onDelete('cascade');

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('projectDiscussionComments');
	}

}
