<?php
/**
 * 数据签名认证
 * @param  array $data 被认证的数据
 * @return string       签名
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function data_auth_sign($data)
{
    //数据类型检测
    if (!is_array($data)) {
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

/**
 * 是否是手机号码，含虚拟运营商的170号段
 * @author wei sun
 * @param string $phone 手机号码
 * @return boolean
 */
function is_phone($phone)
{
    if (is_numeric($phone) && strlen($phone) == 11 && substr($phone, 0, 1) == 1) {
        return true;
    }
    return false;
}


/**
 * 验证是否为中文姓名
 * @param $name
 */
function isChineseName($name)
{
    if (preg_match('/^([\xe4-\xe9][\x80-\xbf]{2}){2,4}$/', $name)) {
        return true;
    }
    return false;
}

/**
 * 获取图片路径
 * @param int $id
 * @return string
 */
function get_file_path($id = 0)
{
    if (!$id) {
        return '';
    }

    static $list;
    /* 获取缓存数据 */
    if (empty($list)) {
        $list = cache('sys_uploads_list');
    }
    /* 查找用户信息 */
    $key   = "u{$id}";
    $image = '';
    if (isset($list[$key])) {
        //已缓存，直接使用
        $image = $list[$key];
    } else {
        //调用接口获取用户信息
        $x = db('uploads')->field('type,path')->find($id);
        if ($x) {
            $image      = '/uploads/' . $x['type'] . '/' . $x['path'];
            $list[$key] = $image;
            $count      = count($list);
            while ($count-- > 3000) {
                array_shift($list);
            }
            cache('sys_uploads_list', $list);
        }
    }
    return $image;
}

/**
 * 生成订单号
 * @param string $letter
 * @return string
 */
function build_order_no($letter = '')
{
    return $letter . date('Ymd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));     //生成16位数字基本号
}

/**
 * 计算两点地理坐标之间的距离
 * @param  Decimal $longitude1 起点经度
 * @param  Decimal $latitude1 起点纬度
 * @param  Decimal $longitude2 终点经度
 * @param  Decimal $latitude2 终点纬度
 * @param  Int $unit 单位 1:米 2:公里
 * @param  Int $decimal 精度 保留小数位数
 * @return mixed
 */
function getDistance($longitude1, $latitude1, $longitude2, $latitude2, $unit = 2, $decimal = 0)
{

    $EARTH_RADIUS = 6370.996; // 地球半径系数
    $PI           = 3.1415926;

    $radLat1 = $latitude1 * $PI / 180.0;
    $radLat2 = $latitude2 * $PI / 180.0;

    $radLng1 = $longitude1 * $PI / 180.0;
    $radLng2 = $longitude2 * $PI / 180.0;

    $a = $radLat1 - $radLat2;
    $b = $radLng1 - $radLng2;

    $distance = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
    $distance = $distance * $EARTH_RADIUS * 1000;

    if ($unit == 2) {
        $distance = $distance / 1000;
    }

    return round($distance, $decimal);
}

/**
 * 验证坐标
 * @param $pos
 * @param bool $city
 * @return array|string
 */
function verfiyPos($pos, $city = true)
{
    if (!$pos) {
        return '坐标不能为空';
    }
    $num = $city ? 3 : 2;
    $pos = explode(',', $pos);

    if (count($pos) != $num) {
        return '坐标格式错误';
    }

    if ($pos[0] >= 180 || $pos[0] <= 0) {
        return '经度格式错误';
    }
    if ($pos[1] >= 90 || $pos[1] <= 0) {
        return '纬度格式错误';
    }

    if ($city) {
        $pos[2] = intval($pos[2]);
    }
    return $pos;
}

/**
 * 是否微信
 * @return bool
 */
function is_wechat()
{
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
        return true;
    }
    return false;
}


/**
 * 时间转化
 * @param $range_time
 * @param bool $time
 * @return array
 */
function range_time($range_time, $time = true)
{
    $range_time    = explode('~', $range_time);
    $range_time[0] = trim($range_time[0]);

    $range_time[1] = trim($range_time[1]);
    if (strlen($range_time[1]) <= 10) {
        $range_time[1] .= ' 23:59:59';
    }
    if ($time) {
        $range_time[0] = strtotime($range_time[0]);
        $range_time[1] = strtotime($range_time[1]);
    }
    return $range_time;
}

/**
 * @param $arr
 * @return array
 */
function arr2java($arr)
{
    if (!$arr) return [];
    $res = [];
    foreach ($arr as $key => $val) {
        array_push($res, [
            'key' => $key,
            'val' => $val,
        ]);
    }
    return $res;
}
