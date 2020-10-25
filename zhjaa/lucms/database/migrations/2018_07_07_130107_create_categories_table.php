<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name')->default('')->comment('分类名称');
            $table->integer('cover_image')->default(0)->comment('封面图片');
            $table->string('description')->default('')->comment('描述');
            $table->integer('weight')->default(10);
            $table->timestamps();
            $table->index('cover_image');
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
