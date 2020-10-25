<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMenus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('菜单名称');
            $table->string('url')->comment('菜单网址');
            $table->integer('position')->comment('位置');
            $table->integer('sort')->comment('排序');
            $table->integer('is_open')->comment('是否开放浏览');
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
        Schema::dropIfExists('menus');
    }
}
