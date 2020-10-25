<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/11/22
 * Time: 2:21 PM
 */

/**
 * 接口错误返回
 * @param string $error_info 错误信息
 * return array
 */
function api_error($error_info = '')
{
    if (!$error_info) {
        $error_info = '无效的请求';
    }
    throw new \App\Exceptions\ApiException($error_info);
}

/**
 * 获取格式化的时间（默认当前时间）
 * @param int $time 时间戳
 * return json
 */
function get_date($time = 0)
{
    if (!$time) {
        $time = time();
    }

    return date('Y-m-d H:i:s', $time);
}

/**
 * 获取分页信息
 * @return mixed
 */
function get_page_params()
{
    $page = (int)request()->page;
    $limit = (int)request()->limit;
    if (!$page) $page = 1;
    if (!$limit) $limit = 20;
    if ($limit > 100) {
        $limit = 100;
    }
    $offset = $limit * ($page - 1);

    return [$page, $limit, $offset];
}

/**
 * 获取app后台配置信息
 * @return mixed
 */
function get_app_config($key)
{
    $config = \Illuminate\Support\Facades\Redis::get('app_config:' . config('app.key'));
    $config = json_decode($config, true);
    if (isset($config[$key])) {
        return $config[$key];
    }
    return false;
}

/**
 * 格式化用逗号分隔的数字检测是否数字
 * @param $number 要分隔的字符串
 * @param $must_array 只有一个值的时候是否格式化为数组
 * @return mixed
 */
function format_number($number, $must_array = false)
{
    if (!is_array($number)) {
        $number = str_replace('，', ',', $number);
        if (strpos($number, ',') || $must_array) {
            $number = explode(',', $number);
        }
    }
    $numbers = array();
    if (is_array($number)) {
        foreach ($number as $val) {
            $_number = (int)$val;
            if ($_number) {
                $numbers[] = $_number;
            }
        }
    } else {
        $numbers = (int)$number;
        if ($must_array) $numbers[] = (int)$numbers;
    }
    return $numbers;
}

/**
 * 格式化价格
 * @param int $pirce 需要格式化的价格
 * @param int $num 保留位数
 * @param int $is_rounded 是否四舍五入
 * @return mixed
 */
function format_price($pirce, $num = 2, $is_rounded = 1)
{
    $return_price = '0';
    if (is_numeric($pirce)) {
        if (!$is_rounded) {
            $is_abs = 0;//如果是负数需要先转正数计算完了在转为负数
            if ($pirce < 0) {
                $is_abs = 1;
                $pirce = abs($pirce);
            }
            $divisor = pow(10, $num);
            $return_price = floor(strval($pirce * $divisor)) / $divisor;
            if ($is_abs == 1) {
                $return_price = -$return_price;
            }
        } else {
            $bd_price = round($pirce, $num);
            $return_price = sprintf("%." . $num . "f", $bd_price);
        }
        $return_price = floatval($return_price);
        if (empty($return_price)) {
            $return_price = '0';
        }
    }
    return $return_price;
}

/**
 * 多行文本换行转换到数组
 * @param $string string 需要转换的文本
 * @return mixed
 */
function textarea_br_to_array($string)
{
    if (!$string) return false;
    $data = explode(chr(10), $string);
    $return = array();
    foreach ($data as $val) {
        $_item = str_replace(chr(13), '', $val);
        if ($_item && !in_array($_item, $return)) {
            $return[] = $_item;
        }
    }
    return $return;
}

/**
 * 数组转换到多行文本换行
 * @param $data array 需要转换的文本
 * @param $glue string 分隔符
 * @return mixed
 */
function array_to_br_textarea($data, $glue = ',')
{
    if (!$data) return false;
    return str_replace($glue, chr(10), $data);
}

/**
 * 将MD5值压缩成8位32进制生成8位长度的唯一英文数字组合字符串
 * @param $a
 * @return string
 */
function strto32($a)
{
    for ($a = md5($a, true),
         $s = '0123456789ABCDEFGHIJKLMNOPQRSTUV',
         $d = '',
         $f = 0;
         $f < 8;
         $g = ord($a[$f]),
         $d .= $s[($g ^ ord($a[$f + 8])) - $g & 0x1F],
         $f++
    ) ;
    return $d;
}

/**
 * @param $url 请求网址
 * @param bool $params 请求参数
 * @param int $ispost 请求方式
 * @param int $https https协议
 * @return bool|mixed
 */
function curl($url, $params = false, $ispost = 0, $https = 0)
{
    $httpInfo = [];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if ($https) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
    }
    if ($ispost) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_URL, $url);
    } else {
        if ($params) {
            if (is_array($params)) {
                $params = http_build_query($params);
            }
            curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
        } else {
            curl_setopt($ch, CURLOPT_URL, $url);
        }
    }

    $response = curl_exec($ch);
    if ($response === false) {
        //echo "cURL Error: " . curl_error($ch);
        return false;
    }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
    curl_close($ch);

    return $response;
}

/**
 * sql debug输出
 */
function get_sql_debug()
{
    \DB::listen(function ($sql) {
        dump($sql);
        $singleSql = $sql->sql;
        if ($sql->bindings) {
            foreach ($sql->bindings as $replace) {
                $value = is_numeric($replace) ? $replace : "'" . $replace . "'";
                $singleSql = preg_replace('/\?/', $value, $singleSql, 1);
            }
            dump($singleSql);
        } else {
            dump($singleSql);
        }
    });


}