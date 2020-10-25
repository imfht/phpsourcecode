<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableArticles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->comment('文章标题');
            $table->string('info')->comment('文章简介');
            $table->text('description')->comment('文章详情');
            $table->string('cover')->comment('文章封面');
            $table->string('thumb')->comment('文章封面微缩图');
            $table->unsignedInteger('category_id')->comment('分类ID');
            $table->integer('sort')->comment('排序');
            $table->integer('is_recommend')->comment('是否推荐');
            $table->json('tag')->comment('标签');
            $table->integer('is_show')->comment('是否显示');
            $table->integer('views')->comment('浏览量');
            $table->string('url')->comment('外链网址');

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
        Schema::dropIfExists('articles');
    }
}
