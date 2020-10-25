<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_issues', function (Blueprint $table) {
            $table->id();
            $table->integer('service_id');
            $table->string('issue');
            $table->integer('status');
            $table->dateTime('start');
            $table->dateTime('end')->nullable();
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
        Schema::dropIfExists('service_issues');
    }
}
