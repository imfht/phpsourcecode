<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\saf;

/**
 * Loader Define file
 * 定义项目常用目录，自动加载规则
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Loader.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.saf
 * @since 1.0
 */

/**
 * 为true时表示测试环境，会打印Debug日志，页面上展示调试信息
 */
defined('DEBUG') || define('DEBUG', false);

/**
 * 显示所有错误信息
 */
DEBUG && ini_set('display_errors', 'On');

/**
 * 设置PHP报错级别
 */
error_reporting(DEBUG ? E_ALL : 0);

/**
 * 是否自动将GET、POST、COOKIE中的"'"、'"'、'\'加上反斜线
 */
defined('MAGIC_QUOTES_GPC') || define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());

/**
 * 调用此框架前需要先定义常量：项目名称
 */
defined('APP_NAME') || exit('Request Error, No defined APP_NAME');

/**
 * 时间常量，单位：秒
 */
define('MINUTE_IN_SECONDS', 60);
define('HOUR_IN_SECONDS',   60 * MINUTE_IN_SECONDS);
define('DAY_IN_SECONDS',    24 * HOUR_IN_SECONDS);
define('WEEK_IN_SECONDS',    7 * DAY_IN_SECONDS);
define('MONTH_IN_SECONDS',  30 * DAY_IN_SECONDS);
define('YEAR_IN_SECONDS',  365 * DAY_IN_SECONDS);

/**
 * 不同操作系统的目录分割符
 */
defined('DS') || define('DS', DIRECTORY_SEPARATOR);

/**
 * 不同操作系统的路径分割符
 */
defined('PS') || define('PS', PATH_SEPARATOR);

/**
 * tfc\saf目录
 */
defined('DIR_TFC_SAF') || define('DIR_TFC_SAF', dirname(__FILE__));

/**
 * TFC框架目录
 */
defined('DIR_TFC') || define('DIR_TFC', substr(DIR_TFC_SAF, 0, -4));

/**
 * 公共框架和代码库目录
 */
defined('DIR_LIBRARIES') || define('DIR_LIBRARIES', substr(DIR_TFC, 0, -4));

/**
 * ROOT目录
 */
defined('DIR_ROOT') || define('DIR_ROOT', substr(DIR_LIBRARIES, 0, -10));

/**
 * 公共业务目录
 */
defined('DIR_SERVICES') || define('DIR_SERVICES', DIR_ROOT . DS . 'services');

/**
 * 公共业务的所有语言包存放目录
 */
defined('DIR_SERVICES_LANGUAGES') || define('DIR_SERVICES_LANGUAGES', DIR_SERVICES . DS . 'languages');

/**
 * 公共业务目录
 */
defined('DIR_SRV') || define('DIR_SRV', DIR_ROOT . DS . 'srv');

/**
 * 当前项目目录
 */
defined('DIR_APP') || define('DIR_APP', DIR_ROOT . DS . 'app' . DS . APP_NAME);

/**
 * 当前项目的所有组件存放目录
 */
defined('DIR_APP_COMPONENTS') || define('DIR_APP_COMPONENTS', DIR_APP . DS . 'components');

/**
 * 当前项目的所有语言包存放目录
 */
defined('DIR_APP_LANGUAGES') || define('DIR_APP_LANGUAGES', DIR_APP . DS . 'languages');

/**
 * 当前项目的公共代码库目录
 */
defined('DIR_APP_LIBRARY') || define('DIR_APP_LIBRARY', DIR_APP . DS . 'library');

/**
 * 当前项目的所有模块存放目录
 */
defined('DIR_APP_MODULES') || define('DIR_APP_MODULES', DIR_APP . DS . 'modules');

/**
 * 当前项目的所有插件存放目录
 */
defined('DIR_APP_PLUGINS') || define('DIR_APP_PLUGINS', DIR_APP . DS . 'plugins');

/**
 * 当前项目的所有脚本存放目录
 */
defined('DIR_APP_SCRIPTS') || define('DIR_APP_SCRIPTS', DIR_APP . DS . 'scripts');

/**
 * 当前项目的所有测试代码存放目录
 */
defined('DIR_APP_TESTS') || define('DIR_APP_TESTS', DIR_APP . DS . 'tests');

/**
 * 当前项目的所有模板存放目录
 */
defined('DIR_APP_VIEWS') || define('DIR_APP_VIEWS', DIR_APP . DS . 'views');

/**
 * 当前项目的所有模板部件存放目录
 */
defined('DIR_APP_WIDGETS') || define('DIR_APP_WIDGETS', DIR_APP . DS . 'widgets');

/**
 * 配置文件根目录
 */
defined('DIR_CFG') || define('DIR_CFG', DIR_ROOT . DS . 'cfg');

/**
 * 当前项目的配置文件存放目录
 */
defined('DIR_CFG_APP') || define('DIR_CFG_APP', DIR_CFG . DS . 'app' . DS . APP_NAME);

/**
 * 数据库的配置文件存放目录
 */
defined('DIR_CFG_DB') || define('DIR_CFG_DB', DIR_CFG . DS . 'db');

/**
 * Ral的配置文件存放目录
 */
defined('DIR_CFG_RAL') || define('DIR_CFG_RAL', DIR_CFG . DS . 'ral');

/**
 * Key的配置文件存放目录
 */
defined('DIR_CFG_KEY') || define('DIR_CFG_KEY', DIR_CFG . DS . 'key');

/**
 * 缓存的配置文件存放目录
 */
defined('DIR_CFG_CACHE') || define('DIR_CFG_CACHE', DIR_CFG . DS . 'cache');

/**
 * 数据文件存放根目录
 */
defined('DIR_DATA') || define('DIR_DATA', DIR_ROOT . DS . 'data');

/**
 * 当前项目的数据文件存放目录
 */
defined('DIR_DATA_APP') || define('DIR_DATA_APP', DIR_DATA . DS . 'app' . DS . APP_NAME);

/**
 * 上传文件存放目录
 */
defined('DIR_DATA_UPLOAD') || define('DIR_DATA_UPLOAD', DIR_DATA . DS . 'u');

/**
 * 运行时生成的临时文件存放目录
 */
defined('DIR_DATA_RUNTIME') || define('DIR_DATA_RUNTIME', DIR_DATA . DS . 'runtime');

/**
 * 角色授权数据缓存目录
 */
defined('DIR_DATA_RUNTIME_ROLES') || define('DIR_DATA_RUNTIME_ROLES', DIR_DATA_RUNTIME . DS . 'roles');

/**
 * 运行时生成的表实体类存放目录
 */
defined('DIR_DATA_RUNTIME_ENTITIES') || define('DIR_DATA_RUNTIME_ENTITIES', DIR_DATA_RUNTIME . DS . 'entities');

/**
 * 日志文件存放根目录
 */
defined('DIR_LOG') || define('DIR_LOG', DIR_ROOT . DS . 'log');

/**
 * 当前项目的日志文件存放目录
 */
defined('DIR_LOG_APP') || define('DIR_LOG_APP', DIR_LOG . DS . APP_NAME);

/**
 * 网站入口目录
 */
defined('DIR_WEBROOT') || define('DIR_WEBROOT', DIR_ROOT . DS . 'webroot');

/**
 * 所有项目公共的静态文件存放目录
 */
defined('DIR_WEBROOT_STATIC') || define('DIR_WEBROOT_STATIC', DIR_WEBROOT . DS . 'static' . DS . APP_NAME);

/**
 * 初始化日志文件存放根目录、当前项目的日志文件存放目录
 */
is_dir(DIR_LOG_APP)       || mkdir(DIR_LOG_APP, 0777, true);
is_dir(DIR_LOG_APP)       || exit('Request Error, Create Log Dir Failed');
is_writeable(DIR_LOG_APP) || exit('Directory Error, "' . DIR_LOG_APP . '" Can not writeable');

/**
 * 初始化上传文件存放目录
 */
is_dir(DIR_DATA_UPLOAD)       || mkdir(DIR_DATA_UPLOAD, 0777, true);
is_dir(DIR_DATA_UPLOAD)       || exit('Request Error, Create Upload Dir Failed');
is_writeable(DIR_DATA_UPLOAD) || exit('Directory Error, "' . DIR_DATA_UPLOAD . '" Can not writeable');

/**
 * 初始化运行时生成的临时文件存放目录
 */
is_dir(DIR_DATA_RUNTIME)       || mkdir(DIR_DATA_RUNTIME, 0777, true);
is_dir(DIR_DATA_RUNTIME)       || exit('Request Error, Create RunTime Dir Failed');
is_writeable(DIR_DATA_RUNTIME) || exit('Directory Error, "' . DIR_DATA_RUNTIME . '" Can not writeable');

/**
 * 初始化角色授权数据缓存目录
 */
is_dir(DIR_DATA_RUNTIME_ROLES)       || mkdir(DIR_DATA_RUNTIME_ROLES, 0777, true);
is_dir(DIR_DATA_RUNTIME_ROLES)       || exit('Request Error, Create RunTime Roles Dir Failed');
is_writeable(DIR_DATA_RUNTIME_ROLES) || exit('Directory Error, "' . DIR_DATA_RUNTIME_ROLES . '" Can not writeable');

/**
 * 初始化表实体类存放目录
 */
is_dir(DIR_DATA_RUNTIME_ENTITIES)       || mkdir(DIR_DATA_RUNTIME_ENTITIES, 0777, true);
is_dir(DIR_DATA_RUNTIME_ENTITIES)       || exit('Request Error, Create RunTime Entities Dir Failed');
is_writeable(DIR_DATA_RUNTIME_ENTITIES) || exit('Directory Error, "' . DIR_DATA_RUNTIME_ENTITIES . '" Can not writeable');

is_file(DIR_LOG_APP               . DS . 'index.html') || file_put_contents(DIR_LOG_APP               . DS . 'index.html', '<!DOCTYPE html><title></title>');
is_file(DIR_DATA_UPLOAD           . DS . 'index.html') || file_put_contents(DIR_DATA_UPLOAD           . DS . 'index.html', '<!DOCTYPE html><title></title>');
is_file(DIR_DATA_RUNTIME          . DS . 'index.html') || file_put_contents(DIR_DATA_RUNTIME          . DS . 'index.html', '<!DOCTYPE html><title></title>');
is_file(DIR_DATA_RUNTIME_ROLES    . DS . 'index.html') || file_put_contents(DIR_DATA_RUNTIME_ROLES    . DS . 'index.html', '<!DOCTYPE html><title></title>');
is_file(DIR_DATA_RUNTIME_ENTITIES . DS . 'index.html') || file_put_contents(DIR_DATA_RUNTIME_ENTITIES . DS . 'index.html', '<!DOCTYPE html><title></title>');

/**
 * 设置公共框架和代码库目录、当前项目的公共代码库目录、当前项目的所有模块存放目录到PHP INI自动加载目录
 */
set_include_path('.' . PS . DIR_LIBRARIES . PS . DIR_SERVICES . PS . DIR_SRV . PS . DIR_APP . PS . trim(get_include_path(), '.' . PS)) 
 || 
exit('Request Error, your server configuration not allowed to change PHP include path');

/**
 * 自动加载PHP文件
 * @param string $className
 * @return void
 */
function spl_autoload($className)
{
    $className = str_replace('\\', DS, $className) . '.php';
    require $className;
}

/**
 * 注册__autoload方法
 */
spl_autoload_register('\tfc\saf\spl_autoload') || exit('Request Error, unable to register autoload as an autoloading method');

/**
 * 初始化$_GET、$_POST、$_COOKIE值，在指定的预定义字符前添加反斜杠
 */
if (!MAGIC_QUOTES_GPC) {
    $_GET    = \tfc\util\String::addslashes($_GET);
    $_POST   = \tfc\util\String::addslashes($_POST);
    $_COOKIE = \tfc\util\String::addslashes($_COOKIE);
}

if (!function_exists('debug_dump')) {
    /**
     * 测试打印数据，只有DEBUG或者强制的时候才输出
     * @param mixed $expression
     * @param boolean $coercion
     * @return void
     */
    function debug_dump($expression, $coercion = false)
    {
        if (DEBUG || $coercion) {
            $response = \tfc\ap\Ap::getResponse();
            if (!$response->headersSent()) {
                $response->contentType('text/html', \tfc\ap\Ap::getEncoding());
            }

            echo '<pre>';
            var_dump($expression);
            echo '</pre>';
            exit;
        }
    }
}

if (!function_exists('debug_print_r')) {
    /**
     * 测试打印数据，只有DEBUG或者强制的时候才输出
     * @param mixed $expression
     * @param boolean $coercion
     * @return void
     */
    function debug_print_r($expression, $coercion = false)
    {
        if (DEBUG || $coercion) {
            $response = \tfc\ap\Ap::getResponse();
            if (!$response->headersSent()) {
                $response->contentType('text/html', \tfc\ap\Ap::getEncoding());
            }

            echo '<pre>';
            print_r($expression);
            echo '</pre>';
            exit;
        }
    }
}
