<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSeoToCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('keywords')->comment('seo关键字');
            $table->string('description')->comment('seo描述');
            $table->string('templet_all')->comment('带子分类模板');
            $table->string('templet_nosub')->comment('不带子分类模板');
            $table->string('templet_article')->comment('文章模板');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('keywords');
            $table->dropColumn('description');
            $table->dropColumn('templet_all');
            $table->dropColumn('templet_nosub');
            $table->dropColumn('templet_article');
        });
    }
}
