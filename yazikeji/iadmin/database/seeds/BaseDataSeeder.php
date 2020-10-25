<?php

use Illuminate\Database\Seeder;

class BaseDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $prefix = Config::get('database.connections.mysql.prefix');

        //插入默认菜单数据
        DB::insert("INSERT INTO `${prefix}sys_roles` (`id`, `name`, `display_name`, `created_at`, `updated_at`) VALUES (1,'super_admin','超级管理员组','2017-03-27 01:28:14','2017-03-27 01:28:14')");

        DB::insert("INSERT INTO `${prefix}sys_permissions`
            (`id`, `pid`, `name`, `display_name`, `sort`, `created_at`, `updated_at`)
            VALUES
            (22,0,'admin.home','后台首页',0,'2017-03-31 02:32:00','2017-03-31 02:32:00'),
            (23,0,'#','系统设置',0,'2017-03-31 02:32:25','2017-03-31 02:32:25'),
            (25,23,'admin.index','菜单管理',0,'2017-03-31 02:36:08','2017-03-31 02:36:08'),
            (26,25,'admin.create','新增菜单页面',0,'2017-03-31 02:36:30','2017-03-31 02:36:30'),
            (27,25,'admin.store','新增菜单动作',0,'2017-03-31 02:36:55','2017-03-31 02:36:55'),
            (28,25,'admin.edit','编辑菜单页面',0,'2017-03-31 02:37:18','2017-03-31 02:37:18'),
            (29,25,'menus.update','编辑菜单提交',0,'2017-03-31 02:37:50','2017-03-31 02:37:50'),
            (30,25,'admin.destroy','删除菜单',0,'2017-03-31 02:38:19','2017-03-31 02:38:19'),
            (31,23,'roles.index','角色管理',0,'2017-03-31 02:39:38','2017-03-31 02:39:38'),
            (32,31,'roles.store','添加角色动作',0,'2017-03-31 02:40:56','2017-03-31 02:40:56'),
            (33,31,'roles.destroy','删除角色',0,'2017-03-31 02:41:22','2017-03-31 02:41:22'),
            (34,31,'roles.permissions','分配权限页面',0,'2017-03-31 02:41:57','2017-03-31 02:41:57'),
            (35,31,'roles.perm.store','分配权限提交',0,'2017-03-31 02:42:23','2017-03-31 02:42:23'),
            (36,31,'roles.users','角色成员列表',0,'2017-03-31 02:43:16','2017-03-31 02:43:16'),
            (37,23,'admins.index','后台管理员管理',0,'2017-03-31 02:44:16','2017-03-31 02:44:16'),
            (38,37,'admins.create','新增管理员页面',0,'2017-03-31 02:44:59','2017-03-31 02:44:59'),
            (39,37,'admins.store','新增管理员提交',0,'2017-03-31 02:45:40','2017-03-31 02:45:40'),
            (40,37,'admins.destroy','删除管理员',0,'2017-03-31 02:47:00','2017-03-31 02:47:00'),
            (41,37,'admins.active','禁用/启用管理员',0,'2017-03-31 02:47:24','2017-03-31 02:47:24'),
            (42,37,'admins.edit','编辑管理员页面',0,'2017-03-31 02:48:56','2017-03-31 02:48:56'),
            (43,37,'admins.update','编辑管理员提交',0,'2017-03-31 02:49:14','2017-03-31 02:49:14')");

        DB::insert("INSERT INTO `${prefix}sys_menus`
            (`id`, `pid`, `name`, `display_name`, `uri`, `sort`, `created_at`)
            VALUES
            (1,0,'Admin.HomeController','后台首页','admin.home',0,'2017-03-24 10:12:32'),
            (4,0,'Admin.SystemController','系统设置','#',0,'2017-03-24 10:12:32'),
            (26,4,'Admin.MenusController','菜单管理','menus.index',0,'2017-03-24 10:12:32'),
            (27,4,'Admin.RolesController','角色管理','roles.index',0,'2017-03-24 10:12:32'),
            (30,4,'Admin.PermissionsController','权限管理','permissions.index',0,'2017-03-24 10:12:32'),
            (31,4,'Admin.AdminsController','管理员管理','admins.index',1,'2017-03-24 10:12:32')");
    }
}
