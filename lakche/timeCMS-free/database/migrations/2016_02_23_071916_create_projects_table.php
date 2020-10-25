<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->comment('项目名称');
            $table->string('info')->comment('项目简介');
            $table->text('text')->comment('项目详情');
            $table->string('cover')->comment('项目封面');
            $table->string('thumb')->comment('项目封面微缩图');
            $table->unsignedInteger('category_id')->comment('分类ID');
            $table->integer('sort')->comment('排序');
            $table->integer('is_recommend')->comment('是否推荐');
            $table->json('tag')->comment('标签');
            $table->integer('is_show')->comment('是否显示');
            $table->integer('views')->comment('浏览量');
            $table->string('url')->comment('外链网址');

            $table->string('keywords')->comment('seo关键字');
            $table->string('description')->comment('seo描述');

            $table->json('speed')->comment('项目进度');
            $table->double('cost', 11, 2)->comment('项目费用');
            $table->integer('period')->comment('项目周期');
            $table->json('person_id')->comment('参与人员');

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
        Schema::dropIfExists('projects');
    }
}
