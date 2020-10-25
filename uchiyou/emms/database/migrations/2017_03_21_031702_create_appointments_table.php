<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mm_appointments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('company_id');
            $table->integer('material_id');
            $table->timestamp('start_time')->default(Date('Y-m-d h:i:s'));
            $table->timestamp('finish_time')->default(Date('Y-m-d h:i:s'));
            $table->smallInteger('status');
            $table->tinyInteger('delete')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointments');
    }
}
