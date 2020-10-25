<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPreUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('az', 10)->after('token')->nullable()->default('')->index('IDEX_az')->comment('A-Z');
        });
        //
        $lists = \App\Module\Base::DBC2A(DB::table('users')->select(['id','username','nickname'])->where('az', '')->get());
        foreach ($lists AS $item) {
            DB::table('users')->where('id', $item['id'])->update([
                'az' => \App\Module\Base::getFirstCharter($item['nickname'] ?: $item['username'])
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('az');
        });
    }
}
