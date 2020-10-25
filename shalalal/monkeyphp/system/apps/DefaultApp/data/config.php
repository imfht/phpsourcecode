<?php

return array(

    'cache' => array(
        'default_provider' => 'apc',
        'file' => '\Monkey\Cache\File',
        'memcache' => '\Monkey\Cache\Memcache',
        'apc' => '\Monkey\Cache\Apc',
    ),
    'cache_file' => array(
        'expire' => 3600, //默认缓存时间
        'dir' => '/fileCache', //缓存文件的相对路径（相对临时缓存目录），留空为/filecache
        'filename' => 'data',
        'filesize' => '15M',
        'check' => false,
    ),
    'cache_memcache' => array(
        'expire' => 3600, //默认缓存时间
        'host' => 'localhost',
        'port' => 11211,
        'persistent' => '',
        'compressed' => false,
    ),
    'cache_apc' => array(
        'expire' => 3600, //默认缓存时间
    ),
    'database' => array(
        'default_provider' => 'default',
        'default' => '\Monkey\Database\Database',
    ),
    'database_default' => array(
        'default_connection' => 'master',
        //链接池子
        'pool' => array(
            //本数组采用了键值倒置设计，目的是方便操作和提高检索速度
            //后面的值 1 是随便设置的值，只要是TRUE类型都可以
            'master' => 1,
        ),
        //每个连接的键名可以为数字（省略或不省略都可以），比如 下面这个表示 links[0]
        'master' => array(
            'protocol' => 'mysql', //数据库协议
            //dsn配置是可选的、优先的
            'dsn' => 'mysql:host=localhost;port=3306;dbname=macaca', //可以代替下面四项
            'host' => 'localhost', //主机名
            'port' => '3306', //服务端口号
            //数据库名
            //（即使使用dsn，这个都不能省略，并且必须和dsn中的设置一致）
            'dbname' => 'macaca',
            'unix_socket' => '',
            'charset' => 'utf8', //字符集设置
            'collation' => '', //这个要charset首先设置才有用
            'username' => 'root', //数据库用户名
            'password' => '123', //数据库用户密码
            'prefix' => 'mcc_', //表前缀

            'transactions' => true, //数据库（引擎）是否支持事务

            'options' => array(), //PDO连接的操作选项
        ),

    ),
    'errorReporting' => array(
        'default_provider' => 'default',
        'default' => '\Monkey\ErrorReporting\ErrorReporting',
    ),
    'errorReporting_default' => array(
        //错误提示页模板目录，内部必须有4个文件，见框架自带错误提示页模板；
        //留空，即使用MonkeyPHP自带的错误提示页模板
        //如果填写，比如'/ErrorTemplate'表示当前应用目录下的 ErrorTemplate 子目录中
        'errorTemplate' => '',
    ),
    'logger' => array(
        'default_provider' => 'default',
        'default' => '\Monkey\Logger\Logger',
    ),
    'logger_default' => array(
        //一般错误日志
        'error_enable' => true,
        'error_dir' => '/logs/error', //表示当前应用目录下的 /logs/error 子目录中
        //sql错误日志
        'sql_enable' => true,
        'sql_dir' => '/logs/sql', //表示当前应用目录下的 /logs/sql 子目录中
    ),
    'permission' => array(
        'default_provider' => 'default',
        'default' => '\Monkey\Permission\Permission',
    ),
    'Permission_default' => array(),
    'router' => array(
        'default_provider' => 'default',
        'default' => '\Monkey\Router\Router',
    ),
    'router_default' => array(
        //路由存贮配置，相对应用目录。
        'map_file' => '/data/router.map.php', //路由器到控制器的映射表
        'pattern_option' => array(
            //路由匹配时的编译标签，简记名（只能用一对花括号括起来）=>正则表达式（只能用一对括号括起来）
            '{i}' => "(\d+)",
            '{s}' => "([^\/]+)",
            '{year}' => "([1-2]\d{3})",
            '{month}' => "(1[0,1,2]|[1-9])",
            '{day}' => "([1-9]|[1,2][0-9]|3[0,1])",
            '{name}' => "(\w+)",
            '{zh|en}' => "(zh|en)",
            '{json}' => "(\.json)",
        ),
        'router_class_auto_prefix' => true, //自动将router表中类名加上前缀 \AppName\Controller\

        //三个选择：rewrite（需服务器支持）、pathinfo（需服务器支持）、get（传统方式）
        'search_mode' => 'rewrite',
        //get字段上的显式方法设置，如http://www.xxx.php?r=index
        'search_get' => 'r',
    ),
    'session' => array(
        'default_provider' => 'apc',
        'file' => '\Monkey\Session\File',
        'memcache' => '\Monkey\Session\Memcache',
        'apc' => '\Monkey\Session\Apc',
    ),
    'session_file' => array(
        //Session键名前缀，保证每个应用的Session不会碰撞（尤其是使用Memcache作为存储介质时）。
        'prefix' => 'Monkey',
        'expire' => 1440, //默认缓存时间
        'dir' => '/sessionCache', //表示当前应用临时缓存目录下的 '/sessionCache' 子目录中
        'filename' => 'session',
        'filesize' => '15M',
        'check' => false,
    ),
    'session_memcache' => array(
        //Session键名前缀，保证每个应用的Session不会碰撞（尤其是使用Memcache作为存储介质时）。
        'prefix' => 'Monkey',
        'expire' => 1440, //默认缓存时间
        'host' => 'localhost',
        'port' => 11211,
        'persistent' => '',
        'compressed' => false,
    ),
    'session_apc' => array(
        //Session键名前缀，保证每个应用的Session不会碰撞（尤其是使用Memcache作为存储介质时）。
        'prefix' => 'Monkey',
        'expire' => 1440, //默认缓存时间
    ),
    'shutdown' => array(
        'default_provider' => 'default',
        'default' => '\Monkey\Shutdown\Shutdown',
    ),
    'shutdown_default' => array(),
    'view' => array(
        'default_provider' => 'default',
        'default' => '\Monkey\View\View',
    ),
    'view_default' => array(
        'charset' => 'UTF-8', //
        'template_root' => '/template', //相对于应用目录
        'compiled_root' => '/template_compiled',
        //分页栏配置：
        'page_style_name' => 'def',
        'def_link' => '<a href="http://urlPre{number}">{text}</a>',
        'def_link_ajax' => '<a href="javascript:ajaxActionName(\'http://urlPre{number}\')">{text}</a>',
        'def_span_current' => '<span class="current_style">{number}</span>',
        'def_span_total' => '<span class="total_style">共{number}页</span>',
        'def_input_jump' => '转到<input type="text" class="jump_style" size="2" title="输入页码，按回车快速跳转" value="1" onkeydown="if(event.keyCode==13) {window.location=\'http://urlPre\'+this.value; doane(event);}" />',
        'def_text_first' => '首页', //另外，图片可以设置为：'<img src="..." width="16" height="11" />'，下同
        'def_text_pre' => '上一页',
        'def_text_next' => '下一页',
        'def_text_last' => '尾页',
        'def_layout' => 'pre-current-next', //'first-pre-current-next-last'， 'first-pre-list-next-last'（list包含current）
        //支持 first、pre、current、list（包含current）、next、last、total、jump

        //主题配置：结果是 'theme_url_base'.'theme_dir'.'cssFile'
        'theme_url_base' => '', //主题的基础url,空表示index.php所在目录
        'theme_dir' => '/defSkin',
    ),
    'siteset' => array(
        'site_enable' => true, //是否关闭网站 	否 是
        'site_domain' => 'localhost', //网站主域名
        'site_map_enable' => false, //是否启用站点地图
        'site_template_style' => 'default', //站点模板
        'site_name' => '我的网站', //    站点名称
        'site_title' => 'MonkeyPHP Demo网站', //站点标题
        'site_key' => 'Macaca, MonkeyPHP, Demo', //站点关键字
        'site_description' => 'Macaca 是轻量级企业网站管理系统', //站点描述
        'site_logo' => '', //站点标志文件
        'site_icp' => '', //ICP备案证书号

        'company_address' => '', //公司地址
        'company_service_call' => '', //客服电话
        'company_fax' => '', //传真
        'company_service_qq' => '', //客服QQ号码，多个客服的QQ号码请以半角逗号（,）分隔。
        'company_service_email' => '', //邮件地址

        'products_list_limit' => 10, //商品列表数量
        'article_list_limit' => 10, //文章列表数量
        'guestbook_list_limit' => 10, //留言列表数量
        'thumbnail_width' => 135, //商品列表数量
        'thumbnail_height' => 135, //商品列表数量
    ),
);



