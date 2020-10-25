<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSoftwareRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('software_records', function (Blueprint $table) {
            $table->id();
            $table->string('name'); //软件名称
            $table->string('description')->nullable();  //软件描述
            $table->integer('category_id'); //软件分类
            $table->string('version');  //版本
            $table->integer('vendor_id');   //制造商
            $table->double('price')->nullable();    //价格
            $table->date('purchased')->nullable();   //购买日
            $table->date('expired')->nullable(); //有效期
            $table->char('distribution')->default('u');   //分发方式,u未知，o开源，f免费，b商业
            $table->string('sn')->nullable();   //序列号
            $table->integer('counts')->default(1);  //授权数量
            $table->string('creator');
            $table->softDeletes();
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
        Schema::dropIfExists('software_records');
    }
}
