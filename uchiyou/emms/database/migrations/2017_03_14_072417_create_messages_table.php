<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mm_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('type');
            $table->integer('sender_id');
            $table->integer('recipient_id');// 接受者
            $table->text('content');
            $table->smallInteger('status');
            $table->smallInteger('action');// 需要采取的动作，0 表示此信息接受者没有动作，
            $table->text('addition');// 如果是图片，则保存 url,若是多个，则切割多个 url;
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
        Schema::dropIfExists('mm_messages');
    }
}
