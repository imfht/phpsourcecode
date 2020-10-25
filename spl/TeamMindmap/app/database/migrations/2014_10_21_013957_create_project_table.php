<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
         * 此数据库表对应于项目信息
        */
        Schema::create('projects', function(Blueprint $table){
            $table->increments('id');		//主键id

            $table->string('name');		//项目名
            $table->string('cover')->nullable();	//项目封面图像
            $table->string('introduction');	//项目介绍

            $table->integer('creater_id')->unsigned();	//创建者id
            $table->foreign('creater_id')->references('id')->on('users')->onDelete('cascade');	//创建者id的外键约束

            $table->timestamps();	//更新和创建时间
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('projects');
    }
}
