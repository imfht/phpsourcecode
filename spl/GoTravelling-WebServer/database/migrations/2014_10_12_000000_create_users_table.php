<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
    {
        //MongoDB下实际上无效果
        $table->increments('id');

        /*
         * 用户名或电话号码可以作为用户登录时使用的唯一标志
         */
        $table->string('username', 60);
        $table->string('cellphone_number', 11);
        $table->unique(['username', 'cellphone_number']);

        $table->string('email', 60);
        $table->string('nickname', 60);
        $table->string('sex', 4);

        $table->string('password', 60);
        $table->string('description', 255);
        $table->string('head_image');

        //暂时设定为可任意填写
        $table->string('address');

        $table->json('favor_places');

        $table->rememberToken();
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
		Schema::drop('users');
	}

}
