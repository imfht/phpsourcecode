<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        //创建表
        Schema::create('users', function($table) {
            $table->increments('id', 11);
            $table->string('username', 64);
            $table->string('password', 64);
            $table->string('email', 64);
            $table->enum('status', array(0, 1))->default(1);
            $table->string('picture', 128)->default('upload/default/default.png');
            $table->timestamps();
            $table->string('remember_token', 256);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('users');
    }

}
