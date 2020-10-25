<?php
namespace z;

class z
{
    final public static function start()
    {
        self::loadMapping();
        self::loadFunctions();
        self::setSession();
        self::setInput();
        headers_sent() || header('Content-type: text/html; charset=utf-8');
        header('X-Powered-By: ' . ($GLOBALS['ZPHP_CONFIG']['POWEREDBY'] ?? 'Z-PHP'));
        $ctrl = '\\ctrl\\' . ROUTE['ctrl'];
        $act = ROUTE['act'];
        is_file($file = $GLOBALS['ZPHP_MAPPING']['ctrl'] . ROUTE['ctrl'] . '.class.php') && require $file;
        if ($GLOBALS['ZPHP_CONFIG']['DEBUG']['level'] < 2) {
            if (!class_exists($ctrl, false)) {
                ctrl::_404();
            }
            method_exists($ctrl, $act) || (method_exists($ctrl, '_404') ? $ctrl::_404() : ctrl::_404());
        }
        method_exists($ctrl, 'init') && $ctrl::init();
        $result = $ctrl::$act();
        method_exists($ctrl, 'after') && $ctrl::after();
        isset($result) ? die(ctrl::json($result)) : debug::ShowMsg();
        die;
    }
    private static function setSession()
    {
        if (isset($GLOBALS['ZPHP_CONFIG']['SESSION']['auto']) && !$GLOBALS['ZPHP_CONFIG']['SESSION']['auto']) {
            return;
        }
        self::SessionStart();
    }
    public static function SessionStart()
    {
        if (!empty($GLOBALS['ZPHP_CONFIG']['SESSION']['name'])) {
            $org = session_name($GLOBALS['ZPHP_CONFIG']['SESSION']['name']);
            isset($_COOKIE[$org]) && setcookie($org, '', 0, '/');
        }
        if (!empty($GLOBALS['ZPHP_CONFIG']['SESSION']['httponly'])) {
            ini_set('session.cookie_httponly', true);
        }
        if (!empty($GLOBALS['ZPHP_CONFIG']['SESSION']['redis'])) {
            $cfg = empty($GLOBALS['ZPHP_CONFIG']['SESSION']['host']) ? $GLOBALS['ZPHP_CONFIG']['REDIS'] : $GLOBALS['ZPHP_CONFIG']['SESSION'];
            $database = $GLOBALS['ZPHP_CONFIG']['SESSION']['database'] ?? 1;
            $session_path = "tcp://{$cfg['host']}:{$cfg['port']}?database={$database}";
            empty($cfg['pass']) || $session_path .= "&auth={$cfg['pass']}";
            ini_set('session.save_handler', 'redis');
            ini_set('session.save_path', $session_path);
        }
        session_start();
    }
    public static function AutoLoad(string $r)
    {
        if (false !== strpos($r, '\\')) {
            $path_arr = explode('\\', $r);
            $path_root = array_shift($path_arr);
            if (!isset($GLOBALS['ZPHP_MAPPING'][$path_root])) {
                if (empty($GLOBALS['ZPHP_AUTOLOAD'])) {
                    throw new \Exception("命名空间 {$path_root} 未做映射");
                } else {
                    return $GLOBALS['ZPHP_AUTOLOAD']($r);
                }

            }
            $fileName = array_pop($path_arr);
            $sub_path = $path_arr ? implode('/', $path_arr) . '/' : '';
            $path = "{$GLOBALS['ZPHP_MAPPING'][$path_root]}{$sub_path}";
            if (is_file($file = "{$path}{$fileName}.class.php") || is_file($file = "{$path}{$fileName}.php")) {
                require $file;
            } else {
                throw new \Exception("file not fond: {$path}{$fileName}.class.php");
            }
        } else {
            empty($GLOBALS['ZPHP_AUTOLOAD']) || $GLOBALS['ZPHP_AUTOLOAD']($r);
        }
    }
    public static function LoadConfig($conf = false)
    {
        if ($conf) {
            is_file($conf) && is_array($conf = require $conf) && $GLOBALS['ZPHP_CONFIG'] = $conf + $GLOBALS['ZPHP_CONFIG'];
        } else {
            $GLOBALS['ZPHP_CONFIG'] = is_file($file = P_APP . 'config.php') && is_array($conf = require $file) ? $conf : [];
            is_file($file = P_COMMON . 'config.php') && is_array($conf = require $file) && $GLOBALS['ZPHP_CONFIG'] += $conf;
        }
    }
    public static function loadFunctions()
    {
        is_file($file = P_COMMON . 'functions.php') && require $file;
        is_file($file = P_APP . 'functions.php') && require $file;
        if (defined('P_MODULE')) {
            is_file($file = P_APP_VER . 'functions.php') && require $file;
            is_file($file = P_MODULE . 'common/functions.php') && require $file;
        } else {
            is_file($file = P_APP_VER . 'common/functions.php') && require $file;
        }
    }
    public static function SetConfig(string $key, $value)
    {
        is_array($value) ? $GLOBALS['ZPHP_CONFIG'][$key] = $value + $GLOBALS['ZPHP_CONFIG'][$key] : $GLOBALS['ZPHP_CONFIG'][$key] = $value;
    }
    public static function GetConfig($key = '')
    {
        return $key ? $GLOBALS['ZPHP_CONFIG'][$key] : $GLOBALS['ZPHP_CONFIG'];
    }
    private static function loadMapping()
    {
        is_file($file = P_APP_VER . 'common/mapping.php') && is_array($map = require $file) && $GLOBALS['ZPHP_MAPPING'] += $map;
        is_file($file = P_COMMON . 'mapping.php') && is_array($map = require $file) && $GLOBALS['ZPHP_MAPPING'] += $map;
        $path = isset(ROUTE['module']) ? P_APP_VER . ROUTE['module'] . '/' : P_APP_VER;
        $GLOBALS['ZPHP_MAPPING'] += [
            'app' => P_APP_VER,
            'module' => "{$path}",
            'ctrl' => "{$path}ctrl/",
            'model' => "{$path}model/",
            'lib' => "{$path}lib/",
            'base' => "{$path}base/",
        ];
    }
    private static function setInput()
    {
        $I['INPUT'] = file_get_contents('php://input');
        if (isset($_SERVER['CONTENT_TYPE'])) {
            $H = explode(';', $_SERVER['CONTENT_TYPE']);
            if ('POST' === $_SERVER['REQUEST_METHOD']) {
                'application/json' === $H[0] && $_POST += json_decode($I['INPUT'], true);
            } else {
                switch ($H[0]) {
                    case 'application/json':
                        $I[$_SERVER['REQUEST_METHOD']] = json_decode($I['INPUT'], true);
                        break;
                    case 'application/x-www-form-urlencoded':
                        parse_str($I['INPUT'], $I[$_SERVER['REQUEST_METHOD']]);
                        break;
                }
            }
        }
        define('DATA', $I);
    }
}

class router
{
    const VER_PREFIX = 'v';
    private static $IS_MODULE = 0,
    $MOD = 0,
    $VER = [],
    $ROUTER = [],
    $FORMAT = [],
    $APP_ISMODULE,
        $APP_MAP;
    public static function init()
    {
        z::LoadConfig();
        self::setVer();
        z::LoadConfig(P_APP_VER . 'config.php');
        self::$IS_MODULE = !empty($GLOBALS['ZPHP_CONFIG']['ROUTER']['module']);
        self::$IS_MODULE || z::LoadConfig(P_APP_VER . 'common/config.php');
        self::$MOD = $GLOBALS['ZPHP_CONFIG']['ROUTER']['mod'] ?? 'auto';
        define('U_RES_VER', VER ? U_RES_APP . '/' . self::VER_PREFIX . VER : U_RES_APP);
        define('TPL_EXT', $GLOBALS['ZPHP_CONFIG']['VIEW']['ext'] ?? '.html');
        define('THEME', $GLOBALS['ZPHP_CONFIG']['VIEW']['theme'] ?? 'default');
        define('P_VIEW_APP', P_APP_VER . 'view/');
        define('P_THEME_APP', P_VIEW_APP . THEME . '/');
        $pathinfo = self::getPathInfo();
        switch (self::$MOD) {
            case 0:
                $route = self::defaultRoute();
                break;
            case 1:
                $route = self::pathinfoRoute($pathinfo);
                break;
            case 2:
            case 3:
                if (!$router = self::router()) {
                    if (self::$IS_MODULE) {
                        $router = [];
                    } else {
                        throw new \Exception('没有找到路由配置');
                    }
                }
                $route = self::route($pathinfo, $router);
                break;
            default:
                if ($router = self::router()) {
                    self::$MOD = 2;
                    $route = self::route($pathinfo, $router);
                } elseif ($pathinfo) {
                    self::$MOD = 1;
                    $route = self::pathinfoRoute($pathinfo);
                } else {
                    self::$MOD = 0;
                    $route = self::defaultRoute();
                }
                break;
        }
        $route['query'] = isset($route['params']) ? $route['params'] + $_GET : $_GET;
        $route['uri'] = $_SERVER['REQUEST_URI'];
        if (isset($route['module'])) {
            $module_path = VER ? APP_NAME . '/' . self::VER_PREFIX . VER . "/{$route['module']}" : APP_NAME . "/{$route['module']}";
            define('P_MODULE', P_APP_VER . $route['module'] . '/');
            define('P_RES_MODULE', P_RES . $module_path . '/');
            define('P_RUN_MODULE', P_RUN . $module_path . '/');
            define('P_HTML_MODULE', P_HTML . $module_path . '/');
            define('P_CACHE_MODULE', P_CACHE . $module_path . '/');
            define('U_RES_MODULE', U_RES . "/{$module_path}");
            define('U_RES_', U_RES_MODULE);
            define('P_RES_', P_RES_MODULE);
            define('P_RUN_', P_RUN_MODULE);
            define('P_HTML_', P_HTML_MODULE);
            define('P_CACHE_', P_CACHE_MODULE);
            define('P_VIEW_MODULE', P_MODULE . 'view/');
            define('P_VIEW_', P_VIEW_MODULE);
            define('P_THEME_MODULE', P_VIEW_MODULE . THEME . '/');
            define('P_THEME_', P_THEME_MODULE);
            if (is_file($file = P_MODULE . 'common/config.php') && $conf = require ($file)) {
                foreach ($conf as $k => $v) {
                    SetConfig($k, $v);
                }
            }
        } else {
            define('P_RES_', P_RES_APP);
            define('P_RUN_', P_RUN_APP);
            define('P_HTML_', P_HTML_APP);
            define('P_CACHE_', P_CACHE_APP);
            define('U_RES_', U_RES_APP);
            define('P_VIEW_', P_VIEW_APP);
            define('P_THEME_', P_THEME_APP);
        }
        define('ROUTE', $route);
    }

    private static function getPathInfo()
    {
        if (isset($_SERVER['DOCUMENT_URI'])) {
            $pathinfo = substr($_SERVER['DOCUMENT_URI'], strlen($_SERVER['SCRIPT_NAME']));
        } else {
            $pathinfo = $_SERVER['PATH_INFO'] ?? $_SERVER['REDIRECT_PATH_INFO'] ?? '';
        }
        $pathinfo && $pathinfo = trim($pathinfo, '/');
        return $pathinfo;
    }
    private static function router(string $name = '', string $ver = '')
    {
        $name || $name = APP_NAME;
        $ver || $ver = self::getVer($name);
        $path = P_ROOT . "app/{$name}/";
        $ver && $ver = '/' . self::VER_PREFIX . $ver;
        $router = is_file($file = "{$path}{$ver}/common/router.php") || is_file($file = "{$path}{$ver}/router.php") || is_file($file = "{$path}/router.php") ? require $file : false;
        isset($router['PATH']) && $router['PATH'] = trim($router['PATH'], '/');
        self::$ROUTER["{$name}-{$ver}"] = $router;
        return $router;
    }
    private static function getModuleRouter(string $m, string $name = '', string $ver = '')
    {
        $name || $name = APP_NAME;
        $ver || $ver = self::getVer($name);
        $key = "{$name}-{$ver}";
        $M = "+{$m}";
        if (isset(self::$ROUTER[$key][$M])) {
            return self::$ROUTER[$key][$M];
        }
        $ver && $ver = '/' . self::VER_PREFIX . $ver;
        $module = P_ROOT . "app/{$name}{$ver}/{$m}/";
        $router = is_file($file = "{$module}common/router.php") ? require $file : false;
        if (isset(self::$ROUTER[$key][$m]) && is_array(self::$ROUTER[$key][$m])) {
            $router = $router ? $router + self::$ROUTER[$key][$m] : self::$ROUTER[$key][$m];
        }
        empty(self::$ROUTER[$key]) ? self::$ROUTER[$key] = [$M => $router] : self::$ROUTER[$key][$M] = $router;
        return $router;
    }
    private static function getVer(string $name)
    {
        if (isset(self::$VER[$name])) {
            return self::$VER[$name];
        }
        $path = P_ROOT . "app/{$name}/";
        if ($conf = is_file($file = "{$path}config.php") ? require $file : false) {
            self::$VER[$name] = $conf['VER'][1] ?? $conf['VER'][0] ?? null;
        }
        self::$VER[$name] ?? self::$VER[$name] = VER;
        return self::$VER[$name];
    }
    private static function getIsmodule(string $app, string $ver)
    {
        if ($app === APP_NAME) {
            return self::$IS_MODULE;
        }
        $ver || $ver = self::getVer($app);
        $ver && $ver = '/' . self::VER_PREFIX . $ver;
        if (!isset(self::$APP_ISMODULE[$app])) {
            if (is_file($file = P_ROOT . "app/{$app}{$ver}/config.php") && $config = require $file) {
                $ismodule = $config['ROUTER']['module'] ?? null;
            }
            if (!isset($ismodule) && is_file($file = P_ROOT . "app/{$app}/config.php") && $config = require $file) {
                $ismodule = $config['ROUTER']['module'] ?? null;
            }
            if (!isset($ismodule) && is_file($file = P_ROOT . 'common/config.php') && $config = require $file) {
                $ismodule = $config['ROUTER']['module'] ?? false;
            }
            self::$APP_ISMODULE[$app] = $ismodule;
        }
        return self::$APP_ISMODULE[$app];
    }
    private static function getAppName(string $php)
    {
        if (isset(self::$APP_MAP[$php])) {
            return self::$APP_MAP[$php];
        }
        if (is_file($file = P_IN . $php) && $str = file_get_contents($file)) {
            $preg = '/define.+\,\s*\'(\w+)\'\s*\)/';
            preg_match($preg, $str, $match);
            self::$APP_MAP[$php] = $match[1] ?? false;
        }
        return self::$APP_MAP[$php];
    }
    public static function setVer()
    {
        if (isset($GLOBALS['ZPHP_CONFIG']['VER'][1])) {
            define('VER', $GLOBALS['ZPHP_CONFIG']['VER'][1]);
        } elseif (isset($_GET['ver']) && ($ver = trim($_GET['ver'])) && file_exists($path = P_APP . self::VER_PREFIX . "{$ver}/")) {
            define('VER', $ver);
            define('P_APP_VER', $path);
        } elseif (($key = $GLOBALS['ZPHP_CONFIG']['HEADER_VER'] ?? false) && ($ver = $_SERVER["HTTP_{$key}"] ?? '')) {
            define('VER', $ver);
        } else {
            define('VER', $GLOBALS['ZPHP_CONFIG']['VER'][0] ?? '');
        }
        if (!defined('P_APP_VER')) {
            $path = VER ? P_APP . self::VER_PREFIX . VER . '/' : P_APP;
            if (!file_exists($path)) {
                throw new \Exception("directory does not exist: {$path}");
            }
            define('P_APP_VER', $path);
        }
        $app_path = VER ? APP_NAME . '/' . self::VER_PREFIX . VER . '/' : APP_NAME . '/';
        self::$VER[APP_NAME] = VER;
        define('P_RES_VER', P_PUBLIC . 'res/' . $app_path);
        define('P_RUN_VER', P_RUN . $app_path);
        define('P_HTML_VER', P_HTML . $app_path);
        define('P_CACHE_VER', P_CACHE . $app_path);
    }
    private static function format(string $name, $m, string $ver)
    {
        $name || $name = APP_NAME;
        $ver || $ver = self::getVer($name);
        $key = "{$name}-{$ver}-{$m}";
        if (isset(self::$FORMAT[$key])) {
            return self::$FORMAT[$key];
        }
        if (!$router = $m ? self::getModuleRouter($m, $name, $ver) : (self::$ROUTER["{$name}-{$ver}"] ?? self::router($name, $ver))) {
            $data = false;
        } else {
            if (isset($router['PATH'])) {
                $data[0] = $router['PATH'] ?: '';
                unset($router['PATH']);
            } else {
                $data[0] = '';
            }
            foreach ($router as $k => $v) {
                if ('*' === $k || '/' !== $k[0] || isset($v['module'])) {
                    continue;
                }
                $ctrl = $v['ctrl'] ?? 'index';
                $act = $v['act'] ?? 'index';
                $a = str_replace('*', '', $act);
                $d = [$k, $v['params'] ?? false];
                if ($a !== $act) {
                    $data[$ctrl]['*'][$a] = $d;
                } else {
                    $data[$ctrl][$act] = $d;
                }
            }
        }
        self::$FORMAT[$key] = $data;
        return $data;
    }
    private static function getUf($path, $ver)
    {
        if (!$path || '/' === $path) {
            self::$IS_MODULE && $uf['m'] = ROUTE['module'];
            $uf['c'] = ROUTE['ctrl'];
            $uf['a'] = 'index';
            return $uf;
        }
        $arr = is_array($path) ? $path : explode('/', $path);
        if ('.php' === substr($arr[0], -4)) {
            $uf['app'][0] = $arr[0];
            $uf['app'][1] = self::getAppName($uf['app'][0]);
            if ($ismodule = self::getIsmodule($uf['app'][1], $ver)) {
                if (4 !== count($arr)) {
                    throw new \Exception('RUL(参数错误)，格式："入口文件名/模块名/控制器/操作"');
                }

                $uf['m'] = $arr[1];
                $uf['c'] = $arr[2];
                $uf['a'] = $arr[3];
            } else {
                if (3 !== count($arr)) {
                    throw new \Exception('URL(参数错误)，格式："入口文件名/控制器/操作"');
                }

                $uf['c'] = $arr[1];
                $uf['a'] = $arr[2];
            }
        } elseif (self::$IS_MODULE) {
            switch (count($arr)) {
                case 1:
                    $uf['m'] = ROUTE['module'];
                    $uf['c'] = ROUTE['ctrl'];
                    $uf['a'] = $arr[0];
                    break;
                case 2:
                    $uf['m'] = ROUTE['module'];
                    $uf['c'] = $arr[0];
                    $uf['a'] = $arr[1];
                    break;
                case 3:
                    $uf['m'] = $arr[0];
                    $uf['c'] = $arr[1];
                    $uf['a'] = $arr[2];
                    break;
            }
        } else {
            switch (count($arr)) {
                case 1:
                    $uf['c'] = ROUTE['ctrl'];
                    $uf['a'] = $arr[0];
                    break;
                case 2:
                    $uf['c'] = $arr[0];
                    $uf['a'] = $arr[1];
                    break;
            }
        }
        return $uf;
    }
    private static function getInPath($info, $ver = '', $param = false)
    {
        if (isset($info['app'])) {
            $php = $info['app'][0];
            $app = $info['app'][1];
        } else {
            $php = PHP_FILE;
            $app = APP_NAME;
        }
        $m = $info['m'] ?? ROUTE['module'] ?? false;
        if ($route = self::format($app, $m, $ver)) {
            $url = isset($route[0]) ? U_HOME . $route[0] : U_ROOT;
        } else {
            $url = !$param && 'index.php' === $php ? U_ROOT : U_HOME . $php;
        }
        return $url;
    }
    public static function U0($path, $args, $ver = '')
    {
        $Q = self::getUf($path, $ver);
        $url = self::getInPath($Q, $ver);
        if (isset($Q['m']) && 'index' === $Q['m']) {
            unset($Q['m']);
        }
        if (isset($Q['app'])) {
            unset($Q['app']);
        }
        if ('index' === $Q['c']) {
            unset($Q['c']);
        }
        if ('index' === $Q['a']) {
            unset($Q['a']);
        }
        if ($args) {
            empty($args['params']) || $Q += $args['params'];
            empty($args['query']) || $Q += $args['query'];
            if (!isset($args['params']) && !isset($args['query'])) {
                $Q += $args;
            }
        }
        $ver && $Q['ver'] = $ver;
        $Q && $url .= '?' . http_build_query($Q);
        return $url;
    }

    public static function U1($path, $args, $ver = '')
    {
        $info = self::getUf($path, $ver);
        $url = self::getInPath($info, $ver, !empty($args['params']));
        $m = isset($info['m']) ? "/{$info['m']}" : '';
        if (empty($args['params'])) {
            if ('index' !== $info['a']) {
                $url .= "{$m}/{$info['c']}/{$info['a']}";
            } elseif ('index' !== $info['c']) {
                $url .= "{$m}/{$info['c']}";
            } elseif ($m && '/index' !== $m) {
                $url .= $m;
            }
        } else {
            $url .= "{$m}/{$info['c']}/{$info['a']}";
            foreach ($args['params'] as $k => $v) {
                $url .= "/{$k}/{$v}";
            }
        }
        $ver && $args['query']['ver'] = $ver;
        empty($args['query']) || $url .= '?' . http_build_query($args['query']);
        return $url;
    }

    public static function U2($path, $args, $ver = '')
    {
        $info = self::getUf($path, $ver);
        $app = $info['app'][1] ?? APP_NAME;
        $m = $info['m'] ?? '';
        $c = $info['c'];
        $a = $info['a'];
        if (!$data = self::format($app, $m, $ver)) {
            throw new \Exception("没有配置路由，[app：{$app}，ver：{$ver}]");
        }
        $url = $m ? ($data[0] ? "{$data[0]}/{$m}" : $m) : $data[0];
        $url = $url ? U_HOME . $url : U_ROOT;
        if (isset($data[$c][$a])) {
            $route = $data[$c][$a];
        } elseif (isset($data[$c]['*'])) {
            foreach ($data[$c]['*'] as $k => $v) {
                if ('' !== $k && false !== strpos($a, $k)) {
                    $route = $v;
                    $a = str_replace($k, '', $a);
                    break;
                }
            }
            $route ?? $route = $data[$c]['*'][''] ?? null;
            $route && $route[0] .= '/' . $a;
        } else {
            throw new \Exception("没有匹配到路由，[ctrl：{$c}，act：{$a}]");
        }
        if (isset($route)) {
            $route[0] && $url .= $route[0];
            if (isset($args['params']) && $route[1]) {
                $i = 0;
                foreach ($route[1] as $k => $v) {
                    if ($k === $i) {
                        ++$i;
                        $key = $v;
                    } else {
                        $key = $k;
                    }
                    if (isset($args['params'][$key])) {
                        $params[] = $args['params'][$key];
                        unset($args['params'][$key]);
                    }
                }
            }
        }
        $query = $args['params'] ?? [];
        empty($args['query']) || $query += $args['query'];
        isset($params) && $url .= '/' . implode('/', $params);
        $ver && $query['ver'] = $ver;
        $query && $url .= '?' . http_build_query($query);
        return $url;
    }

    public static function Url($path, array $args = [], string $ver = '', $mod = null)
    {
        isset($mod) || $mod = $GLOBALS['ZPHP_CONFIG']['ROUTER']['mod'] ?? self::$MOD;
        if (is_array($mod)) {
            return self::U1($path, $args, $ver, $mod);
        }
        switch ($mod) {
            case 0:
                $url = self::U0($path, $args, $ver);
                break;
            case 1:
                $url = self::U1($path, $args, $ver);
                break;
            case 2:
                $url = self::U2($path, $args, $ver);
                break;
            default:
                throw new \Exception('url参数4错误');
        }
        return $url;
    }
    private static function defaultRoute()
    {
        self::$IS_MODULE && $route['module'] = $_GET['m'] ?: 'index';
        if (isset($_GET['c'])) {
            $route['ctrl'] = $_GET['c'] ?: 'index';
            unset($_GET['c']);
        } else {
            $route['ctrl'] = 'index';
        }
        if (!empty($GLOBALS['ZPHP_CONFIG']['ROUTER']['restfull'])) {
            $act = strtolower($_SERVER['REQUEST_METHOD']);
            $route['act'] = $GLOBALS['ZPHP_CONFIG']['ROUTER']['restfull'][$act] ?? $act;
        } elseif (isset($_GET['a'])) {
            $route['act'] = $_GET['a'] ?: 'index';
            unset($_GET['a']);
        } else {
            $route['act'] = 'index';
        }
        return $route;
    }
    private static function pathinfo2arr(string $pathinfo)
    {
        $params = $pathinfo ? explode('/', $pathinfo) : ['index'];
        self::$IS_MODULE && $info['module'] = array_shift($params);
        $info['ctrl'] = $params ? array_shift($params) : 'index';
        if (!empty($GLOBALS['ZPHP_CONFIG']['ROUTER']['restfull']) && $act = strtolower($_SERVER['REQUEST_METHOD'])) {
            $act = $GLOBALS['ZPHP_CONFIG']['ROUTER']['restfull'][$act] ?? $act;
        }
        return [$info, $params, $act ?? false];
    }
    private static function pathinfoRoute($pathinfo)
    {
        list($route, $params, $act) = self::pathinfo2arr($pathinfo);
        $route['act'] = $act ?: ($params ? array_shift($params) : 'index');
        $route['path'] = $params;
        $route['params'] = [];
        if ($params && $params = array_chunk($params, 2)) {
            foreach ($params as $v) {
                $route['params'][$v[0]] = $v[1] ?? '';
            }
        }
        return $route;
    }
    private static function route(string $pathinfo, array $router)
    {
        list($info, $arr, $act) = self::pathinfo2arr($pathinfo);
        if (isset($info['module']) && !$router = self::getModuleRouter($info['module'])) {
            throw new \Exception("没有{$info['module']}模块的路由");
        }
        if ($act && isset($router["/{$info['ctrl']}/{$act}"])) {
            $route = $router["/{$info['ctrl']}/{$act}"];
        } elseif(isset($arr[0]) && isset($router["/{$info['ctrl']}/{$arr[0]}"])) {
            $act = array_shift($arr);
            $route = $router["/{$info['ctrl']}/{$act}"];
        } elseif (!$route = $router["/{$info['ctrl']}/*"] ?? $router["/{$info['ctrl']}"] ?? false) {
            if (!$route = 'index' === $info['ctrl'] && !$arr ? $router['/'] ?? false : $router['*'] ?? false) {
                throw new \Exception('没有匹配到路由, 不想看到此错误请配置 * 路由');
            }
        }
        if (empty($route['ctrl']) || empty($route['act'])) {
            throw new \Exception('必须设置路由的 ctrl 和 act');
        }
        if (false !== strpos($route['act'], '*') && $replace = array_shift($arr) ?: 'index') {
            $route['act'] = str_replace('*', $replace, $route['act']);
        }
        isset($route['module']) || isset($info['module']) && $route['module'] = $info['module'];
        if (isset($route['params'])) {
            $ii = 0;
            $n = 0;
            $ii = 0;
            foreach ($route['params'] as $k => $v) {
                if ($ii === $k) {
                    $key = $v;
                    $value = null;
                } else {
                    $key = $k;
                    $value = $v;
                }
                $params[$key] = $arr[$n] ?? $value;
                ++$n && is_int($k) && ++$ii;
            }
        }
        $route['params'] = $params ?? [];
        $route['path'] = $arr;
        return $route;
    }
}

class ctrl
{
    public static function _404()
    {
        $args = func_get_args();
        $errMsg = $args[0] ?? '404，您请求的文件不存在！';
        if (isset($args[1])) {
            $tpl = view::GetTpl($args[1], true);
            is_file($tpl) || $tpl = P_ROOT . $args[1];
        } else {
            $tpl = P_THEME_ . '404.html';
            is_file($tpl) || is_file($tpl = P_ROOT . '404.html') || $tpl = P_CORE . 'tpl/404.tpl';
        }
        ob_end_clean();
        require $tpl;
        die;
    }

    public static function _500()
    {
        $args = func_get_args();
        $errMsg = $args[0] ?? '500，出错啦！';
        if (isset($args[1])) {
            $tpl = view::GetTpl($args[1], true);
        } else {
            $tpl = P_THEME_ . '500.html';
            is_file($tpl) || is_file($tpl = P_ROOT . '500.html') || $tpl = P_CORE . 'tpl/500.tpl';
        }
        ob_end_clean();
        require $tpl;
        die;
    }

    public static function json($data)
    {
        ob_end_clean();
        header('Content-Type:application/json; charset=utf-8');
        die(json_encode($data, 320));
    }
}
class debug
{
    const ERRTYPE = [2 => '运行警告', 8 => '运行提醒', 256 => '错误', 512 => '警告', 1024 => '提醒', 2048 => '编码标准化警告', 1120 => 'SQL查询', 1130 => '环境', 1131 => '常量', 1132 => '配置', 1133 => '命名空间', 1140 => '模板文件', 1150 => '模板变量', 1160 => 'POST', 8192 => '运行通知'];
    private static $pdotime = 0;
    private static $errs = [];
    public static function pdotime($time)
    {
        self::$pdotime += $time;
    }
    public static function exceptionHandler($e)
    {
        $level = $GLOBALS['ZPHP_CONFIG']['DEBUG']['level'] ?? 3;
        $log = $GLOBALS['ZPHP_CONFIG']['DEBUG']['log'] ?? 0;
        !$log && 2 > $level && z::_500();
        $line = $e->getLine();
        $file = $e->getFile();
        $msg = $e->getMessage() . " at [{$file} : {$line}]";
        $trace = $e->getTraceAsString();
        $trace = str_replace('\\\\', '\\', $trace);
        foreach ($e->getTrace() as $k => $v) {
            $v['args'] && $args["#{$k}"] = 1 === count($v['args']) ? $v['args'][0] : $v['args'];
        }
        $args_str = isset($args) ? P($args, false) : '';
        if ($log) {
            $str = $msg . PHP_EOL . $trace . PHP_EOL;
            $args_str && $str .= 'args: ' . str_replace("\n", PHP_EOL, $args_str);
            self::log($str, 'error');
        }
        if ($level > 1) {
            header('status: 500');
            $type = $GLOBALS['ZPHP_CONFIG']['DEBUG']['type'] ?? 'html';
            if ('json' === $type) {
                $err = ['errMsg' => $msg, 'trace' => $trace];
                isset($args) && $err['args'] = $args;
                ctrl::json($err);
            } else {
                echo "<style>body{margin:0;padding:0;}</style><div style='background:#FFBBDD;padding:1rem;'><h2>ERROR!</h2><h3>{$msg}</h3>";
                echo '<strong><pre>' . $trace . '</pre></strong>';
                if (isset($args)) {
                    echo '<h3>参数：</h3>';
                    P($args);
                }
                die('</div>');
            }
        }
    }
    private static function log($str, $type)
    {
        $dir = P_TMP . "/{$type}_log/" . APP_NAME;
        !file_exists($dir) && !mkdir($dir, 0755, true);
        $file = $dir . '/' . date('Y-m-d') . '.log';
        $str = '[' . date('H:i:s') . "] {$str}";
        file_put_contents($file, $str . PHP_EOL, FILE_APPEND);
    }
    public static function setMsg($errno, $str)
    {
        self::$errs[$errno][] = $str;
    }
    public static function errorHandler($errno, $errstr, $errfile, $errline)
    {
        $level = $GLOBALS['ZPHP_CONFIG']['DEBUG']['level'] ?? 3;
        $log = $GLOBALS['ZPHP_CONFIG']['DEBUG']['log'] ?? 0;
        if ($level < 3 && $log < 2) {
            return;
        }

        $errstr = TransCode($errstr);
        $errfile = '[' . str_replace('\\', '/', $errfile) . " ] : {$errline}";
        $log > 1 && self::log("{$errstr} {$errfile}", 'warning');
        if ($level > 2) {
            IS_AJAX || $errstr = str_replace('\\', '\\\\', $errstr);
            self::$errs[$errno][] = "{$errstr} {$errfile}";
        }
    }
    public static function GetJsonDebug($level = null)
    {
        null === $level && $level = $GLOBALS['ZPHP_CONFIG']['DEBUG']['level'] ?? 0;
        if ($level) {
            $json['运行'] = [
                'SQL查询' => round(1000 * self::$pdotime, 3) . 'ms',
                '运行时间' => round(1000 * (microtime(true) - MTIME), 3) . 'ms',
                '内存使用' => FileSizeFormat(memory_get_usage()),
                '内存峰值' => FileSizeFormat(memory_get_peak_usage()),
            ];
        }
        if (2 < $level) {
            is_file($file = $GLOBALS['ZPHP_MAPPING']['libs'] . 'view.class.php')
            && (require $file)
            && !class_exists('\libs\view', false)
            && ($params = \libs\view::GetParams())
            && self::$errs[1150] = $params;
            $json['文件'] = get_included_files();
            $json['环境'] = $_SERVER;
            $json['POST'] = $_POST;
            $json['常量'] = get_defined_constants(true)['user'];
            $json['配置'] = $GLOBALS['ZPHP_CONFIG'];
            $json['命名空间'] = $GLOBALS['ZPHP_MAPPING'];
        }
        if (1 < $level) {
            foreach (self::$errs as $k => $v) {
                $json[self::ERRTYPE[$k]] = $v;
            }
        }
        return $json ?? null;
    }
    public static function ShowMsg()
    {
        if (!$level = $GLOBALS['ZPHP_CONFIG']['DEBUG']['level'] ?? 0) {
            die;
        }
        switch ($GLOBALS['ZPHP_CONFIG']['DEBUG']['type'] ?? '') {
            case 'html':
                self::ShowHtml($level);
                break;
            case 'json':
                self::ShowJson($level);
                break;
            default:
                IS_WX ? self::ShowHtml($level) : self::ShowJson($level);
                break;
        }
    }
    public static function ShowJson($level)
    {
        $json = json_encode(self::GetJsonDebug($level));
        die("<script>console.log({$json})</script>");
    }
    public static function ShowHtml($level)
    {
        $runtime = microtime(true) - MTIME;
        $html = $tab = '';
        if (2 < $level) {
            self::getConfigs();
            self::getServer();
            self::getConstants();
            self::getIncludeFiles();
            self::getMapping();
            self::getPost();
            self::getParams();
        }
        if (1 < $level) {
            foreach (self::$errs as $k => $v) {
                $tab .= "<button type=\"button\" id=\"{$k}\" tid=\"{$k}\">" . self::ERRTYPE[$k] . ':[' . count($v) . ']</button>';
                $html .= "<div id=\"zdebug-li{$k}\"><p># " . implode('</p><p># ', $v) . '</p></div>';
            }
        }
        require P_CORE . 'tpl/debug.tpl';
        die;
    }
    private static function getIncludeFiles()
    {
        $files = get_included_files();
        foreach ($files as $v) {
            $file = str_replace('\\', '/', $v);
            self::$errs[1100][] = $file . '[ ' . FileSizeFormat(filesize($file)) . ' ]';
        }
    }
    private static function getMapping()
    {
        if (isset($GLOBALS['ZPHP_MAPPING'])) {
            foreach ($GLOBALS['ZPHP_MAPPING'] as $k => $v) {
                $path = str_replace('\\', '/', $v);
                self::$errs[1133][] = "{$k}：$v";
            }
        }
    }
    private static function getConfigs()
    {
        foreach ($GLOBALS['ZPHP_CONFIG'] as $k => $v) {
            $str = htmlspecialchars(json_encode($v, 320));
            self::$errs[1132][] = "[{$k}] : {$str}";
        }
    }
    private static function getParams()
    {
        if (!$params = view::GetParams()) {
            return false;
        }

        foreach ($params as $k => $v) {
            $str = htmlspecialchars(json_encode($v, 320));
            self::$errs[1150][] = "\${$k} : {$str}";
        }
    }
    private static function getPost()
    {
        if ($_POST) {
            foreach ($_POST as $k => $v) {
                $str = htmlspecialchars(json_encode($v, 320));
                self::$errs[1160][] = "[{$k}] : {$str}";
            }
        }
    }
    private static function getConstants()
    {
        $const = get_defined_constants(true)['user'];
        foreach ($const as $k => $v) {
            $str = htmlspecialchars(json_encode($v, 320));
            self::$errs[1131][] = "[{$k}] : {$str}";
        }
    }
    private static function getServer()
    {
        foreach ($_SERVER as $k => $v) {
            $str = htmlspecialchars(json_encode($v, 320));
            self::$errs[1130][] = "[{$k}] : {$str}";
        }
    }
}
