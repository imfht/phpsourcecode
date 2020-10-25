<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMobileConnectUserListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobile_connect_user_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->default(0);
            $table->integer('register_id')->default(0)->comment('大于0表示已经注册过了');
            $table->string('name')->default('');
            $table->string('phone')->default('');
            $table->integer('last_invited_at')->default(0)->comment('最近一次邀请时间,时间戳');
            $table->string('first_alpha')->default('')->comment('用户姓名的拼音的第一个字母，用于筛选');
            $table->timestamps();

            $table->index('user_id');
            $table->index('register_id');
            $table->index('phone');
            $table->index('first_alpha');
            $table->index('last_invited_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mobile_connect_user_lists');
    }
}
