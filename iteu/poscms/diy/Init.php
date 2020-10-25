<?php

define('EXT', '.php'); // PHP文件扩展名
define('SYSDIR', 'system'); // “系统文件夹”的名称
define('BASEPATH', FCPATH . 'system/'); // CI框架目录
define('VIEWPATH', FCPATH . 'dayrui/'); // 定义视图目录，我们把它当做主项目目录

require WEBPATH.'config/user_agents.php';

// 客户端判定
$host = strtolower($_SERVER['HTTP_HOST']);
$is_mobile = 0;
if ($mobiles) {
    foreach ($mobiles as $key => $val) {
        if (FALSE !== (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), $key))) {
            // 表示移动端
            $is_mobile = 1;
            break;
        }
    }
}

define('IS_PC', !$is_mobile); // 是否pc端
define('DOMAIN_NAME', $host); // 当前域名

// 解析自定义域名
if (is_file(WEBPATH . 'config/module_domain.php')){
    $domain = require WEBPATH . 'config/module_domain.php';
    if ($domain) {
        if (isset($domain[$host]) && $domain[$host]
            && is_dir(FCPATH.'module/'.$domain[$host])) {
            $_GET['s'] = $domain[$host]; // 强制定义为模块
        } elseif (isset($domain['space']) && $domain['space']
            && strpos($host, $domain['space'])) {
            $domain = require WEBPATH . 'config'.'/domain.php';
            $system = require WEBPATH . 'config'.'/system.php';
            !isset($domain[$host]) && $system['SYS_DOMAIN'] != $host && $_GET['s'] = 'space'; // 强制定义为模块
        }
    }
    unset($domain);
}

// 伪静态字符串
$uu = isset($_SERVER['HTTP_X_REWRITE_URL']) || trim($_SERVER['REQUEST_URI'], '/') == SELF ? trim($_SERVER['HTTP_X_REWRITE_URL'], '/') : ($_SERVER['REQUEST_URI'] ? trim($_SERVER['REQUEST_URI'], '/') : NULL);
$uri = strpos($uu, SELF) === 0 || strpos($uu, '?') === 0 ? '' : $uu; // 以index.php或者?开头的uri不做处理


if (!defined('IS_MEMBER')) {

    // 根据路由来匹配S变量
    if (!IS_ADMIN && $uri) {

        define('PAGE_CACHE_URL', ($is_mobile ? 'mobile-' : '').$host.'/'.ltrim($uri, '/'));
        // 加载单页缓存
        is_file(WEBPATH.'cache/page/'.md5(PAGE_CACHE_URL).'.html') && exit(file_get_contents(WEBPATH.'cache/page/'.md5(PAGE_CACHE_URL).'.html'));

        define('DR_URI', $uri);
        include WEBPATH.'config/routes.php';
        $rewrite = require WEBPATH.'config/rewrite.php';
        $routes = $rewrite && is_array($rewrite) && count($rewrite) > 0 ? array_merge($routes, $rewrite) : $routes;

        // 正则匹配路由规则
        $value = $u = '';
        foreach ($routes as $key => $val) {
            $match = array();
            if ($key == $uri || @preg_match('/^'.$key.'$/U', $uri, $match)) {
                unset($match[0]);
                $u = $val;
                $value = $match;
                break;
            }

        }
        if ($u) {
            if (strpos($u, '/') !== false) {
                // URI分段模式
                $array = explode('/', $u);
                $s = array_shift($array);
                if (is_dir(FCPATH.'module/'.$s) || is_dir(FCPATH.'app/'.$s)) {
                    $_GET['s'] = $s;
                    $_GET['c'] = array_shift($array);
                    $_GET['m'] = array_shift($array);
                } elseif (is_file(FCPATH.'dayrui/controllers/'.ucfirst($s).'.php')) {
                    $_GET['c'] = $s;
                    $_GET['m'] = array_shift($array);
                }
                // 组装GET参数
                if ($array) {
                    foreach ($array as $k => $t) {
                        $i%2 == 0 && $_GET[str_replace('$', '_', $t)] = isset($array[$k+1]) ? $array[$k+1] : '';
                        $i ++;
                    }
                    if ($value) {
                        foreach ($_GET as $k => $v) {
                            if (strpos($v, '$') !== FALSE) {
                                $id = (int)substr($v, 1);
                                $_GET[$k] = isset($value[$id]) ? $value[$id] : $v;
                            }
                        }
                    }
                }
            } else if (strpos($u, 'index.php?') === 0) {
                // URL参数模式
                $_GET = array();
                $queryParts = explode('&', str_replace('index.php?', '', $u));
                foreach ($queryParts as $param) {
                    $item = explode('=', $param);
                    $_GET[$item[0]] = $item[1];
                    if (strpos($item[1], '$') !== FALSE) {
                        $id = (int)substr($item[1], 1);
                        $_GET[$item[0]] = isset($match[$id]) ? $match[$id] : $item[1];
                    }
                }
            }
        } elseif (isset($_GET['s']) && !isset($_GET['c'])) {
            // 只存在唯一一个s参数时给他强制指向home控制器
            $_GET['c'] = 'home';
        }
    }
    // 判断s参数,“应用程序”文件夹目录
    if (isset($_GET['s']) && preg_match('/^[a-z]+$/i', $_GET['s'])) {
        // 判断会员模块,排除后台调用
        if (!IS_ADMIN && $_GET['s'] == 'member' && is_dir(FCPATH . 'module/' . $_GET['s'])) { // 会员
            if ($_GET['mod'] && is_dir(FCPATH . 'module/' . $_GET['mod'])) { // 模块
                define('APPPATH', FCPATH . 'module/' . $_GET['mod'] . '/');
                define('APP_DIR', $_GET['mod']); // 模块目录名称
                $_GET['d'] = 'member'; // 将项目标识作为directory
            } elseif ($_GET['app'] && is_dir(FCPATH . 'app/' . $_GET['app'] . '/')) { // 应用
                define('APPPATH', FCPATH . 'app/' . $_GET['app'] . '/');
                define('APP_DIR', $_GET['app']); // 应用目录名称
                $_GET['d'] = 'member'; // 将项目标识作为directory
            } else {
                define('APPPATH', FCPATH . 'module/member/');
                define('APP_DIR', 'member'); // 模块目录名称
            }
            define('IS_MEMBER', TRUE);
        } elseif (is_dir(FCPATH . 'module/' . $_GET['s'])) { // 模块
            define('APPPATH', FCPATH . 'module/' . $_GET['s'] . '/');
            define('APP_DIR', $_GET['s']); // 识别目录名称
            define('IS_MEMBER', FALSE);
            // 判断加载模块首页静态文件
            $file = WEBPATH.'cache/index/'.($is_mobile ? 'mobile-' : '').DOMAIN_NAME.'-'.APP_DIR.'.html';
            !$uu && is_file($file) && exit(file_get_contents($file));
        } elseif (is_dir(FCPATH . 'app/' . $_GET['s'] . '/')) { // 应用
            define('APPPATH', FCPATH . 'app/' . $_GET['s'] . '/');
            define('APP_DIR', $_GET['s']); // 应用目录名称
            define('IS_MEMBER', FALSE);
        }
        define('ENVIRONMENT', '../../../branch/extend');
    } else {
        // 系统主目录
        define('APPPATH', FCPATH . 'dayrui/');
        define('APP_DIR', '');
        define('IS_MEMBER', FALSE);
        define('ENVIRONMENT', '../../branch/extend');
        // 判断加载网站首页静态文件
        $file = WEBPATH.'cache/index/'.($is_mobile ? 'mobile-' : '').DOMAIN_NAME.'-home.html';
        !IS_ADMIN && !$uu && is_file($file) && exit(file_get_contents($file));
    }
} else {
    // 通过百度编辑器/api接口定义的会员模块
    define('APPPATH', FCPATH . 'module/member/');
    define('APP_DIR', 'member');
    define('ENVIRONMENT', '../../../branch/extend');
}

// 请求URI字符串
!defined('DR_URI') && define('DR_URI', '');

if (!is_file(APPPATH.'config/constants.php')) {
    @copy(FCPATH.'branch/extend/constants.php', APPPATH.'config/constants.php')
    or exit('文件写失败：'.(defined('APP_DIR') ? APP_DIR : '').'/config/constants.php');
}

function &load_class($class, $directory = 'libraries', $param = NULL)
{
    static $_classes = array();


    // Does the class exist? If so, we're done...
    if (isset($_classes[$class]))
    {
        return $_classes[$class];
    }

    $name = FALSE;

    // Look for the class first in the local application/libraries folder
    // then in the native system/libraries folder
    foreach (array(APPPATH, BASEPATH) as $path)
    {
        if (file_exists($path.$directory.'/'.$class.'.php'))
        {
            $name = 'CI_'.$class;

            if (class_exists($name, FALSE) === FALSE)
            {
                require_once($path.$directory.'/'.$class.'.php');
            }

            break;
        }
    }

    // Is the request a class extension? If so we load it too
    if (file_exists(APPPATH.$directory.'/'.config_item('subclass_prefix').$class.'.php'))
    {
        $name = config_item('subclass_prefix').$class;

        if (class_exists($name, FALSE) === FALSE)
        {
            require_once(APPPATH.$directory.'/'.$name.'.php');
        }
    } elseif ($directory == 'core' && is_file(FCPATH.'branch/extend/Finecms_'.$class.'.php')) {
        $name = config_item('subclass_prefix').$class;
        if (class_exists($name, FALSE) === FALSE)
        {
            require_once(FCPATH.'branch/extend/Finecms_'.$class.'.php');
        }
    } elseif ($directory == 'libraries' && is_file(FCPATH.'branch/extend/Libraries_'.$class.'.php')) {
        $name = config_item('subclass_prefix').$class;
        if (class_exists($name, FALSE) === FALSE)
        {
            require_once(FCPATH.'branch/extend/Libraries_'.$class.'.php');
        }
    } else {
        $name = 'CI_'.$class;
    }

    // Did we find the class?
    if (class_exists($name, FALSE) === FALSE)
    {
        // Note: We use exit() rather than show_error() in order to avoid a
        // self-referencing loop with the Exceptions class
        set_status_header(503);
        echo 'Unable to locate the specified class: '.$name.'.php';
        exit(5); // EXIT_UNK_CLASS
    }
    // Keep track of what we just loaded
    is_loaded($class);

    $_classes[$class] = isset($param)
        ? new $name($param)
        : new $name();
    return $_classes[$class];
}

require BASEPATH . 'core/CodeIgniter.php'; // CI框架核心文件