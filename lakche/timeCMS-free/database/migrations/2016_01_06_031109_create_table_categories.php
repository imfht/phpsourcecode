<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->comment('分类标题');
            $table->integer('sort')->comment('分类排序');
            $table->string('info')->comment('分类说明');
            $table->string('cover')->comment('分类封面');
            $table->string('thumb')->comment('分类封面微缩图');
            $table->unsignedInteger('parent_id')->comment('父节点');
            $table->unsignedInteger('root_id')->comment('根节点');
            $table->integer('is_nav_show')->comment('导航栏是否显示');

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
        Schema::dropIfExists('categories');
    }
}
