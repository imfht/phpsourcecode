<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------

/**
 * thinkshop全局配置文件
 */
$_config = array (
    'PRODUCT_NAME'    => 'thinkshop',                  //产品名称
    'CURRENT_VERSION' => '1.0.0',                      //当前版本
    'WEBSITE_DOMAIN'  => 'http://git.oschina.net/foryoufeng/thinkshop',    //官方网址
    'UPDATE_URL'      => 'http://git.oschina.net/foryoufeng/thinkshop', //官方更新网址
    'COMPANY_NAME'    => 'thinkshop团队',   //公司名称
    'DEVELOP_TEAM'    => 'thinkshop团队',   //当前项目开发团队名称
    'WEB_SITE_TITLE'=> 'thinkshop',   //网站名称
    //产品简介
    'PRODUCT_INFO'    => 'thinkshop是基于thinkphp而开发的一个B2C的电子商城系统',

    //公司简介
    'COMPANY_INFO'    => 'thinkshop团队',

    //系统主页地址配置
    'HOME_PAGE'       => 'http://'.$_SERVER['HTTP_HOST'].__ROOT__,

    //URL模式
    'URL_MODEL' => '2',

    //全局过滤配置
    'DEFAULT_FILTER' => '', //TP默认为htmlspecialchars

    //预先加载的标签库
    'TAGLIB_PRE_LOAD' => 'Home\\TagLib\\Corethink',

    //URL配置
    'URL_CASE_INSENSITIVE' => true, //不区分大小写

    //应用配置
    'DEFAULT_MODULE'     => 'Home',
    'MODULE_DENY_LIST'   => array('Common'),
    'MODULE_ALLOW_LIST'  => array('Home','Admin','Install'),
    //'AUTOLOAD_NAMESPACE' => array('Addons' => THINK_ADDON_PATH), //扩展模块列表

    //模板相关配置
    'TMPL_PARSE_STRING'  => array (
        '__PUBLIC__'     => __ROOT__.'/Public',
        '__ADMIN_IMG__'  => __ROOT__.'/'.APP_PATH.'Admin/View/_Resource/img',
        '__ADMIN_CSS__'  => __ROOT__.'/'.APP_PATH.'Admin/View/_Resource/css',
        '__ADMIN_JS__'   => __ROOT__.'/'.APP_PATH.'Admin/View/_Resource/js',
        '__ADMIN_LIBS__' => __ROOT__.'/'.APP_PATH.'Admin/View/_Resource/libs',
        '__HOME_IMG__'   => __ROOT__.'/'.APP_PATH.'Home/View/default/_Resource/img',
        '__HOME_CSS__'   => __ROOT__.'/'.APP_PATH.'Home/View/default/_Resource/css',
        '__HOME_JS__'    => __ROOT__.'/'.APP_PATH.'Home/View/default/_Resource/js',
        '__HOME_LIBS__'  => __ROOT__.'/'.APP_PATH.'Home/View/default/_Resource/libs',
    ),

    //文件上传默认驱动
    'UPLOAD_DRIVER' => 'Local',

    //文件上传相关配置
    'UPLOAD_CONFIG' => array(
        'mimes'    => '', //允许上传的文件MiMe类型
        'maxSize'  => 2*1024*1024, //上传的文件大小限制 (0-不做限制，默认为2M，后台配置会覆盖此值)
        'autoSub'  => true, //自动子目录保存文件
        'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'  => '', //文件保存后缀，空则使用原后缀
        'replace'  => false, //存在同名是否覆盖
        'hash'     => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ),
);

//返回合并的配置
return array_merge (
    $_config, //系统全局默认配置
    include APP_PATH.'/Common/Conf/db.php', //包含数据库连接配置
    include APP_PATH.'/Common/Conf/custom.php' //包含用户自定义配置
);
