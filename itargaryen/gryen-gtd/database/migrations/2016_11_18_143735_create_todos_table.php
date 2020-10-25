<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTodosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('todos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parents_id')->default(-1);
            $table->integer('grandparents_id')->default(-1);
            $table->string('content');
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('importance');
            $table->timestamp('begin_at')->nullable();
            $table->timestamp('end_at')->nullable();
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
        Schema::drop('todos');
    }
}
