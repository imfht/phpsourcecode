<?php

/**
 * 打印函数
 * @param $data 数组
 * @return void
 */
function p($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

/**
 * 错误提示
 * @param $msg 提示信息，string
 * @return void
 */
function clmao_die($msg) {
    header("content-type:text/html;charset=utf-8");
    die($msg);
}

/**
 * 递归重组节点信息为多维数组
 * @param $node 节点 array
 * @param $access 权限 array
 * @param $pid 父ID int
 * @return array
 */
function node_merge($node, $access = null, $pid = 0) {
    $arr = array();
    foreach ($node as $v) {
        if (is_array($access)) {
            $v[access] = in_array($v['id'], $access) ? 1 : 0;
        }

        if ($v['pid'] == $pid) {
            $v['child'] = node_merge($node, $access, $v['id']);
            $arr [] = $v;
        }
    }
    return $arr;
}

/**
 * 生成网站标题
 * @param $actionName 当前方法名
 * @param $categoryName 当前分类名
 * @param $contentName 当前文章名
 * @return void
 */
function autoTitle($actionName, $categoryName = '', $contentName = '') {
    $actionName = strtolower($actionName);
    if ($actionName == 'index') {
        echo getSiteOption('siteName') . ' | ' . getSiteOption('homeTitle');
    } else if ($actionName == 'category') {
        echo $categoryName . ' | ' . getSiteOption('siteName');
    } else if ($actionName == 'content') {
        echo $contentName . ' | ' . $categoryName . ' | ' . getSiteOption('siteName');
    } else if (strpos($actionName, '搜索结果')) {
        echo $actionName;
    } else if ($actionName == 'login') {
        echo '用户登陆';
    }
}

/**
 * 生成网站面包屑导航
 * @param $actionName 当前方法名
 * @param $categoryName 当前分类名
 * @param $contentName 当前文章名
 * @param $categoryId 当前文章ID
 * @return void
 */
function autoPosition($actionName, $categoryName = '', $contentName = '', $categoryId = 0) {
    $actionName = strtolower($actionName);
    $home = U('/');
    $category = getCategoryUrl($categoryId);
    if ($actionName == 'index') {
        echo '<li class="active">首页</li>';
    } else if ($actionName == 'category') {
        echo "<li><a href='$home'>首页</a></li>"
        . "<li class='active'>$categoryName</li>";
    } else if ($actionName == 'content') {
        echo "<li><a href='$home'>首页</a></li>"
        . "<li><a  href='$category'>$categoryName</a></li>"
        . "<li class='active'>$contentName</li>";
    } else if (strpos($actionName, '搜索结果')) {
        echo "<li><a href='$home'>首页</a></li>"
        . "<li class='active'>[$contentName]的搜索结果</li>";
    }
}

/**
 * 生成分类页的Url
 * @param $cid 分类ID
 * @return string
 */
function getCategoryUrl($cid) {
    return U('/Home/Index/category/', array('id' => $cid));
}

/**
 * 生成网站描述
 * @param $actionName 当前方法名
 * @param $categoryName 当前分类名
 * @param $contentName 当前文章名
 * @param $contentDec 当前文章描述
 * @return void
 */
function autoDec($actionName, $categoryName = '', $contentName = '', $contentDec = '') {
    $actionName = strtolower($actionName);
    if ($actionName == 'index') {
        echo getSiteOption('homeDsc');
    } else if ($actionName == 'category') {
        echo $categoryName;
    } else if ($actionName == 'content') {
        echo $contentDec;
    }
}

/**
 * 生成网站关键字
 * @param $actionName 当前方法名
 * @param $categoryName 当前分类名
 * @return void
 */
function autoKey($actionName, $categoryName) {
    $actionName = strtolower($actionName);
    if ($actionName == 'index') {
        echo getSiteOption('homeKey');
    } else {
        echo $categoryName;
    }
}

/**
 * 创建XML中节点
 * @param $dom dom
 * @param $item item
 * @param $data 值
 * @param $attribute 属性
 * @return void
 */
function create_item($dom, $item, $data, $attribute) {
    if (is_array($data)) {
        foreach ($data as $key => $val) {
            //  创建元素 
            $$key = $dom->createElement($key);
            $item->appendchild($$key);
            //  创建元素值 
            $text = $dom->createTextNode($val);
            $$key->appendchild($text);
            if (isset($attribute[$key])) {
                //  如果此字段存在相关属性需要设置 
                foreach ($attribute[$key] as $akey => $row) {
                    //  创建属性节点 
                    $$akey = $dom->createAttribute($akey);
                    $$key->appendchild($$akey);
                    // 创建属性值节点 
                    $aval = $dom->createTextNode($row);
                    $$akey->appendChild($aval);
                }
            }
        }
    }
}

/**
 * 判断AGENT是否为移动设备
 * @return bool
 */
function is_mobile() {
    $is_mobile = cookie('is_mobile');
    if (!empty($is_mobile)) {
        return true;
    }
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $mobile_agents = Array("240x320", "acer", "acoon", "acs-", "abacho", "ahong", "airness", "alcatel", "amoi", "android", "anywhereyougo.com", "applewebkit/525", "applewebkit/532", "asus", "audio", "au-mic", "avantogo", "becker", "benq", "bilbo", "bird", "blackberry", "blazer", "bleu", "cdm-", "compal", "coolpad", "danger", "dbtel", "dopod", "elaine", "eric", "etouch", "fly ", "fly_", "fly-", "go.web", "goodaccess", "gradiente", "grundig", "haier", "hedy", "hitachi", "htc", "huawei", "hutchison", "inno", "ipad", "ipaq", "ipod", "jbrowser", "kddi", "kgt", "kwc", "lenovo", "lg ", "lg2", "lg3", "lg4", "lg5", "lg7", "lg8", "lg9", "lg-", "lge-", "lge9", "longcos", "maemo", "mercator", "meridian", "micromax", "midp", "mini", "mitsu", "mmm", "mmp", "mobi", "mot-", "moto", "nec-", "netfront", "newgen", "nexian", "nf-browser", "nintendo", "nitro", "nokia", "nook", "novarra", "obigo", "palm", "panasonic", "pantech", "philips", "phone", "pg-", "playstation", "pocket", "pt-", "qc-", "qtek", "rover", "sagem", "sama", "samu", "sanyo", "samsung", "sch-", "scooter", "sec-", "sendo", "sgh-", "sharp", "siemens", "sie-", "softbank", "sony", "spice", "sprint", "spv", "symbian", "tablet", "talkabout", "tcl-", "teleca", "telit", "tianyu", "tim-", "toshiba", "tsm", "up.browser", "utec", "utstar", "verykool", "virgin", "vk-", "voda", "voxtel", "vx", "wap", "wellco", "wig browser", "wii", "windows ce", "wireless", "xda", "xde", "zte");

    foreach ($mobile_agents as $device) {
        if (stristr($user_agent, $device)) {
            return true;
        }
    }
    return false;
}

/**
 * 转义字符串或者数组
 * @param $value 数组或者字符串
 * @return array or string
 */
function wp_slash($value) {
    if (is_array($value)) {
        foreach ($value as $k => $v) {
            if (is_array($v)) {
                $value[$k] = wp_slash($v);
            } else {
                $value[$k] = addslashes($v);
            }
        }
    } else {
        $value = addslashes($value);
    }

    return $value;
}

/**
 * 检测字符串或者数组值是否为数字和字母
 * @param $value 数组或者字符串
 * @return bool
 */
function clmao_ctype_alnum($value) {
    if (is_array($value)) {
        foreach ($value as $k => $v) {
            if (is_array($v)) {
                $value[$k] = clmao_ctype_alnum($v);
            } else {
                if (!ctype_alnum($v)) {
                    return false;
                }
            }
        }
    } else {
        if (!ctype_alnum($value)) {
            return false;
        }
    }
    return true;
}

/**
 * 生成MD5的字符串
 * $str 字符串
 * $return string
 */
function clmao_md5_half($str) {
    return md5('fc0bd22cbc588b608d8f8019765a10da' . md5($str . 'fc0bd22cbc588b608d8f8019765a10da'));
}

/**
 * 字符串的加密和解密
 * @param $str 要处理的字符串
 * @param $key 密钥
 * @param $act 1代表加密，0代表解密，默认为1
 * @return string
 */
function clmao_crypt($str, $key, $act = 1) {
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    if ($act == 1) {
        return mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $str, MCRYPT_MODE_ECB, $iv);
    } else {
        return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $str, MCRYPT_MODE_ECB, $iv), "\0\4");
    }
}

/**
 * 输出《历史上的今天》信息
 * @return void
 */
function dateFromClmao() {
    date_default_timezone_set('PRC');
    $i = date('n', time());
    $filename = './Public/history/today' . $i . '.xml';
    $xml = simpleXML_load_file($filename);
    $todaytime = "date" . date("n\mj\d", time());
    $e = $xml->$todaytime->event;
    $da = $xml->$todaytime->date;
    echo str_replace('，', $da . "，", $e);
}

/**
 * 获取今年的开始时间时间戳
 * @param $year 年份 如2014
 * @return int
 */
function clmao_getYearTime($year = 0) {
    if ($year != 0) {
        return strtotime("1 January $year");
    }
    $y = date("y", time());
    return strtotime("1 January $y");
}

/**
 * 遍历一个文件夹下的所有文件
 * @param $dir 目录地址
 * @return array
 */
function clmao_scandir($dir) {
    $files = array();
    $i = 10;
    if (is_dir($dir)) {
        if ($handle = opendir($dir)) {
            while (($file = readdir($handle)) !== false) {
                if ($file != '.' && $file != '..') {
                    if (is_dir($dir . '/' . $file)) {
                        $files[] = clmao_scandir($dir . '/' . $file);
                    } else {

                        $files[] = $dir . '/' . $file;
                        $i++;
                    }
                }
            }
            closedir($handle);
            return array_values($files);
        }
    }
}

/**
 * 删除一个文件夹下的所有文件
 * @param $dir 目录地址
 * @return array
 */
function clmao_deldir($dir) {
    $files = array();
    if (is_dir($dir)) {
        if ($handle = opendir($dir)) {
            while (($file = readdir($handle)) !== false) {
                if ($file != '.' && $file != '..') {
                    if (is_dir($dir . '/' . $file)) {
                        $files[] = clmao_scandir($dir . '/' . $file);
                    } else {
                        unlink($dir . '/' . $file);
                    }
                }
            }
            closedir($handle);
        }
    }
}

$GLOBALS['one_arr'] = array();

/**
 * 将多维数组转化为一维数组
 * @param $arr 数组
 * @return $GLOBALS['one_arr']
 */
function clmao_getOneArr($arr) {
    foreach ($arr as $k => $v) {
        if (is_array($v)) {
            clmao_getOneArr($v);
        } else {
            $GLOBALS['one_arr'][] = $v;
        }
    }
}

/**
 * 数组的分页
 * @param $arr 数组
 * @return array
 */
function clmao_getArrPage($arr, $start, $length) {
    $data = array();
    $i = 0;
    foreach ($arr as $k => $v) {

        if ($k >= $start) {
            if ($i < $length) {
                $data[] = $v;
                ++$i;
            }
        }
    }
    return $data;
}

/**
 * 验证文件是否合法
 * @param $file 文件名
 * @return bool or void
 */
function clmao_validate_file($file) {
    if (false !== strpos($file, '..'))
        clmao_die('文件非法');

    if (false !== strpos($file, './'))
        clmao_die('文件非法');
    if (':' == substr($file, 1, 1))
        clmao_die('文件非法');
    if (preg_match('/\/@/', $file))
        clmao_die('文件非法');

    return true;
}

/**
 * 验证数组是否为空
 * @param $arr 一维数组
 * @return void
 */
function clmao_arrIsEmpty($arr) {
    foreach ($arr as $v) {
        if (empty($v)) {
            clmao_die('数据不能有空项');
        }
    }
}

/**
 * 截取UTF-8编码下字符串的函数
 *
 * @param   string      $str        被截取的字符串
 * @param   int         $beginIndex     截取的开始
 * @param   int        $length     截取的长度
 *
 * @return  string
 */
function csubstr($string, $beginIndex, $length) {
    if (strlen($string) < $length) {
        return substr($string, $beginIndex);
    }

    $char = ord($string[$beginIndex + $length - 1]);
    if ($char >= 224 && $char <= 239) {
        $str = substr($string, $beginIndex, $length - 1);
        return $str;
    }

    $char = ord($string[$beginIndex + $length - 2]);
    if ($char >= 224 && $char <= 239) {
        $str = substr($string, $beginIndex, $length - 2);
        return $str;
    }

    return substr($string, $beginIndex, $length);
}

/**
 * 截取站点信息
 *
 * @param   string      $key        key
 *
 * @return  string  value
 */
function getSiteOption($key) {
    $option = S('option');
    if (empty($option)) {
        $option = M('option')->select();
        S('option', $option);
        $option = S('option');
    }
    foreach ($option as $k => $value) {
        if ($value['key'] == $key)
            return $value['value'];
    }
}

/* 载入静态文件 */

function loadTPL() {
    $con_action = MODULE_NAME . '_' . CONTROLLER_NAME . '_' . ACTION_NAME;
    if (MODULE_NAME == 'Home') {
        if (CONTROLLER_NAME == 'Index') {
            if (ACTION_NAME == 'category') {
                $con_action = $con_action . '_' . I('id', '', 'intval');
                $con_action = $con_action . '_' . I('p', '', 'intval');
            } else if (ACTION_NAME == 'index') {
                $con_action = $con_action . '_' . I('p', '', 'intval');
            }
        }
    }else if(MODULE_NAME == 'Ajax'){
        if (CONTROLLER_NAME == 'Index') {
            if (ACTION_NAME == 'index') {
                $con_action = $con_action . '_' . I('cid', '', 'intval');
            }
        }
    }
    $filename = APP_PATH . '/Html/' . $con_action . '.tpl';
//echo $filename;die;
    if (is_file($filename)) {
        include($filename);
        die;
    }
}
