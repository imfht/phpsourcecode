<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('sys_admins')) {
            Schema::create('sys_admins', function (Blueprint $table) {
                $table->increments('id');
                $table->string('email', 255)->unique()->comment('登录账号');
                $table->string('nickname', 30)->comment('显示名称');
                $table->string('password', 255)->comment('密码');
                $table->tinyInteger('active')->default(1)->comment('状态（0，1）');
                $table->timestamps();
                $table->string('remember_token', 255)->default('');
            });
        }

        if (!Schema::hasTable('sys_menus')) {
            Schema::create('sys_menus', function (Blueprint $table) {
                $table->increments('id');
                $table->tinyInteger('pid')->comment('父级ID');
                $table->char('name', 30)->comment('唯一标识');
                $table->string('display_name', 255)->comment('菜单显示名');
                $table->string('uri', 100)->comment('菜单链接地址');
                $table->integer('sort')->comment('排序');
                $table->timestamps();
                $table->unique('name', 'sys_menus_name_unique');
            });
        }

        if (!Schema::hasTable('sys_permissions')) {
            Schema::create('sys_permissions', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('pid')->comment('父级ID');
                $table->string('name', 255)->comment('权限路由名称');
                $table->string('display_name', 255)->comment('权限展示名称');
                $table->integer('sort')->comment('排序值');
                $table->timestamps();

                $table->unique('name', 'unique_name');
            });
        }

        if (!Schema::hasTable('sys_roles')) {
            Schema::create('sys_roles', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 100)->comment('角色标识');
                $table->string('display_name', 20)->comment('角色展示名称');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('sys_roles_admins')) {
            Schema::create('sys_roles_admins', function (Blueprint $table) {
                $table->integer('admins_id')->unsigned();
                $table->integer('roles_id')->unsigned();

                $table->foreign('admins_id')->references('id')->on('sys_admins')->onUpdate('cascade')->onDelete('cascade');

                $table->foreign('roles_id')->references('id')->on('sys_roles')->onUpdate('cascade')->onDelete('cascade');

                $table->primary(['admins_id', 'roles_id']);
            });
        }

        if (!Schema::hasTable('sys_roles_permissions')) {
            Schema::create('sys_roles_permissions', function (Blueprint $table) {
                $table->integer('roles_id')->unsigned();
                $table->integer('permissions_id')->unsigned();

                $table->foreign('permissions_id')->references('id')->on('sys_permissions')->onDelete('cascade')->onUpdate('cascade');

                $table->foreign('roles_id')->references('id')->on('sys_roles')->onDelete('cascade')->onUpdate('cascade');

                $table->primary(['roles_id', 'permissions_id']);

            });
        }

        if (!Schema::hasTable('sys_roles_menus')) {
            Schema::create('sys_roles_menus', function (Blueprint $table) {
                $table->integer('roles_id')->unsigned();
                $table->integer('menus_id')->unsigned();

                $table->foreign('menus_id')->references('id')->on('sys_menus')->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('roles_id')->references('id')->on('sys_roles')->onDelete('cascade')->onUpdate('cascade');

                $table->primary(['roles_id', 'menus_id']);

            });
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sys_admins');
        Schema::drop('sys_menus');
        Schema::drop('sys_permissions');
        Schema::drop('sys_roles');
        Schema::drop('sys_roles_admins');
        Schema::drop('sys_roles_permissions');
        Schema::drop('sys_roles_menus');
    }
}
