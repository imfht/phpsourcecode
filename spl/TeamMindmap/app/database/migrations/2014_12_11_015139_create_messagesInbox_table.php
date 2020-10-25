<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesInboxTable extends Migration {

	/**
	 * Run the migrations.
	 * 用户私信接受表
	 * @return void
	 */
	public function up()
	{
		Schema::create('messagesInboxs', function(Blueprint $table)
		{
			$table->increments('id')->comment('表主键');

			$table->unsignedInteger('message_id')->comment('表外键,私信id');
			$table->foreign('message_id')->references('id')->on('messages')->onDelete('cascade');
			$table->unsignedInteger('receiver_id')->comment('表外键,私信接受者id');
			$table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
			$table->boolean('read')->default(0)->comment('是否已读标识');

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
		Schema::drop('messagesInboxs');
	}

}
