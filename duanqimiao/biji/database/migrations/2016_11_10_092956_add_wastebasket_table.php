<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWastebasketTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bijis', function (Blueprint $table) {
            $table->boolean("wastebasket")->after("share");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bijis', function (Blueprint $table) {
            $table->boolean("wastebasket");
        });
    }
}
