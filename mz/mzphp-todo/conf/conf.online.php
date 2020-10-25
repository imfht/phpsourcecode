<?php
/*
********请不尽量不要使用记事本编辑本文件，请务必移除 BOM 头*****
*/

function get_url_abpath(){
    return substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/'));
}
$app_dir = get_url_abpath().'/';
$app_dir_reg = preg_quote($app_dir);

return array(
    //db support： mysql/pdo_mysql/pdo_sqlite(数据库支持:mysql/pdo_mysql/pdo_sqlite)
    'db' => array(
        'mysql' => array(
            'tablepre' => 'todo_',
            'master' => array(
                'host' => '127.0.0.1',
                'user' => 'root',
                'pass' => '',
                'name' => 'todo',
                'charset' => 'utf8',
                'engine' => 'MYISAM',
            ),
    		/*
            'slaves' => array(
                array(
                    'host' => '127.0.0.1:3066',
                    'user' => 'root',
                    'pass' => '',
                    'name' => 'todo',
                    'charset' => 'utf8',
                    'engine' => 'MYISAM',
                ),
            ),
    		*/
        ),
        //other example
        /*
        'pdo_mysql' => array(
            'master' => array(
                'host' => '127.0.0.1',
                'user' => 'root',
                'pass' => '',
                'name' => 'todo',
                'charset' => 'utf8',
                'engine' => 'MYISAM',
            ),
            'slaves' => array(
                array(
                    'host' => '127.0.0.1',
                    'user' => 'root',
                    'pass' => '',
                    'name' => 'todo',
                    'charset' => 'utf8',
                    'engine' => 'MYISAM',
                ),
            ),
            'tablepre' => 'todo_',
        ),
        'pdo_sqlite' => array(
            'host' => ROOT_PATH.'data/tmp/sqlite_test.db',
            'tablepre' => 'todo_',
        ),
        */
    ),
    // cache support: memcache/file(缓存支持：memcache/文件缓存)
    'cache' => array(
        /*
        'memcache' => array(
            'host' => '127.0.0.1:11211',
            'pre' => 'todo_',
        ),
        'file' => array(
            'dir' => ROOT_PATH.'data/cachefe5ec6ab801b860587663dbcb0fe0e67/',
            'pre' => 'todo_',
        ),
		'redis' => array(
			'host' => '127.0.0.1',
			'port' => 19000,
			'table' => 'www',
			'pre' => 'todo_',
		),
        */
    ),

    // 唯一识别ID
    'app_id' => 'todo',

    //网站名称
    'app_name' => 'todo',

    // cookie 前缀
    'cookie_pre' => 'todo',

    // cookie 域名
    'cookie_domain' => '',

    //是否开启 gzip
    'gzip' => 0,

    //是否接受 x_forwarded_for 传过来的ip(反代的时候需要)
    //正常单机外网运行下，建议关掉，因为能伪造 ip
    //'ip_x_forward' => 1,

    // 应用的绝对路径： 如: http://www.domain.com/app/
    'app_url' => '/',

    // 应用的所在路径： 如: http://www.domain.com/app/
    'app_dir' => $app_dir,

    // 404 等错误设置
    'page_setting' => array(
        '404' => 'static/404.htm',
    ),

    // CDN 缓存的静态域名，如 http://static.domain.com/
    'static_url' => './static/',

    // CDN 本地缓存的静态目录，如 http://static.domain.com/
    'static_dir' => ROOT_PATH.'static/',

    // 应用内核扩展目录，一些公共的库需要打包进 _runtime.php （减少io）
    'core_path' => ROOT_PATH.'core/',

    // 模板使用的目录，按照顺序搜索，这样可以支持风格切换,结果缓存在 data/tmp
    'view_path' => array(ROOT_PATH.'view/'),

    // 数据模块的路径，按照数组顺序搜索目录
    'model_path' => array(ROOT_PATH.'model/'),

    // 自动加载 model 的映射表， 在 model_path 中未找到 model 的时, modelname=>array(tablename, primarykey, maxcol)
    'model_map' => array(),

    // 控制器的路径，按照数组顺序搜索目录
    'control_path' => array(ROOT_PATH.'control/'),

    // 站群域名配置文件
    // 生成模板前缀，站群模式需要用到，子域名可以重新定义一个前缀用于区分不同目录下，相同文件的问题
    // 'tpl_prefix' => 'todo_',
    // 'domain_path' => ROOT_PATH.'domain/',
    // 用于站群不同域名指向不同的 view/model/control 目录
    /*
        domain/admin.todo.com.php 例子：
        return array(
            // 最好重新定义 app_id ， 因为模板引擎会根据 app_id 生成前缀
            // 否则两个站模板一旦有同样名字会覆盖
            'app_id' => 'todo_admin',
            'control_path' => array(ROOT_PATH.'control/admin/'),
            'model_path' => array(ROOT_PATH.'model/'),
            'view_path' => array(ROOT_PATH.'view/admin/'),
        ),
    */

    // 临时目录，需要可写，可以指定为 linux /dev/shm/ 目录提高速度,
    'tmp_path' => ROOT_PATH.'data/tmp/',

    // 日志目录，需要可写
    'log_path' => ROOT_PATH.'data/log/',

    // 服务器所在的时区
    'timeoffset' => '+8',

    // 模板插件
    'tpl' => array(
        'plugins' => array(
        	// 支持 static 语法插件，支持 scss、css、js 打包
            'tpl_static' => FRAMEWORK_PATH.'plugin/tpl_static.class.php',
        ),
    ),

    // 开启rewrite
    'url_rewrite' => 0,

    // 是否不压缩 html代码(如果不开启，html中的<script>片段不能有//行注释，只能用块注释/**/)
    'html_no_compress' => 0,

    // 地址重写的分隔符和后缀设置
    'rewrite_info' => array(
        'comma' => '/', // options:/\ - _  |.,
        'ext' => '.html',// for example : .htm
    ),
    'str_replace' => array(),

    'reg_replace' => array(),
);
	