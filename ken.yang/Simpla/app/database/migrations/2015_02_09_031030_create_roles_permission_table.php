<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesPermissionTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('roles_permission', function($table) {
            $table->integer('rid')->references('id')->on('roles');
            $table->string('name', 64); //机器名字
            $table->INTEGER('weight')->unsigned()->default(0); //权限位置
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('roles_permission');
    }

}