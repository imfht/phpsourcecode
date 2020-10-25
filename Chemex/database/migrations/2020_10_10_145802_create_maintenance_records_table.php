<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();
            $table->string('item');
            $table->integer('item_id');
            $table->string('ng_description');
            $table->string('ok_description')->nullable();
            $table->dateTime('ng_time');
            $table->dateTime('ok_time')->nullable();
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
        Schema::dropIfExists('maintenance_records');
    }
}
