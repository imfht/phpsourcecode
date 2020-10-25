<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',32)->comment('用户名');
            $table->string('password')->comment('密码');
            $table->string('avatr',128)->nullable()->comment('头像');
            $table->integer('login_count')->default(0)->comment('登录次数');
            $table->string('create_ip',128)->nullable()->comment('注册ip');
            $table->string('last_login_ip',128)->nullable()->comment('最后登录IP');
            $table->tinyInteger('status')->index()->default(1)->comment('状态: 1 正常, 2=>禁止');
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
        Schema::dropIfExists('admins');
    }
}
