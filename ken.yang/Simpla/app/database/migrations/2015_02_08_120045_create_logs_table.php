<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('logs', function($table) {
            $table->increments('id', 11);
            $table->string('uid', 11); //操作人ID
            $table->enum('type', array('add', 'edit', 'delete', 'login', 'register', 'other'));
            $table->text('message')->nullable(); //操作内容
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('logs');
    }

}
