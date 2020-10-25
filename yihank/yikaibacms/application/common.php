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
use \think\Db;
// 应用公共文件

/**
 * 获取所有模块Service
 * @param string $name 指定service名
 * @return array
 */
function get_all_service($name,$method,$vars=array()){
    if(empty($name))return null;
    $apiPath = APP_PATH.'*/service/'.$name.'.php';
    $apiList = glob($apiPath);
    if(empty($apiList)){
        return;
    }
    $appPathStr = strlen(APP_PATH);
    $method = 'get'.$method.$name;
    $data = array();
    foreach ($apiList as $value) {
        $path = substr($value, $appPathStr,-4);
        $path = str_replace('\\', '/',  $path);
        $appName = explode('/', $path);
        $appName = $appName[0];
        $config = load_config($appName .'/config');
        if(!empty($config['APP_SYSTEM']) &&( !empty($config['APP_STATE']) || !empty($config['APP_INSTALL']))){
            continue;
        }
        $class = model($appName.'/'.$name,'service');
        if(method_exists($class,$method)){
            $data[$appName] = $class->$method($vars);
        }
    }
    return $data;
}
/**
 * 获取指定模块Service
 * @param string $name 指定service名
 * @return Service
 */
function service($appName,$name,$method,$vars=array()){
    $config = load_config($appName .'/config');
    if(!empty($config['APP_SYSTEM']) &&( !empty($config['APP_STATE']) || !empty($config['APP_INSTALL']))){
        return;
    }
    $class = model($appName.'/'.$name,'service');
    if(method_exists($class,$method)){
        return $class->$method($vars);
    }
}

/**
 * 读取模块配置
 * @param string $file 调用文件
 * @return array
 */
function load_config($file){
    $file = get_config_file($file);
    return require $file;
}
/**
 * 解析配置文件路径
 * @param string $file 文件路径或简写路径
 * @return dir
 */
function get_config_file($file){
    $name = $file;
    if(!is_file($file)){
        $str = explode('/', $file);
        $strCount = count($str);
        switch ($strCount) {
            case 1:
                //$app = APP_NAME;
                $app = 'admin';
                $name = $str[0];
                break;
            case 2:
                $app = $str[0];
                $name = $str[1];
                break;
        }
        $app = strtolower($app);
        if(empty($app)&&empty($file)){
            throw new \Exception("Config '{$file}' not found'", 500);
        }
        $file = APP_PATH . "{$app}/conf/{$name}.php";
        if(!file_exists($file)){
            throw new \Exception("Config '{$file}' not found", 500);
        }
    }
    return $file;
}
/**
 * 二维数组排序
 * @param array $array 排序的数组
 * @param string $key 排序主键
 * @param string $type 排序类型 asc|desc
 * @param bool $reset 是否返回原始主键
 * @return array
 */
function array_order($array, $key, $type = 'asc', $reset = false)
{
    if (empty($array) || !is_array($array)) {
        return $array;
    }
    foreach ($array as $k => $v) {
        $keysvalue[$k] = $v[$key];
    }
    if ($type == 'asc') {
        asort($keysvalue);
    } else {
        arsort($keysvalue);
    }
    $i = 0;
    foreach ($keysvalue as $k => $v) {
        $i++;
        if ($reset) {
            $new_array[$k] = $array[$k];
        } else {
            $new_array[$i] = $array[$k];
        }
    }
    return $new_array;
}

//获取ip
function get_client_ip(){
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
        $ip = getenv("REMOTE_ADDR");
    else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
        $ip = $_SERVER['REMOTE_ADDR'];
    else
        $ip = "unknown";
    return $ip;
}
/**
 * 调用指定模块的API
 * @param string $name  指定api名
 * @return Api
 */
function api($appName,$name,$method,$vars=array()){
    header("Content-type: text/html; charset=utf-8");
    $config = load_config($appName .'/config');
    if(!$config['APP_SYSTEM'] &&( !$config['APP_STATE'] || !$config['APP_INSTALL'])){
        return;
    }
    $class = model($appName.'/'.$name,'api');
    if(method_exists($class,$method)){
        return $class->$method($vars);
    }
}
/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data) {
    //数据类型检测
    if(!is_array($data)){
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}
/**
 * html代码输入
 */
function html_in($str){
    $str=htmlspecialchars($str);
    if(!get_magic_quotes_gpc()) {
        $str = addslashes($str);
    }
    return $str;
}

/**
 * html代码输出
 */
function html_out($str){
    if(function_exists('htmlspecialchars_decode')){
        $str=htmlspecialchars_decode($str);
    }else{
        $str=html_entity_decode($str);
    }
    $str = stripslashes($str);
    return $str;
}
/**
 * 站点设置信息调取
 */
function get_site($name='site_title'){
    $config_info=model('admin/Config')->getInfo();
    return $config_info[$name];
}

/**
 * 删除目录及目录下所有文件或删除指定文件
 * @param str $path   待删除目录路径
 * @param int $delDir 是否删除目录，1或true删除目录，0或false则只删除文件保留目录（包含子目录）
 * @return bool 返回删除状态
 */
function delDirAndFile($path, $delDir = FALSE) {
    $handle = opendir($path);
    if ($handle) {
        while (false !== ( $item = readdir($handle) )) {
            if ($item != "." && $item != "..")
                is_dir("$path/$item") ? delDirAndFile("$path/$item", $delDir) : unlink("$path/$item");
        }
        closedir($handle);
        if ($delDir)
            return rmdir($path);
    }else {
        if (file_exists($path)) {
            return unlink($path);
        } else {
            return FALSE;
        }
    }
}

/**
 * @param $arr
 * @param $key_name
 * @return array
 * 将数据库中查出的列表以指定的 id 作为数组的键名
 */
function convert_arr_key($arr, $key_name){
    $arr2 = array();
    foreach($arr as $key => $val){
        $arr2[$val[$key_name]] = $val;
    }
    return $arr2;
}
/**
 * @param $arr
 * @param $key_name
 * @param $key_name2
 * @return array
 * 将数据库中查出的列表以指定的 id 作为数组的键名 数组指定列为元素 的一个数组
 */
function get_id_val($arr, $key_name,$key_name2)
{
    $arr2 = array();
    foreach($arr as $key => $val){
        $arr2[$val[$key_name]] = $val[$key_name2];
    }
    return $arr2;
}
//php获取中文字符拼音首字母
function getFirstCharter($str){
    if(empty($str))
    {
        return '';
    }
    $fchar=ord($str{0});
    if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});
    $s1=iconv('UTF-8','gbk',$str);
    $s2=iconv('gbk','UTF-8',$s1);
    $s=$s2==$str?$s1:$str;
    $asc=ord($s{0})*256+ord($s{1})-65536;
    if($asc>=-20319&&$asc<=-20284) return 'A';
    if($asc>=-20283&&$asc<=-19776) return 'B';
    if($asc>=-19775&&$asc<=-19219) return 'C';
    if($asc>=-19218&&$asc<=-18711) return 'D';
    if($asc>=-18710&&$asc<=-18527) return 'E';
    if($asc>=-18526&&$asc<=-18240) return 'F';
    if($asc>=-18239&&$asc<=-17923) return 'G';
    if($asc>=-17922&&$asc<=-17418) return 'H';
    if($asc>=-17417&&$asc<=-16475) return 'J';
    if($asc>=-16474&&$asc<=-16213) return 'K';
    if($asc>=-16212&&$asc<=-15641) return 'L';
    if($asc>=-15640&&$asc<=-15166) return 'M';
    if($asc>=-15165&&$asc<=-14923) return 'N';
    if($asc>=-14922&&$asc<=-14915) return 'O';
    if($asc>=-14914&&$asc<=-14631) return 'P';
    if($asc>=-14630&&$asc<=-14150) return 'Q';
    if($asc>=-14149&&$asc<=-14091) return 'R';
    if($asc>=-14090&&$asc<=-13319) return 'S';
    if($asc>=-13318&&$asc<=-12839) return 'T';
    if($asc>=-12838&&$asc<=-12557) return 'W';
    if($asc>=-12556&&$asc<=-11848) return 'X';
    if($asc>=-11847&&$asc<=-11056) return 'Y';
    if($asc>=-11055&&$asc<=-10247) return 'Z';
    return null;
}


/**
 * 递归调用找到 重子重孙
 * @param type $cat_id
 */
function getCatGrandson2($cat_id){
    $GLOBALS['catGrandson'][] = $cat_id;
    foreach($GLOBALS['category_id_arr'] as $k => $v){
        // 找到孙子
        if($v == $cat_id){
            getCatGrandson2($k); // 继续找孙子
        }
    }
}

/**
 * 多个数组的笛卡尔积
 *
 * @param unknown_type $data
 */
function combineDika() {
    $data = func_get_args();
    $data = current($data);
    $cnt = count($data);
    $result = array();
    $arr1 = array_shift($data);
    foreach($arr1 as $key=>$item)
    {
        $result[] = array($item);
    }

    foreach($data as $key=>$item)
    {
        $result = combineArray($result,$item);
    }
    return $result;
}
/**
 * 两个数组的笛卡尔积
 * @param unknown_type $arr1
 * @param unknown_type $arr2
 */
function combineArray($arr1,$arr2) {
    $result = array();
    foreach ($arr1 as $item1)
    {
        foreach ($arr2 as $item2)
        {
            $temp = $item1;
            $temp[] = $item2;
            $result[] = $temp;
        }
    }
    return $result;
}

/**
 * 将二维数组以元素的某个值作为键 并归类数组
 * array( array('name'=>'aa','type'=>'pay'), array('name'=>'cc','type'=>'pay') )
 * array('pay'=>array( array('name'=>'aa','type'=>'pay') , array('name'=>'cc','type'=>'pay') ))
 * @param $arr 数组
 * @param $key 分组值的key
 * @return array
 */
function group_same_key($arr,$key){
    $new_arr = array();
    foreach($arr as $k=>$v ){
        $new_arr[$v[$key]][] = $v;
    }
    return $new_arr;
}
/**
 * 过滤数组元素前后空格 (支持多维数组)
 * @param $array 要过滤的数组
 * @return array|string
 */
function trim_array_element($array){
    if(!is_array($array))
        return trim($array);
    return array_map('trim_array_element',$array);
}

/**
 * 获取url 中的各个参数  类似于 pay_code=alipay&bank_code=ICBC-DEBIT
 * @param type $str
 * @return type
 */
function parse_url_param($str){
    $data = array();
    $parameter = explode('&',end(explode('?',$str)));
    foreach($parameter as $val){
        $tmp = explode('=',$val);
        $data[$tmp[0]] = $tmp[1];
    }
    return $data;
}
/**
 * 中文字符串截取
 */
function len($str, $len=0)
{
    if(!empty($len)){
        return \org\Util::msubstr($str, 0, $len);
    }else{
        return $str;
    }
}