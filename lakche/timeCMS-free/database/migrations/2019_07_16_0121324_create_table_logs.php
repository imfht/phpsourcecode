<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id')->comment('用户ID');
            $table->string('module')->comment('模型');
            $table->integer('module_id')->comment('模型对应项目ID');
            $table->string('operation')->comment('操作');
            $table->string('info')->comment('说明');
            $table->string('ip')->comment('ip');

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
        Schema::dropIfExists('logs');
    }
}
