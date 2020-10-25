<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->default(0)->comment('发给哪个管理员的消息,0为所有管理员');
            $table->integer('user_id')->default(0)->comment('哪个用户发的消息，对应 app 用户表');
            $table->string('title')->default('');
            $table->enum('type', ['SY', 'FK'])->default('SY');
            $table->string('content', 2000)->default('');
            $table->enum('status', ['U', 'R'])->default('U')->comment('消息状态');
            $table->timestamps();

            $table->index('status');
            $table->index('type');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_messages');
    }
}
