<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InitDatas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $admin = new \App\Models\Role();
        $admin->name         = 'admin';
        $admin->display_name = '管理员'; 
        $admin->description  = '可以管理整个系统'; 
        $admin->save();
        $user=new \App\Models\User();
        $user->name='admin';
        $user->email='admin@example.com';
        $user->password = \Hash::make('admin');
        $user->save();
        $user->attachRole($admin);
		
		
		$adminRole = \App\Models\Role::find(1);
        $permission=new \App\Models\Permission();
        $permission->name='permission_manage';
        $permission->display_name='模块管理';
        $permission->description='模块管理';
        $permission->save();
        $adminRole->attachPermission($permission);
        $permission=new \App\Models\Permission();
        $permission->name='role_manage';
        $permission->display_name='角色管理';
        $permission->description='管理角色';
        $permission->save();
        $adminRole->attachPermission($permission);
        $permission=new \App\Models\Permission();
        $permission->name='user_manage';
        $permission->display_name='管理用户';
        $permission->description='新建删除用户';
        $permission->save();
        $adminRole->attachPermission($permission);
        $permission=new \App\Models\Permission();
        $permission->name='role_user';
        $permission->display_name='用户角色';
        $permission->description='为用户添加角色';
        $permission->save();
        $adminRole->attachPermission($permission);
        $permission=new \App\Models\Permission();
        $permission->name='permission_role';
        $permission->display_name='角色权限';
        $permission->description='为角色增加权限';
        $permission->save();
        $adminRole->attachPermission($permission);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
