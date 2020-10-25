<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminAuthLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_admins_auth_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admins_id')->unsigned();
            $table->string('platform_info')->comment('登录系统信息');
            $table->string('browser_info')->comment('登录浏览器信息');
            $table->string('ip_address', 20)->comment('登录IP地址');
            $table->timestamp('login_time')->nullable()->comment('登录时间');

            $table->index('admins_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_admins_auth_logs');
    }
}
