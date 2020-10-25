<?php

$modelMenu = include('model_menu.php');

if (false === $modelMenu) {
    $modelMenu = array();
}

// 菜单项配置
$systemMenu = array(
    // 后台首页
    'Index' => array(
        'name' => '首页',
        'target' => 'Index/index',
        'sub_menu' => array(
            array('item' => array('Index/index' => '系统信息')),
            array('item' => array('Index/editPassword' => '修改密码')),
          /*  array('item' => array('Index/siteEdit' => '站点信息')),*/
           /* array('item' => array('Cache/index' => '清除缓存'))*/
        )
    ),

    // 缓存管理
    'Cache' => array(
        'name' => '缓存管理',
        'target' => 'Cache/index',
        'mapping' => 'Index',
        'sub_menu' => array(
            array('item' => array('Cache/index' => '缓存列表'))
        )
    ),

    // 数据管理
//    'Admins' => array(
//        'name' => '管理员权限',
//        'target' => 'Admins/index',
//        'sub_menu' => array(
//            array('item' => array('Admins/index' => '管理员信息')),
//            array('item' => array('Roles/index' => '角色管理')),
//            array('item' => array('Nodes/index' => '节点管理')),
//            array('item' => array('Admins/add' => '添加管理员')),
//            array('item' => array('Roles/add' => '添加角色')),
//            array('item'=>array('Admins/edit'=>'编辑管理员信息'),'hidden'=>true),
//            array('item' => array('Roles/edit'=>'编辑角色信息'),'hidden'=>true)
//        )
//    ),

    // 角色管理
//    'Roles' => array(
//        'name' => '角色管理',
//        'target' => 'Roles/index',
//        'mapping' => 'Admins',
//        'sub_menu' => array(
//            array('item' => array('Roles/index' => '角色列表')),
//            array('item' => array('Roles/add' => '添加角色')),
//            array('item' => array('Roles/edit' => '编辑角色信息'),'hidden'=>true),
//            array('item' => array('Roles/assignAccess' => '分配权限'),
//                  'hidden'=>true)
//        )
//    ),

    // 节点管理
//    'Nodes' => array(
//        'name' => '节点管理',
//        'target' => 'Nodes/index',
//        'mapping' => 'Admins',
//        'sub_menu' => array(
//            array('item' => array('Nodes/index' => '节点列表'))
//        )
//    ),



    // 数据管理
   /* 'Data' => array(
        'name' => '数据管理',
        'target' => 'Data/backup',
        'sub_menu' => array(
            array('item' => array('Data/backup' => '数据备份')),
        )
    ),*/

    'Category' => array(
        'name' => '栏目管理',
        'target' => 'Category/index',
        'sub_menu' => array(
            array('item' => array('Category/index' => '栏目列表')),
            array('item' => array('Category/add' => '添加栏目')),
            array('item' => array('Category/edit' => '编辑栏目'),'hidden' => true),
        )
    ),
    'Article' => array(
        'name' => '文章管理',
        'target' => 'Article/index'
    ),
    /*'Job' => array(
        'name' => '招聘管理',
        'target' => 'Job/index',
        'sub_menu' => array(
            array('item' => array('Job/index' => '招聘信息列表')),
            array('item' => array('Job/add' => '添加招聘信息')),
            array('item' => array('Job/edit' => '编辑招聘信息'),'hidden' => true),
        )
    ),*/

    /*'Resume' => array(
        'name' => '简历信息',
        'target' => 'Resume/index',
        'sub_menu' => array(
            array('item' => array('Resume/index' => '简历信息')),
        )
    ),*/
    'Product' => array(
        'name' => '产品管理',
        'target' => 'Product/index',
        'sub_menu' => array(
            array('item' => array('Product/index' => '产品列表')),
            array('item' => array('Product/add' => '添加产品')),
            array('item' => array('Product/edit' => '编辑产品'),'hidden' => true),
        )
    ),
    'Siteinfo' => array(
        'name' => '网站信息',
        'target' => 'Siteinfo/index',
        'sub_menu' => array(
            array('item' => array('Siteinfo/index' => '网站信息设置')),
        )
    ),
);

return array_merge($systemMenu, $modelMenu);
