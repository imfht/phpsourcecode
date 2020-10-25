<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPreDocsSectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('docs_section', function (Blueprint $table) {
            $table->string('lockname', 100)->after('type')->nullable()->default('')->comment('锁定会员');
            $table->bigInteger('lockdate')->after('lockname')->nullable()->default(0)->comment('锁定时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('docs_section', function (Blueprint $table) {
            $table->dropColumn('lockname');
            $table->dropColumn('lockdate');
        });
    }
}
