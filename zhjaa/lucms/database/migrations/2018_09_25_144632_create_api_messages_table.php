<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->default(0)->comment('哪个管理员发的消息');
            $table->integer('user_id')->default(0)->comment('发给哪个用户的消息,0为所有管理员');
            $table->string('title')->default('');
            $table->string('content')->default('');
            $table->string('url')->default('')->comment('跳转url');
            $table->enum('status', ['U', 'R'])->default('U')->comment('消息状态');
            $table->enum('type', ['SY'])->default('SY');
            $table->enum('is_alert_at_home', ['F', 'T'])->default('F')->comment('是否在首页弹出提示框，已读后就不再弹出');
            $table->timestamps();

            $table->index('status');
            $table->index('type');
            $table->index('user_id');
            $table->index('is_alert_at_home');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_messages');
    }
}
