<?php
// +----------------------------------------------------------------------
// | TpAndVue.
// +----------------------------------------------------------------------
// | FileName: helper.php
// +----------------------------------------------------------------------
// | Author: King east <1207877378@qq.com>
// +----------------------------------------------------------------------


/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author [阶级娃儿] <[<email 262877348@qq.com>]>
 */
function is_login(){
    $user = session('user_auth_session');
    if (empty($user)) {
        return 0;
    } else {
        return session('auth_login_sign') == data_auth_sign($user) ? $user['LoginId'] : 0;
    }
}


/**
 * 检测当前用户是否为管理员
 * @return boolean true-管理员，false-非管理员
 * @author [阶级娃儿] <[<email 262877348@qq.com>]>
 */

function is_administrator($uuid = null)
{
    $uuid = is_null($uuid) ? is_login() : $uuid;
    return $uuid && (intval($uuid) === config('user_administrator'));
}



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
 * [encode 简单对称加密算法之加密]
 * @param  [string] $string [需要加密的字串]
 * @param  [string] $skey   [加密EKY]
 * @return [string]
 */
function encode($string = '', $skey = 'http://www.51bpbpz.cn/') {
    $strArr = str_split(base64_encode($string));
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key < $strCount && $strArr[$key].=$value;
    return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));
}

/**
 * [decode 简单对称加密算法之解密]
 * @param  [string] $string [需要解密的字串]
 * @param  [string] $skey   [解密KEY]
 * @return [string]
 */
function decode($string = '', $skey = 'http://www.51bpbpz.cn/') {
    $strArr = str_split(str_replace(array('O0O0O', 'o000o', 'oo00o'), array('=', '+', '/'), $string), 2);
    //print_r(str_split($skey));
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key <= $strCount  && isset($strArr[$key]) && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
    return base64_decode(join('', $strArr));
}

/**
 * 把返回的数据集转换成Tree
 * @access public
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 */
function list_to_tree2($list, $pk='id',$pid = 'pid',$child = '_child',$root=0) {
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}
/**
 * [arr2str 数组转换为字符串，主要用于把分隔符调整到第二个参数]
 * @param  [array] $arr  [要连接的数组]
 * @param  [string] $glue [分割符]
 * @return [string]
 */
function arr2str($arr, $glue = ','){
    return implode($glue, $arr);
}

/**
 * [str2arr 字符串转换为数组，主要用于把分隔符调整到第二个参数]
 * @param  [string] $str  [要分割的字符串]
 * @param  [string] $glue [分割符]
 * @return [array]
 */
function str2arr($str, $glue = ','){
    return explode($glue, $str);
}

/**
 * 图片地址转化
 * TODO 需要修复
 * @param  [type] $str 路径
 * @param  string $rule oss规则名
 * @param  integer $remote 是否远程
 * @return [type] [description]
 */
function cdn_img_url($str,$rule='',$remote = 1){
    $request = Request::instance();
    if(Config::get('oss_enable') == 1){

        if (Config::get('oss_alias_url')) {
            if($rule !='') $rule = '!'.$rule;
            if($str ==''){
                if($remote == 1){
                    $str = Config::get('oss_alias_url')."/public/static/base/images/default.jpg".$rule;
                }else{
                    $str = $request->domain()."/public/static/base/images/default.jpg";
                    //$str = "/public/base/images/default.jpg";
                }
            } else {
                if($remote == 1){
                    $str = Config::get('oss_alias_url').$str.$rule;
                }else{
                    $str = $request->domain().$str;
                    //$str = $str;
                }
            }
        }
    } else {
        if($str){
            $str = $request->domain().$str;
        } else {
            $str = $request->domain().'/public/static/base/images/default.jpg';
        }
    }

    return $str;
}

/**
 * full_url   渲染链接
 * @param $path
 * @return mixed
 */
function root_full_url($path)
{
    //不存在http://
    $not_http_remote = (strpos($path, 'http://') === false);
    //不存在https://
    $not_https_remote = (strpos($path, 'https://') === false);
    if ($not_http_remote && $not_https_remote) {
        //本地url
        return str_replace('//', '/', getRootUrl() . $path); //防止双斜杠的出现
    } else {
        //远端url
        return $path;
    }
}


/**获取网站的根Url
 * @return string
 */
function getRootUrl()
{
    // if (config('URL_MODEL') == 2)
    //     return BASE_PATH . '/';
    return empty(Env::get('APP_PATH')) ? Env::get('APP_PATH') : '/';
}



define ("DEBUG_MODE", true);
// 应用公共文件
/**
 * [get_config_type 获取配置的类型]
 * @param  [integer] $type [配置类型]
 * @return [string ]        [description]
 */
function get_config_type($type=0){
    $list = Config('config_type_list.');
    return $list[$type];
}

/**
 * [get_config_group 获取配置的分组]
 * @param  [integer] $group [配置分组]
 * @return [string]         [description]
 */
function get_config_group($group=0){
    $list = Config('config_group_list.');
    return $group ? $list[$group] : '无分组';
}

/**
 * [parse_config_attr 分析枚举类型配置值 格式 a:名称1,b:名称2]
 * @param  [string] $string [需要解析的字符串]
 */
function parse_config_attr($string) {
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if(strpos($string,':')){
        $value  =   array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k]   = $v;
        }
    }else{
        $value  =   $array;
    }
    return $value;
}

/**
 * 导出excel
 * @param  array  $data     [description]
 * @param  array  $title    [description]
 * @param  string $filename [description]
 * @return [type]           [description]
 */
function exportExcel($data=array(),$title=array(),$filename='report')
{
    header("Content-type:application/octet-stream");
    header("Accept-Ranges:bytes");
    header("Content-type:application/vnd.ms-excel");
    header("Content-Disposition:attachment;filename=".$filename.".xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    echo "<table style='border:solid 1px #ddd;'>";
    echo "<tr style='border:solid 1px #ddd;'>";
    //导出表头（也就是表中拥有的字段）
    foreach ($data[0] as $key => $value) {
        echo "<th>".$value."</th>";
    }
    echo "</tr>";

    unset($data[0]);

    foreach ($data as $key => $row) {
        echo "<tr style='border:solid 1px #ddd;'>";
        foreach ($row as $key => $value) {
            echo "<td>".$value."</td>";
        }
        echo "</tr>";
    }

    echo "</table>";

}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 6; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 * @author huajie <banhuajie@163.com>
 */
function time_format($time = NULL, $format = 'Y-m-d H:i') {
    $time = $time === NULL ? time() : intval($time);
    return date($format, $time);
}


