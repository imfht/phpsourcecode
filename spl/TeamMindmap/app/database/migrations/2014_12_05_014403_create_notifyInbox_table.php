<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * 通知接收表,包含
 *     id:(表主键)
 *     notification_id:(表外键,通知id,和通知表notifications主键映射)
 *     receiver_id:(表外键,通知接收者id,和用户表users主键映射)
 *     read:(通知是否已读标识)
 * 四个字段.
 *
 */
class CreateNotifyInboxTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifyInbox', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('notification_id')->unsigned();
            $table->foreign('notification_id')->references('id')->on('notifications')->onDelete('cascade');
            $table->integer('receiver_id')->unsigned();
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
            $table->boolean('read')->default(false);
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
        Schema::drop('notifyInbox');
    }
}
