<?php

use Madphp\Log;
use Madphp\Request;

/**
 * 函数库
 * @author 徐亚坤 hdyakun@sina.com
 */

/**
 * 根据级别写日志
 */
if (!function_exists('writeLog')) {
    function writeLog($level = 'error', $message, $force = FALSE)
    {
        return Log::write($level, $message, $force);
    }
}

/**
 * 判断是否CLI模式请求
 * @author 徐亚坤
 */
if (!function_exists('is_cli')) {
    function is_cli()
    {
        return Request::isCli();
    }
}

function is_ajax()
{
    $tmp = 'HTTP_X_REQUESTED_WITH';
    return !empty($_SERVER[$tmp]) && strtolower($_SERVER[$tmp]) == 'xmlhttprequest';
}

/**
 * 判断是否链接
 * @author 徐亚坤
 */
if (!function_exists("is_http")) {
    function is_http($url)
    {
        $preg = "/(http:|https:)/";
        if (preg_match($preg, $url)) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('is_php_version')) {
    function is_php_version($version = '5.0.0')
    {
        static $_is_php;
        $version = (string)$version;
        if (!isset($_is_php[$version])) {
            $_is_php[$version] = (version_compare(PHP_VERSION, $version) < 0) ? FALSE : TRUE;
        }

        return $_is_php[$version];
    }
}

/**
 * 判断是否存在
 * @author 徐亚坤
 */
if (!function_exists('is_useful')) {
    function is_useful($url)
    {
        if (empty($url)) {
            return false;
        }
        if (@fopen($url, 'r')) {
            return true;
        }
        return false;
    }
}

/**
 * 判断字符串纯汉字 OR 纯英文 OR 汉英混合
 * @author 徐亚坤
 * @return 1: 英文，2：纯汉字，3：汉字和英文
 */
if (!function_exists('utf8_str')) {
    function utf8_str($str)
    {
        $mb = mb_strlen($str, 'utf-8');
        $st = strlen($str);
        if ($st == $mb) {
            return 1;
        }
        if ($st % $mb == 0 && $st % 3 == 0) {
            return 2;
        }
        return 3;
    }
}

/**
 * 获取字符串长度，支持中文和其他编码
 * @author 徐亚坤
 * @param string $str 需要计算的字符串
 * @param string $charset 字符编码
 * @return length int
 */
if (!function_exists('abslength')) {
    function abslength($str, $charset = 'utf-8')
    {
        if (empty($str)) {
            return 0;
        }
        if (function_exists('mb_strlen')) {
            return mb_strlen($str, 'utf-8');
        } else {
            @preg_match_all("/./u", $str, $ar);
            return count($ar[0]);
        }
    }
}

/**
 * 字符串截取，支持中文和其他编码
 * @author 徐亚坤
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $strength 字符串的长度
 * @param string $charset 编码格式
 * @param string $suffix 显示截断字符
 * @return string
 */
if (!function_exists('msubstr')) {
    function msubstr($str, $start = 0, $length, $strength, $charset = "utf-8", $suffix = true)
    {
        if (function_exists("mb_substr")) {
            if ($suffix) {
                if ($length < $strength) {
                    return mb_substr($str, $start, $length, $charset) . "…";
                } else {
                    return mb_substr($str, $start, $length, $charset);
                }
            } else {
                return mb_substr($str, $start, $length, $charset);
            }
        } elseif (function_exists('iconv_substr')) {
            // 是否加上点号
            if ($suffix) {
                if ($length < $strength) {
                    return iconv_substr($str, $start, $length, $charset) . "…";
                } else {
                    return iconv_substr($str, $start, $length, $charset);
                }
            } else {
                return iconv_substr($str, $start, $length, $charset);
            }
        } else {
            $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            $slice = join("", array_slice($match[0], $start, $length));
            if ($suffix) {
                return $slice . "…";
            } else {
                return $slice;
            }
        }
    }
}

/**
 * 使用多个字符串分割另一个字符串
 *
 * @param $delimiters array 分隔字符
 * @param $string string 输入的字符串
 * @return array
 */
if (!function_exists('multi_explode')) {
    function multi_explode($delimiters, $string)
    {
        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);
        return $launch;
    }
}

/**
 * 获取根节点的层级关系树
 * @param array $items
 * @return array
 */
if (!function_exists('rootTree')) {
    function rootTree(Array $items)
    {
        foreach ($items as $item) {
            $items[$item['pid']]['sub'][$item['id']] = &$items[$item['id']];
        }
        return isset($items[0]['sub']) ? $items[0]['sub'] : array();
    }
}

/**
 * 获取请求方式
 * @param  string
 * @return string
 */
if (!function_exists('get_request_method')) {
    function get_request_method($default = 'get')
    {
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD']) {
            return strtolower($_SERVER['REQUEST_METHOD']);
        }
        return strtolower($default);
    }
}

/**
 * 输出调试数据
 * @param  mixed 数据
 * @param  bool 是否相关信息
 * @param  bool 是否退出
 * @return void
 */
if (!function_exists("pp")) {
    function pp($data, $exit = true, $dump = false)
    {
        echo "<pre>";
        $dump || print_r($data);
        $dump && var_dump($data);
        echo "</pre>";
        $exit && exit();
    }
}

if (!function_exists('console')) {
    function console($var = null)
    {
        var_dump($var);
    }
}

function curl_post($url, $data)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    // 文件上传参考 CURLFile
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

/**
 * 获取当前数字在第几部分,返回值从0开始
 * @param $num 当前数字
 * @param $total 总数
 * @param $part 分为几部分
 * @return mixed
 */
function get_part_index($num, $total, $part)
{
    return max(0, min(ceil($num / floor($total / $part)) - 1, $part - 1));
}

/**
 * 获取文件行数
 * @param $file
 * @return int
 */
function get_line_num($file)
{
    $fp = @fopen($file, 'r') or die("open file failure!");
    $total_line = 0;
    if ($fp) {
        while (stream_get_line($fp, 8192, PHP_EOL)) {
            $total_line++;
        }
        fclose($fp);
    }
    return $total_line;
}

/**
 * 获取图片信息
 *
 * @param  图片地址
 * @return bool || array
 */
if (!function_exists('image_info')) {
    function image_info($file)
    {
        if (!file_exists($file) && !@fopen($file, 'r')) {
            return false;
        }
        $imageinfo = getimagesize($file);
        if ($imageinfo === false) {
            return false;
        }
        $imagetype = strtolower(substr(image_type_to_extension($imageinfo[2]), 1));
        $imagesize = filesize($file);
        return array(
            'file' => $file,
            'width' => $imageinfo[0],
            'height' => $imageinfo[1],
            'type' => $imagetype,
            'size' => $imagesize,
            'mime' => $imageinfo['mime']
        );
    }
}

/**
 * 获取随机数字，可设定是否重复
 * @param int $min
 * @param int $max
 * @param int $num
 * @param boolean $re
 * @return array
 */
if (!function_exists('random_nums')) {
    function random_nums($min, $max, $num, $re = false)
    {
        $arr = array();
        $t = $i = 0;
        // 如果数字不可重复，防止无限死循环
        if (!$re) {
            $num = min($num, $max - $min + 1);
        }
        do {
            // 取随机数
            $t = mt_rand($min, $max);
            if (!$re && in_array($t, $arr)) {
                // 数字重复
                continue;
            }
            $arr[] = $t;
            ++$i;
        } while ($i < $num);
        return $arr;
    }
}

/**
 * 生成随机字符串
 * @param  $lenth int 长度
 */
if (!function_exists('create_randomstr')) {
    function create_randomstr($lenth = 6)
    {
        return random($lenth, '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ');
    }
}

/**
 * 获取请求ip
 * @author 徐亚坤
 * @return ip 地址
 */
if (!function_exists('ip')) {
    function ip()
    {
        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return preg_match('/[\d\.]{7,15}/', $ip, $matches) ? $matches[0] : '';
    }
}

/**
 * 生成随机字符串
 * @param  int
 * @return string
 */
if (!function_exists('random')) {
    function random($length, $chars = '0123456789')
    {
        $numeric = preg_match('/^[0-9]+$/', $chars) ? 1 : 0;
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
}

/**
 * 转换字节数为其他单位
 *
 * @param  $filesize string
 * @return string
 */
if (!function_exists('sizecount')) {
    function sizecount($filesize)
    {
        $s = array('Bytes', 'KB', 'MB', 'GB', 'TB', 'PB');
        $e = floor(log($filesize, 1024));
        return sprintf('%.2f ' . $s[$e], $filesize / pow(1024, $e));
    }
}

/**
 * 返回经addslashes处理过的字符串或数组
 * @author 徐亚坤
 * @param  $data 需要处理的字符串或数组
 * @return mixed
 */
if (!function_exists('new_addslashes')) {
    function new_addslashes($data)
    {
        if (!is_array($data)) {
            return addslashes($data);
        }
        foreach ($data as $key => $val) {
            $data[$key] = new_addslashes($val);
        }

        return $data;
    }
}

/**
 * 返回经stripslashes处理过的字符串或数组
 * @author 徐亚坤
 * @param  $data 需要处理的字符串或数组
 * @return mixed
 */
if (!function_exists('new_stripslashes')) {
    function new_stripslashes($data)
    {
        if (!is_array($data)) {
            return stripslashes($data);
        }
        foreach ($data as $key => $val) {
            $data[$key] = new_stripslashes($val);
        }

        return $data;
    }
}

/**
 * 返回经unserialize处理过的数组
 * @author 徐亚坤
 * @param $string 需要处理的字符串
 * @return mixed
 */
if (!function_exists('new_unserialize')) {
    function new_unserialize($data)
    {
        if (($ret = unserialize($data)) === false) {
            $ret = unserialize(stripslashes($data));
        }
        return $ret;
    }
}

/**
 * 将字符串转换为数组
 *
 * @param $data string
 * @return array
 *
 */
if (!function_exists('string2array')) {
    function string2array($data)
    {
        if (is_array($data)) return $data;
        if ($data == '') return array();
        if (substr($data, 0, 7) == 'array (') {
            @eval("\$array = $data;");
            return $array;
        } else {
            return new_unserialize($data);
        }
    }
}

/**
 * 将数组转换为字符串
 *
 * @param $data array
 * @param $is_addslashes bool
 * @return string
 *
 */
if (!function_exists('array2string')) {
    function array2string($data, $is_addslashes = true)
    {
        if ($data == '') return '';
        // addslashes 数据需要 stripslashes
        if ($is_addslashes) $data = new_stripslashes($data);
        return addslashes(var_export($data, TRUE));
    }
}

if (!function_exists('iconv')) {
    /**
     * 系统不开启 iconv 模块时, 自建 iconv(), 使用 MB String 库处理
     *
     * @param  string
     * @param  string
     * @param  string
     * @return string
     */
    function iconv($from_encoding = 'GBK', $target_encoding = 'UTF-8', $string)
    {
        return convert_encoding($string, $from_encoding, $target_encoding);
    }
}

/**
 * 兼容性转码
 *
 * 系统转换编码调用此函数, 会自动根据当前环境采用 iconv 或 MB String 处理
 *
 * @param  string
 * @param  string
 * @param  string
 * @return string
 */
if (!function_exists('convert_encoding')) {
    function convert_encoding($string, $from_encoding = 'GBK', $target_encoding = 'UTF-8')
    {
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($string, str_replace('//IGNORE', '', strtoupper($target_encoding)), $from_encoding);
        } else {
            if (strtoupper($from_encoding) == 'UTF-16') {
                $from_encoding = 'UTF-16BE';
            }

            if (strtoupper($target_encoding) == 'UTF-16') {
                $target_encoding = 'UTF-16BE';
            }

            if (strtoupper($target_encoding) == 'GB2312' or strtoupper($target_encoding) == 'GBK') {
                $target_encoding .= '//IGNORE';
            }

            return iconv($from_encoding, $target_encoding, $string);
        }
    }
}

/**
 * Singular
 * Takes a plural word and makes it singular
 * @access  public
 * @param   string
 * @return  str
 */
if (!function_exists('singular')) {
    function singular($str)
    {
        $result = strval($str);

        $singular_rules = array(
            '/(matr)ices$/' => '\1ix',
            '/(vert|ind)ices$/' => '\1ex',
            '/^(ox)en/' => '\1',
            '/(alias)es$/' => '\1',
            '/([octop|vir])i$/' => '\1us',
            '/(cris|ax|test)es$/' => '\1is',
            '/(shoe)s$/' => '\1',
            '/(o)es$/' => '\1',
            '/(bus|campus)es$/' => '\1',
            '/([m|l])ice$/' => '\1ouse',
            '/(x|ch|ss|sh)es$/' => '\1',
            '/(m)ovies$/' => '\1\2ovie',
            '/(s)eries$/' => '\1\2eries',
            '/([^aeiouy]|qu)ies$/' => '\1y',
            '/([lr])ves$/' => '\1f',
            '/(tive)s$/' => '\1',
            '/(hive)s$/' => '\1',
            '/([^f])ves$/' => '\1fe',
            '/(^analy)ses$/' => '\1sis',
            '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/' => '\1\2sis',
            '/([ti])a$/' => '\1um',
            '/(p)eople$/' => '\1\2erson',
            '/(m)en$/' => '\1an',
            '/(s)tatuses$/' => '\1\2tatus',
            '/(c)hildren$/' => '\1\2hild',
            '/(n)ews$/' => '\1\2ews',
            '/([^u])s$/' => '\1',
        );

        foreach ($singular_rules as $rule => $replacement) {
            if (preg_match($rule, $result)) {
                $result = preg_replace($rule, $replacement, $result);
                break;
            }
        }

        return $result;
    }
}

/**
 * Remove Invisible Characters
 * This prevents sandwiching null characters
 * between ascii characters, like Java\0script.
 * @param   string
 * @return  string
 */
if (!function_exists('remove_invisible_characters')) {
    function remove_invisible_characters($str, $url_encoded = TRUE)
    {
        $non_displayables = array();

        // every control character except newline (dec 10)
        // carriage return (dec 13), and horizontal tab (dec 09)

        if ($url_encoded) {
            $non_displayables[] = '/%0[0-8bcef]/';  // url encoded 00-08, 11, 12, 14, 15
            $non_displayables[] = '/%1[0-9a-f]/';   // url encoded 16-31
        }

        $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';   // 00-08, 11, 12, 14-31, 127

        do {
            $str = preg_replace($non_displayables, '', $str, -1, $count);
        } while ($count);

        return $str;
    }
}

/**
 * 字符串半角和全角间相互转换
 * @param string $str 待转换的字符串
 * @param int $type TODBC:转换为半角；TOSBC，转换为全角
 * @return string  返回转换后的字符串
 */
if (!function_exists('convertStrType')) {
    function convertStrType($str, $type)
    {
        $dbc = array(
            '０', '１', '２', '３', '４',
            '５', '６', '７', '８', '９',
            'Ａ', 'Ｂ', 'Ｃ', 'Ｄ', 'Ｅ',
            'Ｆ', 'Ｇ', 'Ｈ', 'Ｉ', 'Ｊ',
            'Ｋ', 'Ｌ', 'Ｍ', 'Ｎ', 'Ｏ',
            'Ｐ', 'Ｑ', 'Ｒ', 'Ｓ', 'Ｔ',
            'Ｕ', 'Ｖ', 'Ｗ', 'Ｘ', 'Ｙ',
            'Ｚ', 'ａ', 'ｂ', 'ｃ', 'ｄ',
            'ｅ', 'ｆ', 'ｇ', 'ｈ', 'ｉ',
            'ｊ', 'ｋ', 'ｌ', 'ｍ', 'ｎ',
            'ｏ', 'ｐ', 'ｑ', 'ｒ', 'ｓ',
            'ｔ', 'ｕ', 'ｖ', 'ｗ', 'ｘ',
            'ｙ', 'ｚ', '－', '　', '：',
            '．', '，', '／', '％', '＃',
            '！', '＠', '＆', '（', '）',
            '＜', '＞', '＂', '＇', '？',
            '［', '］', '｛', '｝', '＼',
            '｜', '＋', '＝', '＿', '＾',
            '￥', '￣', '｀',
        );

        $sbc = array( //半角
            '0', '1', '2', '3', '4',
            '5', '6', '7', '8', '9',
            'A', 'B', 'C', 'D', 'E',
            'F', 'G', 'H', 'I', 'J',
            'K', 'L', 'M', 'N', 'O',
            'P', 'Q', 'R', 'S', 'T',
            'U', 'V', 'W', 'X', 'Y',
            'Z', 'a', 'b', 'c', 'd',
            'e', 'f', 'g', 'h', 'i',
            'j', 'k', 'l', 'm', 'n',
            'o', 'p', 'q', 'r', 's',
            't', 'u', 'v', 'w', 'x',
            'y', 'z', '-', ' ', ':',
            '.', ',', '/', '%', ' #',
            '!', '@', '&', '(', ')',
            '<', '>', '"', '\'', '?',
            '[', ']', '{', '}', '\\',
            '|', '+', '=', '_', '^',
            '￥', '~', '`',
        );

        if ($type == 'TODBC') {
            return str_replace($sbc, $dbc, $str);  //半角到全角
        } elseif ($type == 'TOSBC') {
            return str_replace($dbc, $sbc, $str);  //全角到半角
        } else {
            return $str;
        }
    }
}

/**
 * 字符串截取 支持UTF8/GBK
 * @param $string
 * @param $length
 * @param string $dot
 * @param string $charset
 * @return mixed|string
 */
function str_cut($string, $length, $dot = '...', $charset = 'utf-8')
{
    if (!is_string($string)) return '';
    $string = strip_tags($string);
    $string = str_replace(array('&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), array(' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), $string);
    $string = preg_replace('/^(　){2,}/', '', trim($string));

    $strlen = strlen($string);
    $i = $m = $n = 0;
    do {
        if (preg_match("/[0-9a-zA-Z]/", $string[$i])) { // 纯英文
            $m++;
        } else {
            $n++;
        } // 非英文字节,
        $k = $n / 3 + $m / 2;
        $len = $n / 3 + $m; // 最终截取长度；$len = $n/3+$m*2？
        $i++;
    } while ($k < $length);
    if ($strlen <= $m + $n)
        return $string;
    //截取字符串
    $len = intval($len);
    if (function_exists("mb_substr")) {
        $strcut = mb_substr($string, 0, $len, $charset);
    } elseif (function_exists('iconv_substr')) {
        $strcut = iconv_substr($string, 0, $len, $charset);
        if (false === $strcut) {
            $strcut = $dot = '';
        }
    } else {
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $string, $match);
        count($match[0]) <= $length && $dot = '';
        $strcut = join("", array_slice($match[0], 0, $len));
    }
    $strcut = str_replace(array('&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), array('&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), $strcut);
    return $strcut . $dot;
}
