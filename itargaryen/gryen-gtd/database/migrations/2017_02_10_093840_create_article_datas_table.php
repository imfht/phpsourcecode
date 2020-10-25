<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_datas', function (Blueprint $table) {
            $table->id();
            $table->integer('article_id')->unsigned();
            $table->text('content');
            $table->foreign('article_id')
                ->references('id')
                ->on('articles')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('article_datas');
    }
}
