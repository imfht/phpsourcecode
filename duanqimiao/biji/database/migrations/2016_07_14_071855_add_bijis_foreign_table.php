<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBijisForeignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bijis', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->after('id');
            $table->integer('book_id')->unsigned()->after('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('book_id')->references('id')->on('books');
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
            $table->integer('user_id');
        });
    }
}
