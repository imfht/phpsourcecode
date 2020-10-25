<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPreDocsContent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('docs_content', function (Blueprint $table) {
            $table->text('text')->after('content')->nullable()->comment('内容（主要用于文档类型搜索）');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('docs_content', function (Blueprint $table) {
            $table->dropColumn('text');
        });
    }
}
