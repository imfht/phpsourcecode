<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableComments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('article_id')->comment('文章ID');
            $table->string('user_id')->comment('用户ID');
            $table->integer('comment_id')->comment('留言ID');
            $table->string('name')->comment('姓名');
            $table->string('phone')->comment('联系方式');
            $table->integer('is_show')->comment('是否显示');
            $table->integer('is_open')->comment('是否审核');
            $table->text('info')->comment('留言内容');
            $table->string('hash')->comment('hash');
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
        Schema::dropIfExists('comments');
    }
}
