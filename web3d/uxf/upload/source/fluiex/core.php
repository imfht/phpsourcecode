<?php

define('DISCUZ_CORE_FUNCTION', true);

use fluiex\Logger;
use fluiex\F as C;
use fluiex\util\Chinese;
use fluiex\util\Text as TextUtil;
use fluiex\util\AuthCode;

function durlencode($url)
{
    static $fix = array('%21', '%2A', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D');
    static $replacements = array('!', '*', ';', ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]");
    return str_replace($fix, $replacements, urlencode($url));
}

function setglobal($key, $value, $group = null)
{
    global $_G;
    $key = explode('/', $group === null ? $key : $group . '/' . $key);
    $p = &$_G;
    foreach ($key as $k) {
        if (!isset($p[$k]) || !is_array($p[$k])) {
            $p[$k] = array();
        }
        $p = &$p[$k];
    }
    $p = $value;
    return true;
}

function getglobal($key, $group = null)
{
    global $_G;
    $key = explode('/', $group === null ? $key : $group . '/' . $key);
    $v = &$_G;
    foreach ($key as $k) {
        if (!isset($v[$k])) {
            return null;
        }
        $v = &$v[$k];
    }
    return $v;
}

function getgpc($k, $type = 'GP')
{
    $type = strtoupper($type);
    switch ($type) {
        case 'G': $var = &$_GET;
            break;
        case 'P': $var = &$_POST;
            break;
        case 'C': $var = &$_COOKIE;
            break;
        default:
            if (isset($_GET[$k])) {
                $var = &$_GET;
            } else {
                $var = &$_POST;
            }
            break;
    }

    return isset($var[$k]) ? $var[$k] : NULL;
}

function daddslashes($string, $force = 1)
{
    if (is_array($string)) {
        $keys = array_keys($string);
        foreach ($keys as $key) {
            $val = $string[$key];
            unset($string[$key]);
            $string[addslashes($key)] = daddslashes($val, $force);
        }
    } else {
        $string = addslashes($string);
    }
    return $string;
}

function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
{
    $key = $key != '' ? $key : getglobal('authkey');
    
    $authcode = new AuthCode($key);
    if ('DECODE' == $operation) {
        return $authcode->decode($string);
    }
    
    return $authcode->encode($string, $expiry);
}

function fsocketopen($hostname, $port = 80, &$errno, &$errstr, $timeout = 15)
{
    $fp = '';
    if (function_exists('fsockopen')) {
        $fp = fsockopen($hostname, $port, $errno, $errstr, $timeout);
    } elseif (function_exists('pfsockopen')) {
        $fp = pfsockopen($hostname, $port, $errno, $errstr, $timeout);
    } elseif (function_exists('stream_socket_client')) {
        $fp = stream_socket_client($hostname . ':' . $port, $errno, $errstr, $timeout);
    }
    return $fp;
}

function dfsockopen($url, $limit = 0, $post = '', $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 15, $block = TRUE, $encodetype = 'URLENCODE', $allowcurl = TRUE, $position = 0, $files = array())
{
    require_once libfile('function/filesock');
    return _dfsockopen($url, $limit, $post, $cookie, $bysocket, $ip, $timeout, $block, $encodetype, $allowcurl, $position, $files);
}

function dhtmlspecialchars($string, $flags = null)
{
    if (is_array($string)) {
        foreach ($string as $key => $val) {
            $string[$key] = dhtmlspecialchars($val, $flags);
        }
    } else {
        if ($flags === null) {
            $string = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string);
            if (strpos($string, '&amp;#') !== false) {
                $string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $string);
            }
        } else {
            if (PHP_VERSION < '5.4.0') {
                $string = htmlspecialchars($string, $flags);
            } else {
                if (strtolower(CHARSET) == 'utf-8') {
                    $charset = 'UTF-8';
                } else {
                    $charset = 'ISO-8859-1';
                }
                $string = htmlspecialchars($string, $flags, $charset);
            }
        }
    }
    return $string;
}

function dexit($message = '')
{
    echo $message;
    exit();
}

function fileext($filename)
{
    return addslashes(strtolower(substr(strrchr($filename, '.'), 1, 10)));
}

function dstrpos($string, $arr, $returnvalue = false)
{
    if (empty($string))
        return false;
    foreach ((array) $arr as $v) {
        if (strpos($string, $v) !== false) {
            $return = $returnvalue ? $v : true;
            return $return;
        }
    }
    return false;
}

function isemail($email)
{
    return strlen($email) > 6 && strlen($email) <= 32 && preg_match("/^([A-Za-z0-9\-_.+]+)@([A-Za-z0-9\-]+[.][A-Za-z0-9\-.]+)$/", $email);
}

function quescrypt($questionid, $answer)
{
    return $questionid > 0 && $answer != '' ? substr(md5($answer . md5($questionid)), 16, 8) : '';
}

function random($length, $numeric = 0)
{
    $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
    $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
    if ($numeric) {
        $hash = '';
    } else {
        $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
        $length--;
    }
    $max = strlen($seed) - 1;
    for ($i = 0; $i < $length; $i++) {
        $hash .= $seed{mt_rand(0, $max)};
    }
    return $hash;
}

function strexists($string, $find)
{
    return !(strpos($string, $find) === FALSE);
}

function lang($file, $langvar = null, $vars = array(), $default = null)
{
    global $_G;
    $fileinput = $file;
    list($path, $file) = explode('/', $file);
    if (!$file) {
        $file = $path;
        $path = '';
    }
    if (strpos($file, ':') !== false) {
        $path = 'plugin';
        list($file) = explode(':', $file);
    }

    if ($path != 'plugin') {
        $key = $path == '' ? $file : $path . '_' . $file;
        if (!isset($_G['lang'][$key])) {
            include DISCUZ_ROOT . './source/language/' . ($path == '' ? '' : $path . '/') . 'lang_' . $file . '.php';
            $_G['lang'][$key] = $lang;
        }
        if (defined('IN_MOBILE') && !defined('TPL_DEFAULT')) {
            include DISCUZ_ROOT . './source/language/mobile/lang_template.php';
            $_G['lang'][$key] = array_merge($_G['lang'][$key], $lang);
        }
        if ($file != 'error' && !isset($_G['cache']['pluginlanguage_system'])) {
            loadcache('pluginlanguage_system');
        }
        if (!isset($_G['hooklang'][$fileinput])) {
            if (isset($_G['cache']['pluginlanguage_system'][$fileinput]) && is_array($_G['cache']['pluginlanguage_system'][$fileinput])) {
                $_G['lang'][$key] = array_merge($_G['lang'][$key], $_G['cache']['pluginlanguage_system'][$fileinput]);
            }
            $_G['hooklang'][$fileinput] = true;
        }
        $returnvalue = &$_G['lang'];
    } else {
        if (empty($_G['config']['plugindeveloper'])) {
            loadcache('pluginlanguage_script');
        } elseif (!isset($_G['cache']['pluginlanguage_script'][$file]) && preg_match("/^[a-z]+[a-z0-9_]*$/i", $file)) {
            if (@include(DISCUZ_ROOT . './data/plugindata/' . $file . '.lang.php')) {
                $_G['cache']['pluginlanguage_script'][$file] = $scriptlang[$file];
            } else {
                loadcache('pluginlanguage_script');
            }
        }
        $returnvalue = & $_G['cache']['pluginlanguage_script'];
        $key = &$file;
    }
    $return = $langvar !== null ? (isset($returnvalue[$key][$langvar]) ? $returnvalue[$key][$langvar] : null) : $returnvalue[$key];
    $return = $return === null ? ($default !== null ? $default : $langvar) : $return;
    $searchs = $replaces = array();
    if ($vars && is_array($vars)) {
        foreach ($vars as $k => $v) {
            $searchs[] = '{' . $k . '}';
            $replaces[] = $v;
        }
    }
    if (is_string($return) && strpos($return, '{_G/') !== false) {
        preg_match_all('/\{_G\/(.+?)\}/', $return, $gvar);
        foreach ($gvar[0] as $k => $v) {
            $searchs[] = $v;
            $replaces[] = getglobal($gvar[1][$k]);
        }
    }
    $return = str_replace($searchs, $replaces, $return);
    return $return;
}

function dsign($str, $length = 16)
{
    return substr(md5($str . getglobal('config/security/authkey')), 0, ($length ? max(8, $length) : 16));
}

function loaducenter()
{
    require_once DISCUZ_ROOT . './config/config_ucenter.php';
    require_once DISCUZ_ROOT . './uc_client/client.php';
}

function loadcache($cachenames, $force = false)
{
    global $_G;
    static $loadedcache = array();
    $cachenames = is_array($cachenames) ? $cachenames : array($cachenames);
    $caches = array();
    foreach ($cachenames as $k) {
        if (!isset($loadedcache[$k]) || $force) {
            $caches[] = $k;
            $loadedcache[$k] = true;
        }
    }

    /*if (!empty($caches)) {
        $cachedata = C::t('common_syscache')->fetch_all($caches);
        foreach ($cachedata as $cname => $data) {
            if ($cname == 'setting') {
                $_G['setting'] = $data;
            } elseif ($cname == 'usergroup_' . $_G['groupid']) {
                $_G['cache'][$cname] = $_G['group'] = $data;
            } elseif ($cname == 'style_default') {
                $_G['cache'][$cname] = $_G['style'] = $data;
            } elseif ($cname == 'grouplevels') {
                $_G['grouplevels'] = $data;
            } else {
                $_G['cache'][$cname] = $data;
            }
        }
    }*/
    return true;
}

function dgmdate($timestamp, $format = 'dt', $timeoffset = '9999', $uformat = '')
{
    global $_G;
    $format == 'u' && !$_G['setting']['dateconvert'] && $format = 'dt';
    static $dformat, $tformat, $dtformat, $offset, $lang;
    if ($dformat === null) {
        $dformat = getglobal('setting/dateformat');
        $tformat = getglobal('setting/timeformat');
        $dtformat = $dformat . ' ' . $tformat;
        $offset = getglobal('member/timeoffset');
        $sysoffset = getglobal('setting/timeoffset');
        $offset = $offset == 9999 ? ($sysoffset ? $sysoffset : 0) : $offset;
        $lang = lang('core', 'date');
    }
    $timeoffset = $timeoffset == 9999 ? $offset : $timeoffset;
    $timestamp += $timeoffset * 3600;
    $format = empty($format) || $format == 'dt' ? $dtformat : ($format == 'd' ? $dformat : ($format == 't' ? $tformat : $format));
    if ($format == 'u') {
        $todaytimestamp = TIMESTAMP - (TIMESTAMP + $timeoffset * 3600) % 86400 + $timeoffset * 3600;
        $s = gmdate(!$uformat ? $dtformat : $uformat, $timestamp);
        $time = TIMESTAMP + $timeoffset * 3600 - $timestamp;
        if ($timestamp >= $todaytimestamp) {
            if ($time > 3600) {
                $return = intval($time / 3600) . '&nbsp;' . $lang['hour'] . $lang['before'];
            } elseif ($time > 1800) {
                $return = $lang['half'] . $lang['hour'] . $lang['before'];
            } elseif ($time > 60) {
                $return = intval($time / 60) . '&nbsp;' . $lang['min'] . $lang['before'];
            } elseif ($time > 0) {
                $return = $time . '&nbsp;' . $lang['sec'] . $lang['before'];
            } elseif ($time == 0) {
                $return = $lang['now'];
            } else {
                $return = $s;
            }
            if ($time >= 0 && !defined('IN_MOBILE')) {
                $return = '<span title="' . $s . '">' . $return . '</span>';
            }
        } elseif (($days = intval(($todaytimestamp - $timestamp) / 86400)) >= 0 && $days < 7) {
            if ($days == 0) {
                $return = $lang['yday'] . '&nbsp;' . gmdate($tformat, $timestamp);
            } elseif ($days == 1) {
                $return = $lang['byday'] . '&nbsp;' . gmdate($tformat, $timestamp);
            } else {
                $return = ($days + 1) . '&nbsp;' . $lang['day'] . $lang['before'];
            }
            if (!defined('IN_MOBILE')) {
                $return = '<span title="' . $s . '">' . $return . '</span>';
            }
        } else {
            $return = $s;
        }
        return $return;
    } else {
        return gmdate($format, $timestamp);
    }
}

function dmktime($date)
{
    if (strpos($date, '-')) {
        $time = explode('-', $date);
        return mktime(0, 0, 0, $time[1], $time[2], $time[0]);
    }
    return 0;
}

function dnumber($number)
{
    return abs($number) > 10000 ? '<span title="' . $number . '">' . intval($number / 10000) . lang('core', '10k') . '</span>' : $number;
}

function dimplode($array)
{
    if (!empty($array)) {
        $array = array_map('addslashes', $array);
        return "'" . implode("','", is_array($array) ? $array : array($array)) . "'";
    } else {
        return 0;
    }
}

function libfile($libname, $folder = '')
{
    $libpath = '/source/' . $folder;
    if (strstr($libname, '/')) {
        list($pre, $name) = explode('/', $libname);
        $path = "{$libpath}/{$pre}/{$pre}_{$name}";
    } else {
        $path = "{$libpath}/{$libname}";
    }
    return preg_match('/^[\w\d\/_]+$/i', $path) ? realpath(DISCUZ_ROOT . $path . '.php') : false;
}

function dstrlen($str)
{
    if (strtolower(CHARSET) != 'utf-8') {
        return strlen($str);
    }
    $count = 0;
    for ($i = 0; $i < strlen($str); $i++) {
        $value = ord($str[$i]);
        if ($value > 127) {
            $count++;
            if ($value >= 192 && $value <= 223)
                $i++;
            elseif ($value >= 224 && $value <= 239)
                $i = $i + 2;
            elseif ($value >= 240 && $value <= 247)
                $i = $i + 3;
        }
        $count++;
    }
    return $count;
}

function cutstr($string, $length, $dot = ' ...')
{
    return TextUtil::cutstr($string, $length, $dot);
}

function dstripslashes($string)
{
    if (empty($string))
        return $string;
    if (is_array($string)) {
        foreach ($string as $key => $val) {
            $string[$key] = dstripslashes($val);
        }
    } else {
        $string = stripslashes($string);
    }
    return $string;
}

function aidencode($aid, $type = 0, $tid = 0)
{
    global $_G;
    $s = !$type ? $aid . '|' . substr(md5($aid . md5($_G['config']['security']['authkey']) . TIMESTAMP . $_G['uid']), 0, 8) . '|' . TIMESTAMP . '|' . $_G['uid'] . '|' . $tid : $aid . '|' . md5($aid . md5($_G['config']['security']['authkey']) . TIMESTAMP) . '|' . TIMESTAMP;
    return rawurlencode(base64_encode($s));
}

function runhooks($scriptextra = '')
{
    if (!defined('HOOKTYPE')) {
        define('HOOKTYPE', !defined('IN_MOBILE') ? 'hookscript' : 'hookscriptmobile');
    }
    if (defined('CURMODULE')) {
        global $_G;
        if ($_G['setting']['plugins']['func'][HOOKTYPE]['common']) {
            hookscript('common', 'global', 'funcs', array(), 'common');
        }
        hookscript(CURMODULE, $_G['basescript'], 'funcs', array(), '', $scriptextra);
    }
}

function stripsearchkey($string)
{
    $string = trim($string);
    $string = str_replace('*', '%', addcslashes($string, '%_'));
    return $string;
}

function dmkdir($dir, $mode = 0777, $makeindex = TRUE)
{
    if (!is_dir($dir)) {
        dmkdir(dirname($dir), $mode, $makeindex);
        @mkdir($dir, $mode);
        if (!empty($makeindex)) {
            @touch($dir . '/index.html');
            @chmod($dir . '/index.html', 0777);
        }
    }
    return true;
}

function diconv($str, $in_charset, $out_charset = CHARSET, $ForceTable = FALSE)
{
    global $_G;

    $in_charset = strtoupper($in_charset);
    $out_charset = strtoupper($out_charset);

    if (empty($str) || $in_charset == $out_charset) {
        return $str;
    }

    $out = '';

    if (!$ForceTable) {
        if (function_exists('iconv')) {
            $out = iconv($in_charset, $out_charset . '//IGNORE', $str);
        } elseif (function_exists('mb_convert_encoding')) {
            $out = mb_convert_encoding($str, $out_charset, $in_charset);
        }
    }

    if ($out == '') {
        $chinese = new Chinese($in_charset, $out_charset, true);
        $out = $chinese->Convert($str);
    }

    return $out;
}

function renum($array)
{
    $newnums = $nums = array();
    foreach ($array as $id => $num) {
        $newnums[$num][] = $id;
        $nums[$num] = $num;
    }
    return array($nums, $newnums);
}

function sizecount($size)
{
    if ($size >= 1073741824) {
        $size = round($size / 1073741824 * 100) / 100 . ' GB';
    } elseif ($size >= 1048576) {
        $size = round($size / 1048576 * 100) / 100 . ' MB';
    } elseif ($size >= 1024) {
        $size = round($size / 1024 * 100) / 100 . ' KB';
    } else {
        $size = intval($size) . ' Bytes';
    }
    return $size;
}

function swapclass($class1, $class2 = '')
{
    static $swapc = null;
    $swapc = isset($swapc) && $swapc != $class1 ? $class1 : $class2;
    return $swapc;
}

function writelog($file, $log)
{
    Logger::write($file, $log);
}

function getstatus($status, $position)
{
    $t = $status & pow(2, $position - 1) ? 1 : 0;
    return $t;
}

function setstatus($position, $value, $baseon = null)
{
    $t = pow(2, $position - 1);
    if ($value) {
        $t = $baseon | $t;
    } elseif ($baseon !== null) {
        $t = $baseon & ~$t;
    } else {
        $t = ~$t;
    }
    return $t & 0xFFFF;
}

function memory($cmd, $key = '', $value = '', $ttl = 0, $prefix = '')
{
    if ($cmd == 'check') {
        return C::memory()->enable ? C::memory()->type : '';
    } elseif (C::memory()->enable && in_array($cmd, array('set', 'get', 'rm', 'inc', 'dec'))) {
        if (defined('DISCUZ_DEBUG') && DISCUZ_DEBUG) {
            if (is_array($key)) {
                foreach ($key as $k) {
                    C::memory()->debug[$cmd][] = ($cmd == 'get' || $cmd == 'rm' ? $value : '') . $prefix . $k;
                }
            } else {
                C::memory()->debug[$cmd][] = ($cmd == 'get' || $cmd == 'rm' ? $value : '') . $prefix . $key;
            }
        }
        switch ($cmd) {
            case 'set': return C::memory()->set($key, $value, $ttl, $prefix);
                break;
            case 'get': return C::memory()->get($key, $value);
                break;
            case 'rm': return C::memory()->rm($key, $value);
                break;
            case 'inc': return C::memory()->inc($key, $value ? $value : 1);
                break;
            case 'dec': return C::memory()->dec($key, $value ? $value : -1);
                break;
        }
    }
    return null;
}

function return_bytes($val)
{
    $val = trim($val);
    $last = strtolower($val{strlen($val) - 1});
    switch ($last) {
        case 'g': $val *= 1024;
        case 'm': $val *= 1024;
        case 'k': $val *= 1024;
    }
    return $val;
}

function dintval($int, $allowarray = false)
{
    $ret = intval($int);
    if ($int == $ret || !$allowarray && is_array($int))
        return $ret;
    if ($allowarray && is_array($int)) {
        foreach ($int as &$v) {
            $v = dintval($v, true);
        }
        return $int;
    } elseif ($int <= 0xffffffff) {
        $l = strlen($int);
        $m = substr($int, 0, 1) == '-' ? 1 : 0;
        if (($l - $m) === strspn($int, '0987654321', $m)) {
            return $int;
        }
    }
    return $ret;
}

function strhash($string, $operation = 'DECODE', $key = '')
{
    $key = md5($key != '' ? $key : getglobal('authkey'));
    if ($operation == 'DECODE') {
        $hashcode = gzuncompress(base64_decode(($string)));
        $string = substr($hashcode, 0, -16);
        $hash = substr($hashcode, -16);
        unset($hashcode);
    }

    $vkey = substr(md5($string . substr($key, 0, 16)), 4, 8) . substr(md5($string . substr($key, 16, 16)), 18, 8);

    if ($operation == 'DECODE') {
        return $hash == $vkey ? $string : '';
    }

    return base64_encode(gzcompress($string . $vkey));
}

function dunserialize($data)
{
    if (($ret = unserialize($data)) === false) {
        $ret = unserialize(stripslashes($data));
    }
    return $ret;
}

function currentlang()
{
    $charset = strtoupper(CHARSET);
    if ($charset == 'GBK') {
        return 'SC_GBK';
    } elseif ($charset == 'BIG5') {
        return 'TC_BIG5';
    } elseif ($charset == 'UTF-8') {
        global $_G;
        if ($_G['config']['output']['language'] == 'zh_cn') {
            return 'SC_UTF8';
        } elseif ($_G['config']['output']['language'] == 'zh_tw') {
            return 'TC_UTF8';
        }
    } else {
        return '';
    }
}
