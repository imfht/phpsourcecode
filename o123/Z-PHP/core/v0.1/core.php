<?php
use z\debug;
use z\router;
use z\z;
function AppRun($entry)
{
    define('ZPHP_VER', '4.1.0');
    error_reporting(E_ALL);
    $core = str_replace('\\', '/', dirname(__FILE__));
    $p = explode('/', $core);
    'core' === array_pop($p) || array_pop($p);
    $php = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
    define('ZPHP_OS', 0 === stripos(strtoupper(PHP_OS), 'WIN') ? 'WINDOWS' : 'LINUX');
    define('PHP_FILE', array_pop($php));
    define('U_ROOT', $php ? '/' . implode('/', $php) : '');
    define('U_HOME', U_ROOT . '/');
    define('U_TMP', U_HOME . 'tmp');
    define('TIME', $_SERVER['REQUEST_TIME']);
    define('MTIME', microtime(true));
    define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 'xmlhttprequest' === strtolower($_SERVER['HTTP_X_REQUESTED_WITH']));
    define('IS_WX', false !== strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger'));
    define('METHOD', $_SERVER['REQUEST_METHOD']);
    define('P_IN', str_replace('\\', '/', dirname($entry)) . '/');
    define('P_CORE', $core . '/');
    define('P_ROOT', implode('/', $p) . '/');
    define('P_TMP', P_ROOT . 'tmp/');
    define('P_BASE', P_ROOT . 'base/');
    define('P_LOG', P_ROOT . 'tmp/log/');
    define('P_RUN', P_ROOT . 'tmp/run/');
    define('P_HTML', P_ROOT . 'tmp/html/');
    define('P_CACHE', P_ROOT . 'tmp/cache/');
    define('P_APP', P_ROOT . 'app/' . APP_NAME . '/');
    define('P_COMMON', P_ROOT . 'common/');
    define('P_RUN_APP', P_RUN . APP_NAME . '/');
    define('P_HTML_APP', P_HTML . APP_NAME . '/');
    define('P_CACHE_APP', P_CACHE . APP_NAME . '/');
    define('LEN_IN', strlen(P_IN));
    if (P_IN === P_ROOT) {
        define('P_PUBLIC', P_IN . 'public/');
        define('U_PUBLIC', U_HOME . 'public');
    } else {
        define('P_PUBLIC', P_IN);
        define('U_PUBLIC', U_ROOT);
    }
    define('P_RES', P_PUBLIC . 'res/');
    define('P_RES_APP', P_RES . APP_NAME . '/');
    define('U_RES', U_PUBLIC . '/res');
    define('U_RES_APP', U_RES . '/' . APP_NAME);
    $GLOBALS['ZPHP_MAPPING'] = [
        'z' => P_CORE . 'z/',
        'ext' => P_CORE . 'ext/',
        'root' => P_ROOT,
        'libs' => P_ROOT . 'libs/',
        'common' => P_COMMON,
    ];
    require P_CORE . 'z/z.class.php';
    set_exception_handler('\z\debug::exceptionHandler');
    spl_autoload_register('\z\z::AutoLoad');
    router::init();
    ini_set('date.timezone', $GLOBALS['ZPHP_CONFIG']['TIME_ZONE'] ?? 'Asia/Shanghai');
    isset($GLOBALS['ZPHP_CONFIG']['DEBUG']['level']) || $GLOBALS['ZPHP_CONFIG']['DEBUG']['level'] = 3;
    if ($GLOBALS['ZPHP_CONFIG']['DEBUG']['level'] > 1) {
        ini_set('display_errors', 'On');
        set_error_handler('\z\debug::errorHandler');
    } else {
        ini_set('display_errors', 'Off');
        ini_set('expose_php', 'Off');
    }
    z::start();
}
function Zautoload(string $act)
{
    $GLOBALS['ZPHP_AUTOLOAD'] = $act;
}
function Debug(int $i, $msg = '')
{
    $GLOBALS['ZPHP_CONFIG']['DEBUG']['level'] = $i;
    $msg && $GLOBALS['ZPHP_CONFIG']['DEBUG']['type'] = $msg;
}
function IsFullPath(string $path): bool
{
    return 'WINDOWS' === ZPHP_OS ? ':' === $path[1] : '/' === $path[0];
}
function SetConfig(string $key, $value)
{
    if (isset($GLOBALS['ZPHP_CONFIG'][$key]) && is_array($value)) {
        $GLOBALS['ZPHP_CONFIG'][$key] = $value + $GLOBALS['ZPHP_CONFIG'][$key];
    } else {
        $GLOBALS['ZPHP_CONFIG'][$key] = $value;
    }
}
function ReadFileSH($file)
{
    $h = fopen($file, 'r');
    if (!flock($h, LOCK_SH)) {
        throw new \Exception('获取文件共享锁失败');
    }
    $result = fread($h, filesize($file));
    flock($h, LOCK_UN);
    fclose($h);
    return $result;
}
function P($var, bool $echo = true)
{
    ob_start();
    var_dump($var);
    $html = preg_replace('/\]\=\>\n(\s+)/m', '] =>', htmlspecialchars_decode(ob_get_clean()));
    if ($echo) {
        echo "<pre>{$html}</pre>";
    } else {
        return $html;
    }
}
function FileSizeFormat(int $size = 0, int $dec = 2): string
{
    $unit = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    $pos = 0;
    while ($size >= 1024) {
        $size /= 1024;
        ++$pos;
    }
    return round($size, $dec) . $unit[$pos];
}
function TransCode($str)
{
    $encode = mb_detect_encoding($str, ['ASCII', 'UTF-8', 'GB2312', 'GBK', 'BIG5', 'EUC-CN']);
    return 'UTF-8' === $encode ? $str : mb_convert_encoding($str, 'UTF-8', $encode);
}
function MakeDir($dir, $mode = 0755, $recursive = true)
{
    if (!file_exists($dir) && !mkdir($dir, $mode, $recursive)) {
        throw new Error("创建目录{$dir}失败,请检查权限");
    }
    return true;
}
function Page($cfg, $return = false)
{
    $var = $cfg['var'] ?? 'p';
    $data['rows'] = $cfg['rows'] ?? 0;
    $data['num'] = ($cfg['num'] ?? 10);
    $data['p'] = $cfg['p'] ?? (isset($_GET[$var]) ? (int) $_GET[$var] : 1);
    if (isset($cfg['max'])) {
        $maxRows = $data['num'] * $cfg['max'];
        if ($maxRows < $data['rows']) {
            $data['rows'] = $maxRows;
            $data['p'] > $cfg['max'] && $data['p'] = $cfg['max'];
        }
    }
    $data['pages'] = $data['rows'] ? (int) ceil($data['rows'] / $data['num']) : 1;
    $inrange = $cfg['inrange'] ?? true;
    $inrange && $data['pages'] < $data['p'] && $data['p'] = $data['pages'];
    $start = ($data['p'] - 1) * $data['num'];
    $data['limit'] = "{$start},{$data['num']}";
    if (!$return) {
        return $data['limit'];
    }
    switch ($data['pages'] <=> $data['p']) {
        case -1:
            $data['r'] = 0;
            break;
        case 0:
            $data['r'] = $data['rows'] % $data['num'] ?: ($data['rows'] ? $data['num'] : 0);
            break;
        case 1:
            $data['r'] = $data['num'];
            break;
    }
    if (is_array($return)) {
        $p = $data['p'];
        $var = $cfg['var'] ?? 'p';
        $ver = $cfg['ver'] ?? '';
        $mod = $cfg['mod'] ?? null;
        $nourl = $cfg['nourl'] ?? 'javascript:;';
        $params = ROUTE['params'] ?? false;
        $query = $_GET;
        foreach ($return as $v) {
            switch ($v) {
                case 'prev':
                    $params[$var] = $p - 1;
                    $data['prev'] = $params[$var] && $p !== $params[$var] ? router::url([ROUTE['ctrl'], ROUTE['act']], ['params' => $params, 'query' => $query], $ver, $mod) : $nourl;
                    break;
                case 'next':
                    $params[$var] = $p + 1;
                    $data['next'] = $data['pages'] > $p ? router::url([ROUTE['ctrl'], ROUTE['act']], ['params' => $params, 'query' => $query], $ver, $mod) : $nourl;
                    break;
                case 'first':
                    $params[$var] = 1;
                    $data['first'] = 1 === $p || 1 === $data['pages'] ? $nourl : router::url([ROUTE['ctrl'], ROUTE['act']], ['params' => $params, 'query' => $query], $ver, $mod);
                    break;
                case 'last':
                    $params[$var] = $data['pages'];
                    $data['last'] = 1 === $data['pages'] || $data['pages'] === $p ? $nourl : router::url([ROUTE['ctrl'], ROUTE['act']], ['params' => $params, 'query' => $query], $ver, $mod);
                    break;
                case 'list':
                    (int) $rolls = $cfg['rolls'] ?? 10;
                    if (1 < $data['pages']) {
                        $pos = intval($rolls / 2);
                        if ($pos < $p && $data['pages'] > $rolls) {
                            $i = $p - $pos;
                            $end = $i + $rolls - 1;
                            $end > $data['pages'] && ($end = $data['pages']) && ($i = $end - $rolls + 1);
                        } else {
                            $i = 1;
                            $end = $rolls > $data['pages'] ? $data['pages'] : $rolls;
                        }
                        for ($i; $i <= $end; $i++) {
                            $params[$var] = $i;
                            $data['list'][$i] = $p == $i ? 'javascript:;' : router::url([ROUTE['ctrl'], ROUTE['act']], ['params' => $params, 'query' => $query], $ver, $mod);
                        }
                    } else {
                        $data['list'] = [];
                    }
                    break;
            }
        }
    }
    return $data;
}
