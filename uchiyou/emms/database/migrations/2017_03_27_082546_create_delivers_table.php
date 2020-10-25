<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mm_delivers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('using_record_id');
            $table->char('phone',11);
            $table->string('address');
            $table->tinyInteger('status');
            $table->text('user_comment');
            $table->text('deliver_comment');
            $table->integer('deliver_man_id');
            $table->tinyInteger('delete')->default(1);
            $table->timestamp('arrival_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mm_delivers');
    }
}
