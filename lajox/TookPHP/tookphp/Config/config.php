<?php
return array(
    /********************************基本参数********************************/
    'DEFAULT_TIME_ZONE'             => 'PRC',       //时区
    'DEFAULT_LANG'                  => 'zh-cn',     //默认语言包
    'CHECK_FILE_CASE'               => TRUE,        //windows区分大小写
    'LOAD_EXT_CONFIG'               => '',          //拓展配置
    'LOAD_EXT_FILE'                 => '',          //加载扩展文件
    'APP_AUTOLOAD_PATH'             => '',          //自动加载路径
    'APP_AUTOLOAD_NAMESPACE'        => array(),     //自动加载命名空间
    'FILTER_FUNCTION'               => array('htmlspecialchars','strip_tags'), //过滤函数会在Q(),date_format()等函数中使用
    /********************************默认参数********************************/
    'DEFAULT_JSONP_HANDLER'         => 'jsonpReturn', // 默认JSONP格式返回的处理方法
    /********************************数据库********************************/
    'DB_DRIVER'                     => 'Mysqli',    //数据库驱动
    'DB_CHARSET'                    => 'utf8',      //数据库字符集
    'DB_HOST'                       => '127.0.0.1', //数据库连接主机  如127.0.0.1
    'DB_PORT'                       => 3306,        //数据库连接端口
    'DB_USER'                       => 'root',      //数据库用户名
    'DB_PASSWORD'                   => '',          //数据库密码
    'DB_DATABASE'                   => '',          //数据库名称
    'DB_PREFIX'                     => '',          //表前缀
    'DB_PCONNECT'                   => false,       //数据库持久链接
    'CACHE_SELECT_TIME'             => -1,          //缓存时间 -1为不缓存 0为永久缓存
    'CACHE_SELECT_LENGTH'           => 30,          //缓存最大条数
    /********************************模板参数********************************/
    'TPL_CHARSET'                   => 'utf-8',     //字符集
    'TPL_PATH'                      => 'View',      //模板目录
    'TPL_STYLE'                     => '',          //风格
    'TPL_EXT'                       => '.html',     //模版文件扩展名
    'TPL_TAGS'                      => array(),     //模板标签
    'TPL_ERROR'                     => 'error',     //错误信息模板
    'TPL_SUCCESS'                   => 'success',   //正确信息模板
    'TPL_ENGINE'                    => 'Tk',        //模板引擎 Tk,Smarty
    'TPL_TAG_LEFT'                  => '<',         //左标签
    'TPL_TAG_RIGHT'                 => '>',         //右标签
    'TPL_CACHE_TIME'                => -1,          //模板缓存时间 -1为不缓存 0为永久缓存
    'LAYOUT_ON'                     => false,       //是否启用布局
    'LAYOUT_NAME'                   => 'layout',    //当前布局名称 默认为layout
    'LAYOUT_REPLACE'                => '{__CONTENT__}', //布局模板的内容替换标识
    /********************************储存********************************/
    'STORAGE_DRIVER'		        =>'File',       //储存驱动 支持File与Memcache储存
    /********************************系统调试********************************/
    '404_URL'                       => '',          //404跳转url
    'ERROR_URL'                     => '',          //错误跳转URL
    'ERROR_MESSAGE'                 => '网站出错了，请稍候再试...', //关闭DEBUG显示的错误信息
    'SHOW_NOTICE'                   => FALSE,        //显示Warning与Notice错误显示
    /********************************LOG日志处理********************************/
    'LOG_SIZE'                      => 2000000,     //日志文件大小
    'LOG_RECORD'                    => TRUE,        //记录日志
    'LOG_LEVEL'                     => array('FATAL','ERROR','WARNING','NOTICE','INFO','SQL'),//写入日志的错误级别
    'LOG_EXCEPTION_RECORD'          => TRUE,        // 记录异常
    /********************************SESSION********************************/
    'SESSION_AUTO_START'            => TRUE,        //自动开启SESSION
    'SESSION_TYPE'                  => '',          //引擎:mysql,memcache,redis
    'SESSION_OPTIONS'               => array(),     //Session选项
    /********************************COOKIE********************************/
    'COOKIE_EXPIRE'                 => 0,           // Coodie有效期
    'COOKIE_DOMAIN'                 => '',          // Cookie有效域名
    'COOKIE_PATH'                   => '/',         // Cookie路径
    'COOKIE_PREFIX'                 => '',          // Cookie前缀 避免冲突
    /********************************URL设置********************************/
    'HTTPS'                         => FALSE,       //基于https协议
    'URL_REWRITE'                   => FALSE,       //url重写模式
    'URL_TYPE'                      => 1,           //类型 1:PATHINFO模式 2:普通模式 3:兼容模式
    'PATHINFO_DLI'                  => '/',         //URL分隔符 URL_TYPE为1、3时起效
    'PATHINFO_VAR'                  => 'q',         //兼容模式get变量
    'HTML_SUFFIX'                   => '',          //伪静态扩展名
    'VAR_GROUP'                     => 'g',         //模块组URL变量
    'VAR_MODULE'                    => 'm',         //模块URL变量
    'VAR_CONTROLLER'                => 'c',         //控制器URL变量
    'VAR_ACTION'                    => 'a',         //动作URL变量
    'VAR_AJAX_SUBMIT'               => 'submit',    // 默认的AJAX提交变量
    'VAR_JSONP_HANDLER'             => 'callback',  // 默认的JSONP回调URL变量
    'MODULE_LIST'                   => '',          //启用分组模块
    'DENY_MODULE'                   => 'Common,Temp,Addons',//禁止使用的模块
    'DEFAULT_MODULE'                => 'Home',     //默认模块
    'DEFAULT_CONTROLLER'            => 'Index',     //默认控制器
    'DEFAULT_ACTION'                => 'index',     //默认方法
    'CONTROLLER_FIX'                => 'Controller',//控制器文件后缀
    'MODEL_FIX'                     => 'Model',     //模型文件名后缀
    /********************************文件上传********************************/
    'FILE_UPLOAD_TYPE'              =>'Local',      //文件上传驱动类型： Local, Ftp
    'UPLOAD_TYPE_CONFIG' => array(
        'exts'                      => array(),     //允许上传的文件后缀
        'maxSize'                   =>  0,          //上传的文件大小限制 (0-不做限制)
        'rootPath'                  => './Uploads/', //保存根路径
        'autoSub'                   => true,        //自动子目录保存文件
        'subName'                   => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'saveName'                  => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
    ),
    /********************************图像水印处理********************************/
    'WATER_ON'                      => TRUE,        //开关
    'WATER_FONT'                    => TOOK_PATH . 'Data/Font/font.ttf',   //水印字体
    'WATER_IMG'                     => TOOK_PATH . 'Data/Image/water.png', //水印图像
    'WATER_POS'                     => 9,           //位置  1~9九个位置  0为随机
    'WATER_PCT'                     => 60,          //透明度
    'WATER_QUALITY'                 => 80,          //压缩比
    'WATER_TEXT'                    => 'WWW.19WWW.COM', //水印文字
    'WATER_TEXT_COLOR'              => '#f00f00',   //文字颜色
    'WATER_TEXT_SIZE'               => 12,          //文字大小
    /********************************图片缩略图********************************/
    'THUMB_PREFIX'                  => '',          //缩略图前缀
    'THUMB_ENDFIX'                  => '_thumb',    //缩略图后缀
    'THUMB_TYPE'                    => 6,   //生成方式,
                                            //1:固定宽度,高度自增 2:固定高度,宽度自增 3:固定宽度,高度裁切
                                            //4:固定高度,宽度裁切 5:缩放最大边       6:自动裁切图片
    'THUMB_WIDTH'                   => 300,         //缩略图宽度
    'THUMB_HEIGHT'                  => 300,         //缩略图高度
    /********************************验证码********************************/
    'CODE_FONT'                     => TOOK_PATH . 'Data/Font/font.ttf', //字体
    'CODE_STR'                      => '23456789abcdefghjkmnpqrstuvwsyz', //验证码种子
    'CODE_WIDTH'                    => 120,         //宽度
    'CODE_HEIGHT'                   => 35,          //高度
    'CODE_BG_COLOR'                 => '#ffffff',   //背景颜色
    'CODE_LEN'                      => 4,           //文字数量
    'CODE_FONT_SIZE'                => 20,          //字体大小
    'CODE_FONT_COLOR'               => '',          //字体颜色
    /********************************分页处理********************************/
    'PAGE_VAR'                      => 'page',      //分页GET变量
    'PAGE_ROW'                      => 10,          //页码数量
    'PAGE_SHOW_ROW'                 => 10,          //每页显示条数
    'PAGE_STYLE'                    => 2,           //页码风格
    'PAGE_DESC'                     => array('pre' => '上一页', 'next' => '下一页',//分页文字设置
                                            'first' => '首页', 'end' => '尾页', 'unit' => '条'),
    /********************************RBAC权限控制********************************/
    'RBAC_TYPE'                     => 1,           //1时时认证｜2登录认证
    'RBAC_SUPER_ADMIN'              => 'super_admin', //超级管理员SESSION名
    'RBAC_USERNAME_FIELD'           => 'username',  //用户名字段
    'RBAC_PASSWORD_FIELD'           => 'password',  //密码字段
    'RBAC_AUTH_KEY'                 => 'uid',       //用户SESSION名
    'RBAC_NO_AUTH'                  => array(),     //不需要验证的控制器或方法如:array('index/index')表示index控制器的index方法不需要验证
    'RBAC_USER_TABLE'               => 'user',      //用户表
    'RBAC_ROLE_TABLE'               => 'role',      //角色表
    'RBAC_NODE_TABLE'               => 'node',      //节点表
    'RBAC_ROLE_USER_TABLE'          => 'user_role', //角色与用户关联表
    'ACCESS_TABLE'                  => 'access',    //权限分配表
    /********************************缓存********************************/
    'CACHE_TYPE'                    => 'file',      //类型:file memcache redis
    'CACHE_TIME'                    => 0,           //全局默认缓存时间 0为永久缓存 -1 不缓存
    'CACHE_MEMCACHE'                => array(       //多个服务器设置二维数组
        'host'      => '127.0.0.1',     //主机
        'port'      => 11211,           //端口
        'timeout'   => 1,               //超时时间(单位为秒)
        'weight'    => 1,               //权重
        'pconnect'  => 1,               //持久连接
    ),
    'CACHE_REDIS'                   => array( //多个服务器设置二维数组
        'host'      => '127.0.0.1',     //主机
        'port'      => 6379,            //端口
        'password'  => '',              //密码
        'timeout'   => 1,               //超时时间(单位为秒)
        'Db'        => 0,               //数据库
        'pconnect'  => 0,               //持久连接
    ),
    /********************************URL路由********************************/
    'ROUTE'                         => array(),
    /********************************钓子********************************/
    'HOOK'                          => array(),
    /********************************别名导入********************************/
    'ALIAS'                         => array()

);
?>