<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagMapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tag_maps', function (Blueprint $table) {
            $table->id();
            $table->integer('tag_id')->unsigned();
            $table->integer('article_id')->unsigned();
            $table->timestamps();
            $table->foreign('tag_id')
                ->references('id')
                ->on('tags')
                ->onDelete('cascade');
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
        Schema::drop('tag_maps');
    }
}
