<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->increments('id')->unsigned('');
            $table->integer('user_id')->default(0);
            $table->enum('type', ['C', 'U', 'R', 'D', 'L', 'O'])->default('O')->comment('日志所属操作类型:模型 CURD 操作,后台登录,其它操作');
            $table->string('table_name')->default('')->comment('表名：articles');
            $table->ipAddress('ip')->default('')->comment('IP');
            $table->text('content')->comment('日志内容,json_encode([data=>insert into ... ,message=>添加数据)');
            $table->timestamps();
            $table->index('user_id');
            $table->index('type');
            $table->index('table_name');
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
