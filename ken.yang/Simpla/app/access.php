<?php

/*
 * 核心权限路由控制列表
 */

$list = array();

/**
 * ---------------------------------------
 * 管理员
 * ---------------------------------------
 */
$list['admin'] = array(
    'siderbar' => '',
    'title' => '管理员',
    'class' => '',
    'list' => array(
        array('as' => 'admin_login', 'title' => '管理员登陆', 'description' => ''),
        array('as' => 'admin', 'title' => '管理首页', 'description' => ''),
    )
);

/**
 * ---------------------------------------
 * 用户
 * ---------------------------------------
 */
$list['admin_user'] = array(
    'title' => '用户管理',
    'siderbar' => 'user',
    'class' => 'fa-users',
    'list' => array(
        array('as' => 'admin_user', 'title' => '用户列表', 'weight' => '1', 'description' => ''),
        array('as' => 'admin_user_add', 'title' => '添加用户', 'weight' => '2', 'description' => ''),
        array('as' => 'admin_user_edit', 'title' => '编辑用户', 'description' => ''),
        array('as' => 'admin_user_delete', 'title' => '删除用户', 'description' => ''),
        array('as' => 'admin_user_info', 'title' => '用户信息', 'description' => ''),
        array('as' => 'admin_user_ip_lock', 'title' => '锁定IP', 'description' => '')
    )
);

//用户角色
$list['admin_user_roles'] = array(
    'title' => '用户角色管理',
    'siderbar' => 'user',
    'class' => 'fa-users',
    'list' => array(
        array('as' => 'admin_user_roles', 'title' => '用户角色', 'weight' => '3', 'description' => ''),
        array('as' => 'admin_user_roles_add', 'title' => '添加用户角色', 'description' => ''),
        array('as' => 'admin_user_roles_edit', 'title' => '编辑用户角色', 'description' => ''),
        array('as' => 'admin_user_roles_delete', 'title' => '删除用户角色', 'description' => ''),
        array('as' => 'admin_user_roles_permission', 'title' => '用户角色权限管理', 'description' => '')
    )
);



/**
 * ---------------------------------------
 * 内容
 * ---------------------------------------
 */
$list['admin_node'] = array(
    'title' => '内容管理',
    'siderbar' => 'node',
    'class' => 'fa-files-o',
    'list' => array(
        array('as' => 'admin_node', 'title' => '内容列表', 'weight' => '1', 'description' => ''),
        //array('as' => 'admin_node_list', 'title' => '内容列表', 'description' => ''),
        array('as' => 'admin_node_add', 'title' => '添加内容列表', 'weight' => '2', 'description' => ''),
        array('as' => 'admin_node_add_type', 'title' => '添加内容', 'description' => ''),
        array('as' => 'admin_node_edit', 'title' => '编辑内容', 'description' => ''),
        array('as' => 'admin_node_delete', 'title' => '删除内容', 'description' => '')
    )
);
//内容类型
$list['admin_node_type'] = array(
    'title' => '内容类型管理',
    'siderbar' => 'node',
    'class' => 'fa-files-o',
    'list' => array(
        array('as' => 'admin_node_type', 'title' => '内容类型', 'weight' => '3', 'description' => ''),
        array('as' => 'admin_node_type_add', 'title' => '添加内容类型', 'weight' => '4', 'description' => ''),
        array('as' => 'admin_node_type_edit', 'title' => '编辑内容类型', 'description' => ''),
        array('as' => 'admin_node_type_field_add', 'title' => '添加内容类型字段', 'description' => ''),
        array('as' => 'admin_node_type_field_edit', 'title' => '编辑内容类型字段', 'description' => ''),
        array('as' => 'admin_node_type_field_name_edit', 'title' => '编辑字段', 'description' => ''),
        array('as' => 'admin_node_type_field_name_config', 'title' => '字段配置', 'description' => ''),
        array('as' => 'admin_node_type_field_delete', 'title' => '删除字段', 'description' => ''),
        array('as' => 'admin_node_type_display', 'title' => '展示内容类型', 'description' => ''),
        array('as' => 'admin_node_type_delete', 'title' => '删除内容类型', 'description' => '')
    )
);

/**
 * ---------------------------------------
 * 分类
 * ---------------------------------------
 */
$list['admin_category'] = array(
    'title' => '分类管理',
    'siderbar' => 'category',
    'class' => 'fa-sitemap',
    'list' => array(
        array('as' => 'admin_category', 'title' => '分类列表', 'weight' => '1', 'description' => ''),
        array('as' => 'admin_category_add', 'title' => '添加分类', 'description' => ''),
        array('as' => 'admin_category_edit', 'title' => '编辑分类', 'description' => ''),
        array('as' => 'admin_category_delete', 'title' => '删除分类', 'description' => '')
    )
);
//分类类型
$list['admin_category_type'] = array(
    'title' => '分类类型管理',
    'siderbar' => 'category',
    'class' => 'fa-sitemap',
    'list' => array(
        array('as' => 'admin_category_type_add', 'title' => '添加分类类型', 'weight' => '2', 'description' => ''),
        array('as' => 'admin_category_type_list', 'title' => '分类类型列表', 'description' => ''),
        array('as' => 'admin_category_type_edit', 'title' => '编辑分类类型', 'description' => ''),
        array('as' => 'admin_category_type_delete', 'title' => '删除分类类型', 'description' => ''),
        array('as' => 'admin_category_type_weight', 'title' => '编辑分类类型权重', 'description' => '')
    )
);

/**
 * ---------------------------------------
 * 区块
 * ---------------------------------------
 */
$list['admin_block'] = array(
    'title' => '区块管理',
    'siderbar' => 'block',
    'class' => 'fa-table',
    'list' => array(
        array('as' => 'admin_block', 'title' => '区块列表', 'weight' => '1', 'description' => ''),
        array('as' => 'admin_block_add', 'title' => '添加区块', 'weight' => '2', 'description' => ''),
        array('as' => 'admin_block_edit', 'title' => '编辑区块', 'description' => ''),
        array('as' => 'admin_block_delete', 'title' => '删除区块', 'description' => ''),
        array('as' => 'admin_block_refresh', 'title' => '区块刷新', 'description' => '')
    )
);
//区块区域
$list['admin_block_area'] = array(
    'title' => '区块区域管理',
    'siderbar' => 'block',
    'class' => 'fa-table',
    'list' => array(
        array('as' => 'admin_block_area_add', 'title' => '添加区块区域', 'weight' => '3', 'description' => ''),
        array('as' => 'admin_block_area_edit', 'title' => '编辑区块区域', 'description' => ''),
        array('as' => 'admin_block_area_delete', 'title' => '删除区块区域', 'description' => '')
    )
);

/**
 * ---------------------------------------
 * 菜单
 * ---------------------------------------
 */
$list['admin_menu'] = array(
    'title' => '菜单管理',
    'siderbar' => 'menu',
    'class' => 'fa-bar-chart-o',
    'list' => array(
        array('as' => 'admin_menu', 'title' => '菜单列表', 'weight' => '1', 'description' => ''),
        array('as' => 'admin_menu_add', 'title' => '添加菜单', 'description' => ''),
        array('as' => 'admin_menu_edit', 'title' => '编辑菜单', 'description' => ''),
        array('as' => 'admin_menu_delete', 'title' => '删除菜单', 'description' => '')
    )
);
//菜单类型
$list['admin_menu_type'] = array(
    'title' => '菜单类型管理',
    'siderbar' => 'menu',
    'class' => 'fa-bar-chart-o',
    'list' => array(
        array('as' => 'admin_menu_type_add', 'title' => '添加菜单类型', 'weight' => '2', 'description' => ''),
        array('as' => 'admin_menu_type_list', 'title' => '菜单类型列表', 'description' => ''),
        array('as' => 'admin_menu_type_edit', 'title' => '编辑菜单类型', 'description' => ''),
        array('as' => 'admin_menu_type_delete', 'title' => '删除菜单类型', 'description' => ''),
        array('as' => 'admin_menu_type_weight', 'title' => '编辑菜单类型权重', 'description' => '')
    )
);


/**
 * ---------------------------------------
 * 设置
 * ---------------------------------------
 */
$list['admin_setting'] = array(
    'title' => '设置管理',
    'siderbar' => 'setting',
    'class' => 'fa-wrench',
    'list' => array(
        array('as' => 'admin_setting', 'title' => '站点设置', 'weight' => '1', 'description' => ''),
        array('as' => 'admin_setting_add', 'title' => '全局设置', 'description' => ''),
        array('as' => 'admin_setting_theme', 'title' => '主题管理', 'weight' => '2', 'description' => ''),
        array('as' => 'admin_setting_module', 'title' => '模块管理', 'weight' => '3', 'description' => ''),
        array('as' => 'admin_setting_cache', 'title' => '缓存设置', 'weight' => '4', 'description' => ''),
        array('as' => 'admin_setting_cache_clear', 'title' => '清除缓存', 'description' => ''),
        array('as' => 'admin_setting_seo', 'title' => 'SEO设置', 'weight' => '5', 'description' => ''),
        array('as' => 'admin_setting_comment', 'title' => '评论设置', 'weight' => '6', 'description' => ''),
        array('as' => 'admin_setting_comment_name', 'title' => '第三方评论', 'description' => ''),
        array('as' => 'admin_setting_email', 'title' => 'SMTP邮件设置', 'weight' => '7', 'description' => ''),
        array('as' => 'admin_setting_email_test', 'title' => '发送测试邮件', 'description' => ''),
        array('as' => 'admin_setting_maintenance', 'title' => '站点离线管理', 'description' => '')
    )
);

/**
 * -------------------------------------
 * 友情连接
 * ---------------------------------------
 */
$list['admin_link'] = array(
    'title' => '友情连接',
    'siderbar' => 'link',
    'class' => 'fa-link',
    'list' => array(
        array('as' => 'admin_link', 'title' => '友情连接列表', 'weight' => '1', 'description' => ''),
        array('as' => 'admin_link_add', 'title' => '添加友情连接', 'weight' => '2', 'description' => ''),
        array('as' => 'admin_link_edit', 'title' => '编辑友情连接', 'description' => ''),
        array('as' => 'admin_link_delete', 'title' => '删除友情连接', 'description' => ''),
    )
);

/**
 * ----------------------------------------
 * 报告
 * ----------------------------------------
 */
$list['admin_report'] = array(
    'title' => '报告管理',
    'siderbar' => 'report',
    'class' => 'fa-file-text-o',
    'list' => array(
        array('as' => 'admin_report', 'title' => '报告', 'description' => ''),
        array('as' => 'admin_report_register_count', 'title' => '注册用户统计', 'weight' => '1', 'description' => ''),
        array('as' => 'admin_report_node_count', 'title' => '内容发布统计', 'weight' => '2', 'description' => ''),
        array('as' => 'admin_report_logs', 'title' => '用户操作日志', 'weight' => '3', 'description' => '')
    )
);

return $list;
