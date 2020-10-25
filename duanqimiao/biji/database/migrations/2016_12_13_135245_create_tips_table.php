<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tips', function (Blueprint $table) {
            $table->increments('id');

            $table->string('reporter_name');//举报人

            $table->integer('reported_id')->unsigned();//被举报人id

            $table->string('reported_name');//被举报人姓名

            $table->integer('biji_id')->unsigned();//被举报笔记id

            $table->string('biji_title');//被举报笔记标题

            $table->string('cause');//举报原因

            $table->boolean("handle");//处理情况

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
        //
    }
}
