<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com>
//<http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
* 系统配文件
* 所有系统级别的配置
*/

return array(
/* 模块相关配置 */
'AUTOLOAD_NAMESPACE' => array('Addons' => ONETHINK_ADDON_PATH), //扩展模块列表
'DEFAULT_MODULE'     => 'Home',
'MODULE_DENY_LIST'   => array('Common', 'User'),
//'MODULE_ALLOW_LIST'  => array('Home','Admin'),

/* 系统数据加密设置 */
'DATA_AUTH_KEY' => 'Bx~IZO+lV|G1"){3WPXN`bFUef9qy(54E?:LjH,S', //默认数据加密KEY
/*自动加载函数列表文件*/
 'LOAD_EXT_FILE' =>'extend,common',//定义自动加载扩展函数Common下的Common和extend
/*增加设置项@水月居*/
'TMPL_ACTION_ERROR'     =>  './Public/Tpl/dispatch_jump.tpl', // 默认错误跳转对应的模板文件
'TMPL_ACTION_SUCCESS'   =>  './Public/Tpl/dispatch_jump.tpl', // 默认成功跳转对应的模板文件

/* 调试配置 */
//'SHOW_PAGE_TRACE' => true,//该项已经取消换成在数据库config中设置

/* 用户相关设置 */
'USER_MAX_CACHE'     => 1000, //最大缓存用户数
'USER_ADMINISTRATOR' => 1, //管理员用户ID

/* URL配置 */
'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写
'URL_MODEL'            => 2, //URL模式  默认关闭伪静态 2rewrite
'VAR_URL_PARAMS'       => '', // PATHINFO URL参数变量
'URL_PATHINFO_DEPR'    => '/', //PATHINFO URL分割符

/* 全局过滤配置 */
'DEFAULT_FILTER' => '', //全局过滤函数

/* 数据库配置 */
'DB_TYPE'   => 'mysqli', // 数据库类型
'DB_HOST'   => '127.0.0.1', // 服务器地址
'DB_NAME'   => 'wgyxxdata', // 数据库名
'DB_USER'   => 'admin', // 用户名
'DB_PWD'    => 'singliang',  // 密码
'DB_PORT'   => '3306', // 端口
'DB_PREFIX' => 'wgy_', // 数据库表前缀

/* 文档模型配置 (文档模型核心配置，请勿更改) */
'DOCUMENT_MODEL_TYPE' => array(2 => '主题', 1 => '目录', 3 => '段落'),
'LOAD_EXT_CONFIG' => 'router',
/* 文件上传相关配置 */
'DOWNLOAD_UPLOAD' => array(
'mimes'    => '', //允许上传的文件MiMe类型
'maxSize'  => 10*1024*1024, //上传的文件大小限制 (0-不做限制)
'exts'     => 'jpg,gif,png,jpeg,zip,rar,tar,gz,7z,doc,docx,txt,xml', //允许上传的文件后缀
'autoSub'  => true, //自动子目录保存文件
'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
'rootPath' => './Uploads/Download/', //保存根路径
'savePath' => '', //保存路径
'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
'saveExt'  => '', //文件保存后缀，空则使用原后缀
'replace'  => false, //存在同名是否覆盖
'hash'     => true, //是否生成hash编码
'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
), //下载模型上传配置（文件上传类配置）


/* 图片上传相关配置 */
'PICTURE_UPLOAD' => array(
'mimes'    => '', //允许上传的文件MiMe类型
'maxSize'  => 20*1024*1024, //上传的文件大小限制 (0-不做限制)
'exts'     => 'jpg,gif,png,jpeg', //允许上传的文件后缀
'autoSub'  => true, //自动子目录保存文件
'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
'rootPath' => './Uploads/Picture/', //保存根路径
'savePath' => '', //保存路径
'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
'saveExt'  => '', //文件保存后缀，空则使用原后缀
'replace'  => true, //存在同名是否覆盖
'hash'     => true, //是否生成hash编码
'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
), //图片上传相关配置（文件上传类配置）

'PICTURE_UPLOAD_DRIVER'=>'local',
'DOWNLOAD_UPLOAD_DRIVER'=>'local',
//本地上传文件驱动配置
'UPLOAD_LOCAL_CONFIG'=>array(),

/* 编辑器图片上传相关配置 */
'EDITOR_UPLOAD' => array(
'mimes'    => '', //允许上传的文件MiMe类型
'maxSize'  => 20*1024*1024, //上传的文件大小限制 (0-不做限制)
'exts'     => 'jpg,gif,png,jpeg', //允许上传的文件后缀
'autoSub'  => true, //自动子目录保存文件
'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
'rootPath' => './Uploads/Editor/', //保存根路径
'savePath' => '', //保存路径
'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
'saveExt'  => '', //文件保存后缀，空则使用原后缀
'replace'  => false, //存在同名是否覆盖
'hash'     => true, //是否生成hash编码
'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
),
);
