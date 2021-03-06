<?php

declare(strict_types=1);

/*
 * This file is part of the your app package.
 *
 * The PHP Application For Code Poem For You.
 * (c) 2018-2099 http://yourdomian.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Phinx\Migration\AbstractMigration;

final class Resource extends AbstractMigration
{
    public function up(): void
    {
        $this->struct();
        $this->seed();
    }

    public function down(): void
    {
        $this->table('resource')->drop()->save();
    }

    private function struct(): void
    {
        $sql = <<<'EOT'
            CREATE TABLE `resource` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
                `name` varchar(64) NOT NULL DEFAULT '' COMMENT '资源名字',
                `num` varchar(64) NOT NULL DEFAULT '' COMMENT '编号',
                `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态 0=禁用;1=启用;',
                `create_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                `update_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
                `delete_at` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间 0=未删除;大于0=删除时间;',
                `create_account` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '创建账号',
                `update_account` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '更新账号',
                `version` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '操作版本号',
                PRIMARY KEY (`id`),
                UNIQUE KEY `uniq_num` (`num`,`delete_at`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='资源';
            EOT;
        $this->execute($sql);
    }

    private function seed(): void
    {
        $sql = <<<'EOT'
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (2, '资源列表', 'get:resource', 1, '2018-12-08 13:00:38', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (3, '权限列表', 'get:permission', 1, '2018-12-08 13:00:52', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (4, '角色列表', 'get:role', 1, '2018-12-08 13:01:18', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (5, '用户列表', 'get:user', 1, '2018-12-08 13:01:32', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (7, '资源保存', 'post:resource', 1, '2018-12-08 13:05:31', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (8, '资源更新', 'put:resource/*', 1, '2018-12-08 13:05:47', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (9, '资源删除', 'delete:resource/*', 1, '2018-12-08 13:06:21', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (10, '资源状态', 'post:resource/status', 1, '2018-12-08 13:07:33', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (11, '权限保存', 'post:permission', 1, '2018-12-08 13:10:37', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (12, '权限更新', 'put:permission/*', 1, '2018-12-08 13:10:59', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (13, '权限删除', 'delete:permission/*', 1, '2018-12-08 13:11:19', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (14, '权限状态', 'post:permission/status', 1, '2018-12-08 13:11:39', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (15, '角色保存', 'post:role', 1, '2018-12-08 13:13:53', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (16, '角色更新', 'put:role/*', 1, '2018-12-08 13:14:05', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (17, '角色状态', 'post:role/status', 1, '2018-12-08 13:14:17', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (18, '角色删除', 'delete:role/*', 1, '2018-12-08 13:14:42', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (19, '用户保存', 'post:user', 1, '2018-12-08 13:15:56', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (20, '用户更新', 'put:user/*', 1, '2018-12-08 13:16:06', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (21, '用户状态', 'post:user/status', 1, '2018-12-08 13:16:16', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (22, '用户删除', 'delete:user/*', 1, '2018-12-08 13:16:29', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (25, '系统配置', 'get:base/get-option', 1, '2018-12-08 13:00:03', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (26, '超级管理员', '*', 1, '2018-12-08 13:00:03', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (28, '更新配置', 'post:base/option', 1, '2019-01-31 02:05:26', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (29, '用户管理菜单', 'user_index_menu', 1, '2019-01-31 02:34:10', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (30, '权限管理菜单', 'permission_index_menu', 1, '2019-01-31 02:34:34', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (31, '资源管理菜单', 'resource_index_menu', 1, '2019-01-31 02:35:12', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (32, '角色管理菜单', 'role_index_menu', 1, '2019-01-31 02:35:31', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (33, '系统配置菜单', 'option_index_menu', 1, '2019-01-31 02:36:43', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (34, '个人中心菜单', 'profile_index_menu', 1, '2019-01-31 02:37:00', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (35, '基础配置一级菜单', 'base_menu', 1, '2019-01-31 02:38:12', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (36, '权限管理一级菜单', 'permission_menu', 1, '2019-01-31 02:38:48', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (37, '测试一级菜单', 'test_menu', 1, '2019-01-31 02:39:13', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (38, '测试菜单', 'test_index_menu', 1, '2019-01-31 02:39:30', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (40, '用户编辑按钮', 'user_edit_button', 1, '2019-01-30 18:35:48', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (41, '用户删除按钮', 'user_delete_button', 1, '2019-01-30 18:36:04', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (42, '用户新增按钮', 'user_add_button', 1, '2019-01-30 18:36:31', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (43, '角色编辑按钮', 'role_edit_button', 1, '2019-01-30 18:37:14', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (44, '角色授权按钮', 'role_permission_button', 1, '2019-01-30 18:37:33', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (45, '角色删除按钮', 'role_delete_button', 1, '2019-01-30 18:38:22', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (46, '角色新增按钮', 'role_add_button', 1, '2019-01-30 18:38:48', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (47, '资源编辑按钮', 'resource_edit_button', 1, '2019-01-30 18:39:25', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (48, '资源删除按钮', 'resource_delete_button', 1, '2019-01-30 18:39:39', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (49, '资源新增按钮', 'resource_add_button', 1, '2019-01-30 18:40:01', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (50, '权限新增按钮', 'permission_add_button', 1, '2019-01-30 18:40:41', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (51, '权限编辑按钮', 'permission_edit_button', 1, '2019-01-30 18:40:57', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (52, '权限资源授权按钮', 'permission_resource_button', 1, '2019-01-30 18:41:13', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (53, '权限删除按钮', 'permission_delete_button', 1, '2019-01-30 18:41:29', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (54, '资源状态按钮', 'resource_status_button', 1, '2019-01-30 20:40:45', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (55, '角色状态按钮', 'role_status_button', 1, '2019-01-30 20:40:56', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (56, '权限状态按钮', 'permission_status_button', 1, '2019-01-30 20:41:13', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (57, '用户状态按钮', 'user_status_button', 1, '2019-01-30 20:41:53', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (58, '角色详情', 'get:role/*', 1, '2019-01-31 09:49:07', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (59, '权限详情', 'get:permission/*', 1, '2019-01-31 09:49:35', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (60, '角色授权', 'post:role/permission', 1, '2019-01-31 09:51:42', '2019-08-25 21:19:23', 0, 0, 0);
            INSERT INTO `resource`(`id`, `name`, `num`, `status`, `create_at`, `update_at`, `delete_at`, `create_account`, `update_account`) VALUES (61, '权限资源授权', 'post:permission/resource', 1, '2019-01-31 09:52:12', '2019-08-25 21:19:23', 0, 0, 0);
            EOT;
        $this->execute($sql);
    }
}
