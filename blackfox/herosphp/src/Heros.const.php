<?php
/**
 * 系统常量定义
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since v1.2.1
 */

//定义编译路径
define('APP_RUNTIME_PATH', APP_ROOT.'runtime/');

//定义import加载类的类别
define('IMPORT_APP', 1);    //加载当前应用模块中的class文件
define('IMPORT_CUSTOM', 2); //加载自定义路径中的class文件

//采用import加载文件的后缀名常量
define('EXT_PHP', '.php');   //加载php class文件
define('EXT_MODEL', '.model.php');   //加载model文件
define('EXT_CONFIG', '.config.php');   //加载配置文件
define('EXT_HTML', '.html');    //加载html文件

define('EXT_TPL', '.html');     //模板文件后缀
define('EXT_URI', '.shtml');     //uri 伪静态路径后缀

/**
 * 以下订制数据库访问策略，提供单台服务器访问和读写分离集群访问模式
 * 如果采用集群访问模式请在 /config/db.config.php文件中设置好你的数据库集群配置
 */
define('DB_ACCESS_SINGLE', 1);  //单台服务器访问模式
define('DB_ACCESS_CLUSTERS', 2);  //数据库服务器集群调度访问模式
