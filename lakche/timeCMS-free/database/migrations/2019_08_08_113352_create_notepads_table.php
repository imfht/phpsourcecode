<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotepadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notepads', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id')->comment('用户ID');
            $table->text('notepad')->comment('笔记内容');
            $table->integer('is_private')->comment('保密级别，0.不加密，1.简单加密，2.绝密');
            $table->string('key')->comment('密钥，保密等级1时验证');
            $table->json('tag')->comment('标签');
            $table->string('code')->comment('分享码');
            $table->string('hash')->comment('hash');

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
        Schema::dropIfExists('notepads');
    }
}
