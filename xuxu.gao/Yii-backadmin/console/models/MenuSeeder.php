<?php
/**
 * Created by PhpStorm.
 * User: xu.gao
 * Date: 2016/1/25
 * Time: 9:47
 */

namespace console\models;


use backend\models\Menu\Menu;

class MenuSeeder{


    public static function initMenu(){

        //菜单操作
        $menu = new Menu();
        $menu->parent_id    = 0;
        $menu->name         = '菜单管理';
        $menu->slug         = 'admin.menu.manage';
        $menu->url          = 'menu';//控制器的名字,主要是结合路由实现菜单的折叠效果
        $menu->description  = '菜单管理';
        $menu->save();
        $menuList = new Menu();
        $menuList->parent_id    = $menu->id;
        $menuList->name         = '菜单列表';
        $menuList->slug         = 'admin.menu.menulist';
        $menuList->url          = '/Admin/menu/menulist';
        $menuList->description  = '菜单列表';
        $menuList->save();
        $menuList = new Menu();
        $menuList->parent_id    = $menu->id;
        $menuList->name         = '添加菜单';
        $menuList->slug         = 'admin.menu.menuadd';
        $menuList->url          = '/Admin/menu/menuadd';
        $menuList->description  = '添加菜单';
        $menuList->save();
        //用户操作
        $userMenu = new Menu();
        $userMenu->parent_id   = 0;
        $userMenu->name        = '用户管理';
        $userMenu->slug        = 'admin.user.usermanage';
        $userMenu->url         = 'user';
        $userMenu->description = '用户管理';
        $userMenu->save();

        $userMenuList = new Menu();
        $userMenuList->parent_id   = $userMenu->id;
        $userMenuList->name        = '用户列表';
        $userMenuList->slug        = 'admin.user.userlist';
        $userMenuList->url         = '/Admin/user/userlist';
        $userMenuList->description = '用户列表';
        $userMenuList->save();

        $userMenuList = new Menu();
        $userMenuList->parent_id   = $userMenu->id;
        $userMenuList->name        = '添加用户';
        $userMenuList->slug        = 'admin.user.useradd';
        $userMenuList->url         = '/Admin/user/useradd';
        $userMenuList->description = '添加用户';
        $userMenuList->save();

        //角色操作
        $roleMenu = new Menu();
        $roleMenu->parent_id   = 0;
        $roleMenu->name        = '角色管理';
        $roleMenu->slug        = 'admin.role.rolemanage';
        $roleMenu->url         = 'role';
        $roleMenu->description = '角色管理';
        $roleMenu->save();

        $roleMenuList = new Menu();
        $roleMenuList->parent_id   = $roleMenu->id;
        $roleMenuList->name        = '角色列表';
        $roleMenuList->slug        = 'admin.role.rolelist';
        $roleMenuList->url         = '/Admin/role/rolelist';
        $roleMenuList->description = '角色列表';
        $roleMenuList->save();

        $roleMenuList = new Menu();
        $roleMenuList->parent_id   = $roleMenu->id;
        $roleMenuList->name        = '添加角色';
        $roleMenuList->slug        = 'admin.role.roleadd';
        $roleMenuList->url         = '/Admin/role/roleadd';
        $roleMenuList->description = '添加角色';
        $roleMenuList->save();

        //权限操作
        $permisssionMenu = new Menu();
        $permisssionMenu->parent_id   = 0;
        $permisssionMenu->name        = '权限管理';
        $permisssionMenu->slug        = 'admin.permission.permissionmanage';
        $permisssionMenu->url         = 'permission';
        $permisssionMenu->description = '权限管理';
        $permisssionMenu->save();

        $permisssionList = new Menu();
        $permisssionList->parent_id   = $permisssionMenu->id;
        $permisssionList->name        = '权限列表';
        $permisssionList->slug        = 'admin.permission.permissionlist';
        $permisssionList->url         = '/Admin/permission/permissionlist';
        $permisssionList->description = '权限列表';
        $permisssionList->save();

        $permisssionList = new Menu();
        $permisssionList->parent_id   = $permisssionMenu->id;
        $permisssionList->name        = '添加权限';
        $permisssionList->slug        = 'admin.permission.permissionadd';
        $permisssionList->url         = '/Admin/permission/permissionadd';
        $permisssionList->description = '添加权限';
        $permisssionList->save();




    }

}