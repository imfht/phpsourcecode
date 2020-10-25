<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_files', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('did')->nullable()->default(0)->comment('对话ID');
            $table->string('group', 100)->nullable()->default('')->comment('群组');
            $table->string('name', 100)->nullable()->default('')->comment('文件名称');
            $table->integer('size')->nullable()->default(0)->comment('文件大小(B)');
            $table->string('ext', 20)->nullable()->default('')->comment('文件格式');
            $table->string('path')->nullable()->default('')->comment('文件地址');
            $table->string('thumb')->nullable()->default('')->comment('缩略图');
            $table->string('username')->nullable()->default('')->comment('上传用户');
            $table->bigInteger('indate')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_files');
    }
}
