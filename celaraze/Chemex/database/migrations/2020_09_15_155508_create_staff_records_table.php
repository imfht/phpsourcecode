<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_records', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('department_id');
            $table->char('gender')->default('无');
            $table->string('title')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
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
        Schema::dropIfExists('staff_records');
    }
}
