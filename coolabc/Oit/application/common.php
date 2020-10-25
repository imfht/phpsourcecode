<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
use app\common\api\Dict;
use app\common\model\app\AppFsFile;
use think\Log;
use think\Request;

/**
 * 无限级分类
 * @param        $data
 * @param        $parent_id
 * @param        $children_id
 * @param string $name
 * @param string $pid
 * @return array
 */
function unlimited_layer($data, $parent_id, $children_id, $name = 'children', $pid = '') {
    $arr = array();
    foreach ($data as $k => $v) {
        if ($v[$parent_id] == $pid) {
            $children = unlimited_layer($data, $parent_id, $children_id, $name, $v[$children_id]);
            if (count($children) != 0) {
                $v[$name] = $children;
            }
            $arr[] = $v;
        }
    }
    return $arr;
}

/**
 * 将数组，根据A列，合并B的数据。
 * 返回一个新的数组，包含要2列，并且A列唯一
 * 将 [[a:1, b:333],[a:1, b: 444]] 合并成[[a:1, b:333444]]
 * 将 [[a:1, b:'bb'],[a:1, b: 22]] 合并成[[a:1, b['bb',22]]]
 * @param      $arr
 * @param      $compare_col
 * @param      $merge_col
 * @param null $type
 * @return array
 */
function array_merge_column($arr, $compare_col, $merge_col, $type = null) {
    $new_arr = [];
    foreach ($arr as $key => $val) {
        $t_v = $val[$compare_col];
        $t_a = array_column($new_arr, $compare_col);
        $curr = array_search($t_v, $t_a);
        if ($curr === false) {
            if ($type == null) {
                $new_arr[] = $val;
            } else {
                $new_arr[] = [$compare_col => $val[$compare_col], $merge_col => [$val[$merge_col]]];
            }
        } else {
            if ($type == null) {
                $new_arr[$curr][$merge_col] .= $val[$merge_col];
            } else {
                $new_arr[$curr][$merge_col][] = $val[$merge_col];
            }
        }
    }
    return $new_arr;
}

/**
 * 转化序列数组为键值数组, 一般就是 key => val
 * @param $arr
 * @param $k
 * @param $v
 * @return mixed
 */
function array_key_val($arr, $k, $v) {
    $data = [];
    foreach ($arr as $key => $val) {
        $data[$val[$k]] = $val[$v];
    }

    return $data;
}

/**
 * 比较数组1与数组2，当数组1与数组2的比较列内容一致，就将数组2的另一列改名并添加到数组1中间
 * @param $arr1
 * @param $arr2
 * @param $com_1_col
 * @param $com_2_col
 * @param $add_2_col
 * @param $change_col_name
 * @return $arr1
 */
function array_add_column($arr1, $arr2, $com_1_col, $com_2_col, $add_2_col, $change_col_name) {
    foreach ($arr1 as $key => $val) {
        $temp = $val[$com_1_col];
        for ($i = 0, $c = count($arr2); $i < $c; $i++) {
            if ($temp == $arr2[$i][$com_2_col]) {
                $arr1[$key][$change_col_name] = $arr2[$i][$add_2_col];
            }
        }
    }

    return $arr1;
}

/**
 * 保留arr1中某一列的值，被包含在arr2中数组
 * @param $arr1
 * @param $col
 * @param $arr2
 * @return array
 */
function array_column_equal_arr($arr1, $col, $arr2) {
    $new_arr = [];
    foreach ($arr1 as $key => $val) {
        if (in_array($val[$col], $arr2)) {
            $new_arr[] = $arr1[$key];
        }
    }
    return $new_arr;
}

/**
 * 判断是否手机登录
 */
function is_mobile() {
    //$user_agent = $_SERVER['HTTP_USER_AGENT'];
    $user_agent = Request::instance()->header('user-agent');
    $mobile_agents = [
        "android", "windows ce", "htc", "huawei", "acer", "240x320", "acoon", "acs-", "abacho",
        "ahong", "airness", "alcatel", "coolpad",
        "amoi", "anywhereyougo.com", "applewebkit/525", "applewebkit/532", "asus", "audio",
        "au-mic", "avantogo", "becker", "benq", "bilbo", "bird", "blackberry", "blazer", "bleu",
        "cdm-", "compal", "danger", "dbtel", "dopod", "elaine", "eric", "etouch",
        "fly ", "fly_", "fly-", "go.web", "goodaccess", "gradiente", "grundig", "haier",
        "hedy", "hitachi", "hutchison", "inno", "ipad", "ipaq", "ipod",
        "jbrowser", "kddi", "kgt", "kwc", "lenovo", "lg ", "lg2", "lg3", "lg4", "lg5", "lg7",
        "lg8", "lg9", "lg-", "lge-", "lge9", "longcos", "maemo", "mercator", "meridian",
        "micromax", "midp", "mini", "mitsu", "mmm", "mmp", "mobi", "mot-", "moto",
        "nec-", "netfront", "newgen", "nexian", "nf-browser", "nintendo", "nitro", "nokia",
        "nook", "novarra", "obigo", "palm", "panasonic", "pantech", "philips",
        "phone", "pg-", "playstation", "pocket", "pt-", "qc-", "qtek", "rover",
        "sagem", "sama", "samu", "sanyo", "samsung", "sch-", "scooter", "sec-", "sendo",
        "sgh-", "sharp", "siemens", "sie-", "softbank", "sony", "spice", "sprint", "spv",
        "symbian", "tablet", "talkabout", "tcl-", "teleca", "telit", "tianyu", "tim-",
        "toshiba", "tsm", "up.browser", "utec", "utstar", "verykool", "virgin", "vk-",
        "voda", "voxtel", "vx", "wap", "wellco", "wig browser", "wii",
        "wireless", "xda", "xde", "zte",
    ];
    $is_mobile = false;
    foreach ($mobile_agents as $device) {
        if (stristr($user_agent, $device)) {
            Log::write($device, 'notice');
            $is_mobile = true;
            break;
        }
    }
    return $is_mobile;
}

/**
 * 是否微信浏览器
 * @return bool
 */
function is_weixin() {
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
        return true;
    }
    return false;
}

/**
 * 获得数组1不在数组2中的差值
 * @param $array1
 * @param $array2
 * @param $com_field_id
 * @return array
 */
function array_diff_ext($array1, $array2, $com_field_id) {
    $arr1 = array_column($array1, $com_field_id);
    $arr2 = array_column($array2, $com_field_id);
    $arr_diff = array_diff($arr1, $arr2);
    $arr_r = [];
    foreach ($array1 as $v) {
        if (in_array($v[$com_field_id], $arr_diff)) {
            $arr_r[] = $v;
        }
    }
    return $arr_r;
}

/**
 * 判断数组的维度
 * @param $array
 * @return int
 */
function array_depth($array) {
    if (!is_array($array)) return 0;
    $max_depth = 1;
    foreach ($array as $value) {
        if (is_array($value)) {
            $depth = array_depth($value) + 1;

            if ($depth > $max_depth) {
                $max_depth = $depth;
            }
        }
    }
    return $max_depth;
}

/**
 * 将图片转为base64
 * @param $image_file
 * @return string
 */
function get_img_base64($image_file = null) {
    $base64_image = '';
    $image_info = getimagesize($image_file);
    $image_data = fread(fopen($image_file, 'r'), filesize($image_file));
    $base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
    return $base64_image;
}

/**
 * 返回时间毫秒数
 * 与java对接时time()返回的时间戳不够精细
 * @return float
 */
function get_milli_second() {
    list($t1, $t2) = explode(' ', microtime());
    return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
}

/**
 * 日期之间间隔天数
 * @param $day1
 * @param $day2
 * @return float
 */
function date_day_between($day1, $day2) {
    $second1 = strtotime($day1);
    $second2 = strtotime($day2);

    if ($second1 < $second2) {
        $tmp = $second2;
        $second2 = $second1;
        $second1 = $tmp;
    }
    return ($second1 - $second2) / 86400;
}

/**
 * 将已经上传的临时文件，保存到相应的web框架目录与oit中
 * @param $file_name
 * @param $temp_web_path
 * @param $to_web_path
 * @param $to_oit_info
 * @return bool
 */
function move_file_to_oit($file_name, $temp_web_path, $to_web_path, $to_oit_info) {
    $temp_file = $temp_web_path . $file_name;
    $file_info = pathinfo($temp_file);
    $file_size = filesize($temp_file);
    $file_time = filemtime($temp_file);
    // 检测是否存在文件
    if (false == file_exists($temp_file)) {
        return [
            'state' => false,
            'info' => lang('没有找到临时文件'),
        ];
    }

    // 把临时文件移至web框架目录
    $web_file = $to_web_path . $to_oit_info['obj_id'] . '.' . $to_oit_info['file_type'] . '.' . $file_info['extension'];
    if (!copy($temp_file, $web_file)) {
        return [
            'state' => false,
            'info' => lang('复制到 web 框架目录失败'),
        ];
    }

    // 把临时文件移至oit文件目录中
    // 获取 app_fs_file 文件序列号
    $app_fs_file = new AppFsFile();
    // odf00000001.扩展名 8位补位码
    $file_id = $app_fs_file->get_new_id(null, 'int', 'odf', 8);  // 序号
    $save_name = $app_fs_file->get_new_id(null, 'pad', 'odf', 8);  // 补齐文件号
    $oit_file_path = config('oit_fs_file_path');  // 默认oit文件所在路径
    $oit_new_file = $oit_file_path . $save_name . '.' . $file_info['extension'];  // 完整路径
    if (!copy($temp_file, $oit_new_file)) {
        return [
            'state' => false,
            'info' => lang('复制到 oit 文件目录中失败'),
        ];
    }
    // 文件复制成功, 插入数据至 app_fs_file 表中
    $data = [
        'file_id' => $file_id,
        'obj_id' => $to_oit_info['obj_id'],
        'file_ext' => $file_info['extension'],
        'file_type' => $to_oit_info['file_type'],
        'file_size' => $file_size,
        'create_date' => date('Ymd'),
        'create_user_id' => session('user_id'),
        'org_file_name' => $web_file,
    ];
    $result = $app_fs_file->save($data);
    if (empty($result)) {
        return [
            'state' => false,
            'info' => lang('保存文件数据到 app_fs_file 中出错'),
        ];
    }

    return [
        'state' => true,
        'info' => lang('保存临时文件到web框架与oit文件中成功: ' . $temp_file),
        'oit_obj' => $file_id,
        'web_path' => $web_file,
    ];
}


