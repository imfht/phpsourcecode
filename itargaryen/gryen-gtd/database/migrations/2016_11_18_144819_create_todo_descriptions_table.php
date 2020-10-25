<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTodoDescriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('todo_descriptions', function (Blueprint $table) {
            $table->id();
            $table->integer('todo_id')->unsigned();
            $table->string('content');
            $table->timestamps();
            $table->foreign('todo_id')
                ->references('id')
                ->on('todos')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('todo_descriptions');
    }
}
