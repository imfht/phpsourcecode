W<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPreDocsBookTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('docs_book', function (Blueprint $table) {
            //
            $table->string('role_edit', 20)->after('title')->nullable()->default('reg')->index('IDEX_role_edit');
            $table->string('role_view', 20)->after('role_edit')->nullable()->default('all')->index('IDEX_role_view');
            $table->text('setting')->after('role_view')->nullable();
        });
        //
        $upArray = [
            'role_edit' => 'reg',
            'role_view' => 'all'
        ];
        DB::table('docs_book')->update(array_merge($upArray, [
            'setting' => \App\Module\Base::array2string($upArray)
        ]));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('docs_book', function (Blueprint $table) {
            $table->dropColumn('role_edit');
            $table->dropColumn('role_view');
            $table->dropColumn('setting');
        });
    }
}
