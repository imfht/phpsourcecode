<?php

return array(
//'配置项'=>'配置值'
    /* 数据库设置 */
    'DB_TYPE' => '[DB_TYPE]', // 数据库类型
    'DB_HOST' => '[DB_HOST]', // 服务器地址
    'DB_NAME' => '[DB_NAME]', // 数据库名
    'DB_USER' => '[DB_USER]', // 用户名
    'DB_PWD' => '[DB_PWD]', // 密码
    'DB_PORT' => '[DB_PORT]', // 端口
    'DB_PREFIX' => '[DB_PREFIX]', // 数据库表前缀
    'DEFAULT_MODULE'        =>  'Ajax',

//权限验证
    'RBAC_SUPERADMIN' => '[RBAC_SUPERADMIN]', //超级管理员
    'ADMIN_AUTH_KEY' => 'ADMIN_AUTH_KEY', //超级管理员识别
    'USER_AUTH_ON' => true, //是否开启
    'USER_AUTH_TYPE' => 1, //1登陆验证 2时时验证
    'USER_AUTH_KEY' => 'uid', //用户识别号
    'NOT_AUTH_MOUDULE' => 'Cache,Ueditor,Freshen,SiteMap,Admin', //'无需验证的控制器',
    'NOT_AUTH_ACTION' => 'main,sale,help,pc,mobile,adminIndex,createXML,createHtml', //'无需验证的方法',
    'RBAC_ROLE_TABLE' => '[DB_PREFIX]role', //角色表',
    'RBAC_USER_TABLE' => '[DB_PREFIX]role_user', //角色跟用户的中间表
    'RBAC_ACCESS_TABLE' => '[DB_PREFIX]access', //权限表名称
    'RBAC_NODE_TABLE' => '[DB_PREFIX]node', //节点表名称

    'sitePageNum' => '15', //分页显示数量
//伪静态 1为pathinfo 3为兼容模式
    'URL_MODEL' => 3,
    'URL_HTML_SUFFIX' => 'html',
    //安全措施
    'DEFAULT_FILTER' => 'htmlspecialchars,trim',
    'COOKIE_KEY' => 'fc0bd22cbc588b608d8f8019765a10da', //COOKIE密钥
    'VERITY_KEY' => 'clmao', //万能验证码
//提示文字
   
     /* 模板引擎设置 */
    'TMPL_CONTENT_TYPE'     =>  'text/html', // 默认模板输出类型
    'TMPL_ACTION_ERROR'     =>  APP_PATH . 'Admin/View/Public/error.html', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   =>  APP_PATH . 'Admin/View/Public/jump.html', // 默认成功跳转对应的模板文件
    'TMPL_EXCEPTION_FILE'   =>  THINK_PATH.'Tpl/think_exception.tpl',// 异常页面的模板文件

);
