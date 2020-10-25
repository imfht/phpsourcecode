<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('check_records', function (Blueprint $table) {
            $table->id();
            $table->string('check_item');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->integer('user_id');
            $table->integer('status')->default(0);
            $table->string('creator');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('check_records');
    }
}
