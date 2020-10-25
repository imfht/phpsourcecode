<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('phone');
            $table->string('type')->default('O')->commit('R注册，L登录,I邀请通讯录,O其它');
            $table->string('code');
            $table->ipAddress('ip');
            $table->timestamps();

            $table->index('phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms');
    }
}
