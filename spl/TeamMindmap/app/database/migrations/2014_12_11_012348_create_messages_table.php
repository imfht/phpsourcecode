<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateMessagesTable
 *
 * 用户私信发送表
 */
class CreateMessagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('messages', function(Blueprint $table){
			$table->increments('id')->comment('表主键');
			$table->string('title', 40)->comment('私信标题');
			$table->unsignedInteger('sender_id')->comment('发送者id');
			$table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
			$table->text('content')->comment('私信内容');

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
		Schema::drop('messages');
	}

}
