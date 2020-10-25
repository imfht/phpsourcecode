<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPhoneToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->default('')->comment('手机号');
            $table->string('weixin_openid')->default('');
            $table->string('mini_openid')->default('')->comment('小程序 openid');
            $table->string('weixin_unionid')->default('');
            $table->string('weixin_session_key')->default('');
            $table->string('weixin_head_image_path')->default('')->comment('微信头像路径');
            $table->string('country')->default('');
            $table->string('province')->default('');
            $table->string('city')->default('');

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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone');
            $table->dropColumn('weixin_openid');
            $table->dropColumn('mini_openid');
            $table->dropColumn('weixin_unionid');
            $table->dropColumn('weixin_session_key');
            $table->dropColumn('weixin_head_image_path');
            $table->dropColumn('country');
            $table->dropColumn('province');
            $table->dropColumn('city');
        });
    }
}
