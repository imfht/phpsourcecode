<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function(Blueprint $table){
            $table->increments('id');
            $table->string('username', 50);
            $table->string('password', 70);
            $table->string('email', 50);
            $table->string('description', 255)->nullable();
            $table->string('head_image', 20)->default('default.png');
            $table->boolean('active')->default('1');
            $table->string('remember_token', 60)->nullable();
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
        Schema::drop('users');
    }
}
