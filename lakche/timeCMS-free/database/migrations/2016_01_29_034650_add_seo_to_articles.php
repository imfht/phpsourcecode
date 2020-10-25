<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSeoToArticles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('subtitle')->comment('副标题');
            $table->string('keywords')->comment('seo关键字');
            $table->string('description')->comment('seo描述');
            $table->string('author')->comment('文章作者');
            $table->string('source')->comment('文章来源');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('subtitle');
            $table->dropColumn('keywords');
            $table->dropColumn('description');
            $table->dropColumn('author');
            $table->dropColumn('source');
        });
    }
}
