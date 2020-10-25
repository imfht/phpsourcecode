<?php
/**
 * Created by PhpStorm.
 * User: xu.gao
 * Date: 2016/1/28
 * Time: 16:08
 */

namespace console\models;


use backend\models\AuthItem\AuthItem;
use backend\models\AuthItem\AuthItemChild;

class RoleSeeder {

    /**
     * 初始化角色
     */
    public static function initRole(){

        $authItem               = new AuthItem();
        $authItem->name         = 'admin';
        $authItem->type         = '1';
        $authItem->description  = '管理员';
        $authItem->save();

    }

    /**
     * 初始化权限
     */
    public static function initPermission(){


        $authItem               = new AuthItem();
        $authItem->name         = 'admin.main.main';
        $authItem->type         = '2';
        $authItem->description  = '主界面';
        $authItem->typename     = '基本操作';
        $authItem->save();
        $childs                 = new AuthItemChild();
        $childs->parent         = 'admin';
        $childs->child          = 'admin.main.main';
        $childs->save();

        $authItem               = new AuthItem();
        $authItem->name         = 'admin.user.logout';
        $authItem->type         = '2';
        $authItem->description  = '退出登录';
        $authItem->typename     = '基本操作';
        $authItem->save();
        $childs                 = new AuthItemChild();
        $childs->parent         = 'admin';
        $childs->child          = 'admin.user.logout';
        $childs->save();

        $authItem               = new AuthItem();
        $authItem->name         = 'admin.auth.authlogin';
        $authItem->type         = '2';
        $authItem->description  = '用户登录';
        $authItem->typename     = '基本操作';
        $authItem->save();
        $childs                 = new AuthItemChild();
        $childs->parent         = 'admin';
        $childs->child          = 'admin.auth.authlogin';
        $childs->save();
        //菜单权限
        $authItem               = new AuthItem();
        $authItem->name         = 'admin.menu.manage';
        $authItem->type         = '2';
        $authItem->description  = '菜单管理';
        $authItem->typename     = '菜单权限';
        $authItem->save();
        $childs                 = new AuthItemChild();
        $childs->parent         = 'admin';
        $childs->child          = 'admin.menu.manage';
        $childs->save();

        $authItem               = new AuthItem();
        $authItem->name         = 'admin.menu.menulist';
        $authItem->type         = '2';
        $authItem->description  = '菜单列表';
        $authItem->typename     = '菜单权限';
        $authItem->save();
        $childs                 = new AuthItemChild();
        $childs->parent         = 'admin';
        $childs->child          = 'admin.menu.menulist';
        $childs->save();

        $authItem               = new AuthItem();
        $authItem->name         = 'admin.menu.menuadd';
        $authItem->type         = '2';
        $authItem->description  = '添加菜单';
        $authItem->typename     = '菜单权限';
        $authItem->save();
        $childs                 = new AuthItemChild();
        $childs->parent         = 'admin';
        $childs->child          = 'admin.menu.menuadd';
        $childs->save();

        $authItem               = new AuthItem();
        $authItem->name         = 'admin.menu.menuupdate';
        $authItem->type         = '2';
        $authItem->description  = '更新菜单';
        $authItem->typename     = '菜单权限';
        $authItem->save();
        $childs                 = new AuthItemChild();
        $childs->parent         = 'admin';
        $childs->child          = 'admin.menu.menuupdate';
        $childs->save();

        $authItem               = new AuthItem();
        $authItem->name         = 'admin.menu.menudelete';
        $authItem->type         = '2';
        $authItem->description  = '删除菜单';
        $authItem->typename     = '菜单权限';
        $authItem->save();
        $childs                 = new AuthItemChild();
        $childs->parent         = 'admin';
        $childs->child          = 'admin.menu.menudelete';
        $childs->save();

        //用户权限
        $authItem               = new AuthItem();
        $authItem->name         = 'admin.user.usermanage';
        $authItem->type         = '2';
        $authItem->description  = '用户管理';
        $authItem->typename     = '用户权限';
        $authItem->save();
        $childs                 = new AuthItemChild();
        $childs->parent         = 'admin';
        $childs->child          = 'admin.user.usermanage';
        $childs->save();

        $authItem               = new AuthItem();
        $authItem->name         = 'admin.user.userlist';
        $authItem->type         = '2';
        $authItem->description  = '用户列表';
        $authItem->typename     = '用户权限';
        $authItem->save();
        $childs                 = new AuthItemChild();
        $childs->parent         = 'admin';
        $childs->child          = 'admin.user.userlist';
        $childs->save();

        $authItem               = new AuthItem();
        $authItem->name         = 'admin.user.useradd';
        $authItem->type         = '2';
        $authItem->description  = '添加用户';
        $authItem->typename     = '用户权限';
        $authItem->save();
        $childs                 = new AuthItemChild();
        $childs->parent         = 'admin';
        $childs->child          = 'admin.user.useradd';
        $childs->save();

        $authItem               = new AuthItem();
        $authItem->name         = 'admin.user.userupdate';
        $authItem->type         = '2';
        $authItem->description  = '更新用户';
        $authItem->typename     = '用户权限';
        $authItem->save();
        $childs                 = new AuthItemChild();
        $childs->parent         = 'admin';
        $childs->child          = 'admin.user.userupdate';
        $childs->save();

        $authItem               = new AuthItem();
        $authItem->name         = 'admin.user.userdelete';
        $authItem->type         = '2';
        $authItem->description  = '删除用户';
        $authItem->typename     = '用户权限';
        $authItem->save();
        $childs                 = new AuthItemChild();
        $childs->parent         = 'admin';
        $childs->child          = 'admin.user.userdelete';
        $childs->save();
        //角色权限
        $authItem               = new AuthItem();
        $authItem->name         = 'admin.role.rolemanage';
        $authItem->type         = '2';
        $authItem->description  = '角色管理';
        $authItem->typename     = '角色权限';
        $authItem->save();
        $childs                 = new AuthItemChild();
        $childs->parent         = 'admin';
        $childs->child          = 'admin.role.rolemanage';
        $childs->save();

        $authItem               = new AuthItem();
        $authItem->name         = 'admin.role.rolelist';
        $authItem->type         = '2';
        $authItem->description  = '角色列表';
        $authItem->typename     = '角色权限';
        $authItem->save();
        $childs                 = new AuthItemChild();
        $childs->parent         = 'admin';
        $childs->child          = 'admin.role.rolelist';
        $childs->save();

        $authItem               = new AuthItem();
        $authItem->name         = 'admin.role.roleadd';
        $authItem->type         = '2';
        $authItem->description  = '添加角色';
        $authItem->typename     = '角色权限';
        $authItem->save();
        $childs                 = new AuthItemChild();
        $childs->parent         = 'admin';
        $childs->child          = 'admin.role.roleadd';
        $childs->save();



        $authItem               = new AuthItem();
        $authItem->name         = 'admin.role.roleupdate';
        $authItem->type         = '2';
        $authItem->description  = '更新角色';
        $authItem->typename     = '角色权限';
        $authItem->save();
        $childs                 = new AuthItemChild();
        $childs->parent         = 'admin';
        $childs->child          = 'admin.role.roleupdate';
        $childs->save();

        $authItem               = new AuthItem();
        $authItem->name         = 'admin.role.roledelete';
        $authItem->type         = '2';
        $authItem->description  = '删除角色';
        $authItem->typename     = '角色权限';
        $authItem->save();
        $childs                 = new AuthItemChild();
        $childs->parent         = 'admin';
        $childs->child          = 'admin.role.roledelete';
        $childs->save();

        $authItem               = new AuthItem();
        $authItem->name         = 'admin.role.rolepermission';
        $authItem->type         = '2';
        $authItem->description  = '更新权限';
        $authItem->typename     = '角色权限';
        $authItem->save();
        $childs                 = new AuthItemChild();
        $childs->parent         = 'admin';
        $childs->child          = 'admin.role.rolepermission';
        $childs->save();

        //权限
        $authItem               = new AuthItem();
        $authItem->name         = 'admin.permission.permissionmanage';
        $authItem->type         = '2';
        $authItem->description  = '权限管理';
        $authItem->typename     = '管理权限';
        $authItem->save();
        $childs                 = new AuthItemChild();
        $childs->parent         = 'admin';
        $childs->child          = 'admin.permission.permissionmanage';
        $childs->save();

        $authItem               = new AuthItem();
        $authItem->name         = 'admin.permission.permissionlist';
        $authItem->type         = '2';
        $authItem->description  = '权限列表';
        $authItem->typename     = '管理权限';
        $authItem->save();
        $childs                 = new AuthItemChild();
        $childs->parent         = 'admin';
        $childs->child          = 'admin.permission.permissionlist';
        $childs->save();

        $authItem               = new AuthItem();
        $authItem->name         = 'admin.permission.permissionadd';
        $authItem->type         = '2';
        $authItem->description  = '添加权限';
        $authItem->typename     = '管理权限';
        $authItem->save();
        $childs                 = new AuthItemChild();
        $childs->parent         = 'admin';
        $childs->child          = 'admin.permission.permissionadd';
        $childs->save();

        $authItem               = new AuthItem();
        $authItem->name         = 'admin.permission.permissionupdate';
        $authItem->type         = '2';
        $authItem->description  = '更新权限';
        $authItem->typename     = '管理权限';
        $authItem->save();
        $childs                 = new AuthItemChild();
        $childs->parent         = 'admin';
        $childs->child          = 'admin.permission.permissionupdate';
        $childs->save();

        $authItem               = new AuthItem();
        $authItem->name         = 'admin.permission.permissiondelete';
        $authItem->type         = '2';
        $authItem->description  = '删除权限';
        $authItem->typename     = '管理权限';
        $authItem->save();
        $childs                 = new AuthItemChild();
        $childs->parent         = 'admin';
        $childs->child          = 'admin.permission.permissiondelete';
        $childs->save();
    }

}