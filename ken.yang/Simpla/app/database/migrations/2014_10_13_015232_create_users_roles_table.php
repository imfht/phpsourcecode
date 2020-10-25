<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersRolesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        //创建表
        Schema::create('users_roles', function($table) {
            $table->integer('uid')->references('id')->on('users');
            $table->integer('rid')->unsigned()->default(2)->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('users_roles');
    }

}
