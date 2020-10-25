<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * 通知表notifications,包含
 *     id:(表主键)
 *     type_id:(表外键,通知类型id,和通知类型表notifyTypes主键映射)
 *     title:(通知标题,例:项目更新通知)
 *     content:(通知内容,例:用户a删除了项目b)
 *     trigger_id:(表外键,通知触发者id,和用户表users主键映射)
 *     source_id:(表外键,通知源id,和通知类型表notifyTypes中map字段对应模型表主键映射,例:通知类型为项目通知,则映射为projects主键)
 * 六个字段.
 *     project_id: 该通知所属于的项目, 如果为０则是系统通知.
 */

class CreateNotificationsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('type_id')->unsigned();
            $table->foreign('type_id')->references('id')->on('notifyTypes')->onDelete('cascade');
            $table->string('title', 50);
            $table->string('content', 255);
            $table->integer('trigger_id')->unsigned();
            $table->foreign('trigger_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('source_id')->unsigned()->nullable();
            $table->unsignedInteger('project_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
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
        Schema::drop('notifications');
    }
}

