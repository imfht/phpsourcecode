<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('persons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('姓名');
            $table->string('head')->comment('头像');
            $table->string('head_thumbnail')->comment('头像微缩图');
            $table->string('info')->comment('简介');
            $table->string('title')->comment('头衔');
            $table->text('text')->comment('详情');
            $table->json('tag')->comment('特长');
            $table->integer('sex')->comment('性别');
            $table->integer('sort')->comment('排序');
            $table->integer('point')->comment('贡献度');
            $table->integer('age')->comment('从业时间');
            $table->integer('is_recommend')->comment('是否推荐');
            $table->integer('is_show')->comment('是否显示');
            $table->string('url')->comment('外部链接');
            $table->string('keywords')->comment('seo关键字');
            $table->string('description')->comment('seo描述');

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
        Schema::dropIfExists('persons');
    }
}
