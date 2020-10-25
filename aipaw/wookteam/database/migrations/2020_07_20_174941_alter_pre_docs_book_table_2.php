<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPreDocsBookTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('docs_book', function (Blueprint $table) {
            $table->string('role_look', 20)->after('role_edit')->nullable()->default('edit')->index('IDEX_role_look');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('docs_book', function (Blueprint $table) {
            $table->dropColumn('role_look');
        });
    }
}
