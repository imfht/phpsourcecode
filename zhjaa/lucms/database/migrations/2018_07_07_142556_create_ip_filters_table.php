<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIpFiltersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ip_filters', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->enum('type', ['white', 'black'])->default('white')->comment('类型');
            $table->string('ip')->default('')->comment('IP');
            $table->timestamps();
            $table->index('type');
            $table->unique('ip');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ip_filters');
    }
}
