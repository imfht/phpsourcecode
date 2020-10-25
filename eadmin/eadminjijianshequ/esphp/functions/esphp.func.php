<?php

// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------
use esclass\Cache;
use esclass\Session;
use esclass\Cookie;
use esclass\database;
use esclass\Request;
use esclass\ValidateCode;
use esclass\Loader;
use esclass\Debug;
use esclass\Hook;
use extend\JWT\JWT;

use app\admin\logic\Log as LogicLog;

function strapiarr()
{

    $args = func_get_args();
    $opt  = array_shift($args);

    $count = count($args);
    $str   = 'array(';
    foreach ($args as $key => $vo) {
        if (strpos($vo, '$') !== false) {
            $str .= $vo;
        } else {
            $str .= "'" . $opt[$key] . "'" . '=>' . "'" . $vo . "'";


        }
        if ($key != $count - 1) {
            $str .= ',';
        }
    }
    $str .= ')';

    return $str;
}

function remove_xss($html)
{
    $html = htmlspecialchars_decode($html);
    preg_match_all("/\<([^\<]+)\>/is", $html, $ms);

    $searchs[]  = '<';
    $replaces[] = '&lt;';
    $searchs[]  = '>';
    $replaces[] = '&gt;';

    if ($ms[1]) {
        $allowtags = 'video|attach|img|a|font|div|table|tbody|caption|tr|td|th|br|p|b|strong|i|u|em|span|ol|ul|li|blockquote|strike|pre|code|embed';
        $ms[1]     = array_unique($ms[1]);
        foreach ($ms[1] as $value) {
            $searchs[] = "&lt;" . $value . "&gt;";

            $value = str_replace('&amp;', '_uch_tmp_str_', $value);
            $value = string_htmlspecialchars($value);
            $value = str_replace('_uch_tmp_str_', '&amp;', $value);

            $value    = str_replace(['\\', '/*'], ['.', '/.'], $value);
            $skipkeys = ['onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate',
                'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange',
                'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick',
                'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate',
                'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete',
                'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel',
                'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart',
                'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop',
                'onsubmit', 'onunload', 'javascript', 'script', 'eval', 'behaviour', 'expression'];
            $skipstr  = implode('|', $skipkeys);
            $value    = preg_replace(["/($skipstr)/i"], '.', $value);
            if (!preg_match("/^[\/|\s]?($allowtags)(\s+|$)/is", $value)) {
                $value = '';
            }
            $replaces[] = empty($value) ? '' : "<" . str_replace('&quot;', '"', $value) . ">";
        }
    }
    $html = str_replace($searchs, $replaces, $html);
    $html = htmlspecialchars($html);
    return $html;
}

function string_htmlspecialchars($string, $flags = null)
{
    if (is_array($string)) {
        foreach ($string as $key => $val) {
            $string[$key] = string_htmlspecialchars($val, $flags);
        }
    } else {
        if ($flags === null) {
            $string = str_replace(['&', '"', '<', '>'], ['&amp;', '&quot;', '&lt;', '&gt;'], $string);
            if (strpos($string, '&amp;#') !== false) {
                $string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $string);
            }
        } else {
            if (PHP_VERSION < '5.4.0') {
                $string = htmlspecialchars($string, $flags);
            } else {
                if (!defined('CHARSET') || (strtolower(CHARSET) == 'utf-8')) {
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

function string_remove_xss($val)
{

    $val = htmlspecialchars_decode($val);
    $val = strip_tags($val, '<img><attach><u><p><b><i><a><strike><pre><code><font><blockquote><span><ul><li><table><tbody><tr><td><ol><iframe><embed>');

    $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);


    $search = 'abcdefghijklmnopqrstuvwxyz';
    $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $search .= '1234567890!@#$%^&*()';
    $search .= '~`";:?+/={}[]-_|\'\\';


    for ($i = 0; $i < strlen($search); $i++) {
        $val = preg_replace('/(&#[xX]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val);
        $val = preg_replace('/(�{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val);
    }

    $ra1 = ['embed', 'iframe', 'frame', 'script', 'ilayer', 'layer', 'javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'script', 'object', 'frameset', 'bgsound', 'title', 'base'];
    $ra2 = ['onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload'];
    $ra  = array_merge($ra1, $ra2);

    $found = true;
    while ($found == true) {
        $val_before = $val;
        for ($i = 0; $i < sizeof($ra); $i++) {
            $pattern = '/';
            for ($j = 0; $j < strlen($ra[$i]); $j++) {
                if ($j > 0) {
                    $pattern .= '(';
                    $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                    $pattern .= '|';
                    $pattern .= '|(�{0,8}([9|10|13]);)';
                    $pattern .= ')*';
                }
                $pattern .= $ra[$i][$j];
            }
            $pattern     .= '/i';
            $replacement = substr($ra[$i], 0, 2) . '<x>' . substr($ra[$i], 2);
            $val         = preg_replace($pattern, $replacement, $val);
            if ($val_before == $val) {
                $found = false;
            }
        }
    }
    $val = htmlspecialchars($val);
    return $val;
}

/**
 * 页面数组提交后格式转换
 */
function transform_array($array)
{

    $new_array = [];
    $key_array = [];

    foreach ($array as $key => $val) {

        $key_array[] = $key;
    }

    $key_count = count($key_array);

    foreach ($array[$key_array[0]] as $i => $val) {

        $temp_array = [];

        for ($j = 0; $j < $key_count; $j++) {

            $key              = $key_array[$j];
            $temp_array[$key] = $array[$key][$i];
        }

        $new_array[] = $temp_array;
    }

    return $new_array;
}


/**
 * 关联数组转索引数组
 */
function relevance_arr_to_index_arr($array)
{


    $new_array = [];

    foreach ($array as $v) {

        $temp_array = [];

        foreach ($v as $vv) {
            $temp_array[] = $vv;
        }

        $new_array[] = $temp_array;
    }

    return $new_array;
}

function DB($name, $alias = '')
{
    if ($alias) {
        return database::getInstance()->table($name, $alias);
    } else {
        return database::getInstance()->table($name);
    }


}

function is_mobile()
{

    if (Request::instance()->isMobile()) {
        return true;
    } else {
        return false;
    }

}

/**
 * 钩子
 */
function hook($tag = '', $params = [])
{

    Hook::listen($tag, $params);
}

/**
 * 获取插件类的类名
 *
 * @param strng $name 插件名
 */
function get_addon_class($name = '')
{

    $lower_name = strtolower($name);
    $name       = ucfirst($name);
    $class      = SYS_ADDON_DIR_NAME . "\\{$lower_name}\\{$name}";

    return $class;
}

/**
 * 获取目录列表
 */
function get_dir($dir_name)
{

    $dir_array = [];

    if (false != ($handle = opendir($dir_name))) {

        $i = 0;

        while (false !== ($file = readdir($handle))) {

            if ($file != "." && $file != ".." && !strpos($file, ".")) {

                $dir_array[$i] = $file;

                $i++;
            }
        }

        closedir($handle);
    }

    return $dir_array;
}

function model($name = '', $layer = 'model')
{
    $class  = ucfirst($name);
    $module = Request::instance()->module();
    $class  = 'app\\' . $module . '\\' . $layer . '\\' . $class;

    if (class_exists($class)) {
        $model = new $class();
    } else {
        $class = str_replace('\\' . $module . '\\', '\\common\\', $class);
        if (class_exists($class)) {
            $model = new $class();
        } else {

        }

    }
    return $model;
}


/**
 * 检测用户是否登录
 *
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_login()
{

    $module = Request::instance()->module();

    $member = session('member_auth');

    if (empty($member)) {

        $uid = DATA_DISABLE;
    } else {
        if ($module == 'admin') {
            $memberinfo = session('member_info');

            if (!$memberinfo['is_inside']) {
                return DATA_DISABLE;
            } else {
                $uid = $member['member_id'];
            }

        } else {
            $uid = session('member_auth_sign') == data_auth_sign($member) ? $member['member_id'] : DATA_DISABLE;
        }

    }

    return $uid;
}

/**
 * 简化ajax封装方法
 * @param        $code
 * @param string $msg
 * @param string $data
 * @param string $url
 * @param int    $wait
 */
function func_result($code, $msg = 'success', $data = '', $url = '', $wait = 2)
{
    $result = [
        'code' => $code,
        'msg'  => $msg,
        'data' => $data,
        'url'  => $url,
        'wait' => $wait,
    ];

    $data = json_encode($result, JSON_UNESCAPED_UNICODE);

    if (!headers_sent()) {
        // 发送状态码
        http_response_code(200);
        header('Content-Type:application/json; charset=utf-8');

    }
    exit($data);
}

/**
 * 检测当前用户是否为管理员
 *
 * @return boolean true-管理员，false-非管理员
 */
function is_administrator($member_id = null)
{

    $return_id = is_null($member_id) ? is_login() : $member_id;

    return $return_id && (intval($return_id) === SYS_ADMINISTRATOR_ID);
}

/**
 * 数据签名认证
 *
 * @param  array $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data)
{

    // 数据类型检测
    if (!is_array($data)) : $data = (array)$data; endif;

    // 排序
    ksort($data);

    // url编码并生成query字符串
    $code = http_build_query($data);

    // 生成签名
    $sign = sha1($code);

    return $sign;
}

/**
 * 分析数组及枚举类型配置值 格式 a:名称1,b:名称2
 *
 * @return array
 */
function parse_config_attr($string)
{

    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));

    if (strpos($string, ':')) {

        $value = [];

        foreach ($array as $val) {

            list($k, $v) = explode(':', $val);

            $value[$k] = $v;
        }

    } else {

        $value = $array;
    }

    return $value;
}

/**
 * 解析数组配置
 */
function parse_config_array($name = '')
{

    return parse_config_attr(webconfig($name));
}

/**
 * 记录行为日志
 */
function action_log($name = '', $describe = '')
{

    $logLogic = get_sington_object('logLogic', LogicLog::class);

    $logLogic->logAdd($name, $describe);
}

/**
 * 获取插件的模型名
 *
 * @param strng $name 插件名
 *                    * @param strng $model 模型名
 */
function get_addon_model($name, $model)
{
    $name  = strtolower($name);
    $model = strtolower($model);
    $class = "addon\\{$name}\model\\{$model}";
    return $class;
}

function getbaseurl()
{
    $baseUrl = str_replace(['/', '\\'], '', dirname($_SERVER['SCRIPT_NAME']));


    $baseUrl = empty($baseUrl) ? '/' : '/' . trim($baseUrl, '/') . '/';

    return $baseUrl;
}

function getweburl()
{

    $scheme = is_ssl() ? 'https://' : 'http://'; //协议类型;
    return $scheme . $_SERVER['HTTP_HOST'] . getbaseurl();

}


function json($arr)
{

    exit(json_encode($arr));

}

/**
 * 发送HTTP请求方法
 *
 * @param  string $url    请求URL
 * @param  array  $params 请求参数
 * @param  string $method 请求方法GET/POST
 * @return array  $data   响应数据
 */
function http_curl($url, $params, $method = 'GET', $header = [], $multi = false)
{


    //$url=$url;
    $opts = [
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER     => $header
    ];
    /* 根据请求类型设置特定参数 */
    switch (strtoupper($method)) {
        case 'GET':
            $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
            break;
        case 'POST':
            //判断是否传输文件
            $params                   = $multi ? $params : http_build_query($params);
            $opts[CURLOPT_URL]        = $url;
            $opts[CURLOPT_POST]       = 1;
            $opts[CURLOPT_POSTFIELDS] = $params;
            break;
        default:
            throw new Exception('不支持的请求方式！');
    }
    /* 初始化并执行curl请求 */
    $ch = curl_init();
    curl_setopt_array($ch, $opts);
    $data  = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    if ($error) throw new Exception('请求发生错误：' . $error);
    return $data;
}

/**
 * 插件显示内容里生成访问插件的url
 *
 * @param string $url   url
 * @param array  $param 参数
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function addons_url($url, $param = [])
{

    $parse_url  = parse_url($url);
    $addons     = $parse_url['scheme'];
    $controller = $parse_url['host'];
    $action     = $parse_url['path'];

    /* 基础参数 */
    $params_array = [
        'addon_name'      => $addons,
        'controller_name' => $controller,
        'action_name'     => substr($action, 1),
    ];

    $params = array_merge($params_array, $param); //添加额外参数

    return es_url('addon/execute', $params);
}


function dump($var, $echo = true, $label = null, $flags = ENT_SUBSTITUTE)
{
    $label = (null === $label) ? '' : rtrim($label) . ':';
    ob_start();
    var_dump($var);
    $output = ob_get_clean();
    $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
    if (IS_CLI) {
        $output = PHP_EOL . $label . $output . PHP_EOL;
    } else {
        if (!extension_loaded('xdebug')) {
            $output = htmlspecialchars($output, $flags);
        }
        $output = '<pre>' . $label . $output . '</pre>';
    }
    if ($echo) {
        echo($output);
        return;
    } else {
        return $output;
    }

}

/**
 * 获取目录下所有文件
 */
function file_list($path = '')
{

    $file = scandir($path);

    foreach ($file as $k => $v) {

        if (is_dir($path . SYS_DSS . $v)) : unset($file[$k]); endif;
    }

    return array_values($file);
}

/**
 * 页面数组转换后的数组转json
 */
function transform_array_to_json($array)
{

    return json_encode(transform_array($array));
}

/**
 * 将二维数组数组按某个键提取出来组成新的索引数组
 */
function array_extract($array = [], $key = 'id')
{

    $count = count($array);

    $new_arr = [];

    for ($i = 0; $i < $count; $i++) {

        if (!empty($array) && !empty($array[$i][$key])) {

            $new_arr[] = $array[$i][$key];
        }
    }

    return $new_arr;
}

/**
 * 根据某个字段获取关联数组
 */
function array_extract_map($array = [], $key = 'id')
{


    $count = count($array);

    $new_arr = [];

    for ($i = 0; $i < $count; $i++) {

        $new_arr[$array[$i][$key]] = $array[$i];
    }

    return $new_arr;
}

function generate_code($uid, $length = 6, $time = 3600)
{
    $min = pow(10, ($length - 1));
    $max = pow(10, $length) - 1;

    $code = rand($min, $max);

    cache('moblecode' . $uid, $code, $time);

    return $code;
}

//用于生成用户密码的随机字符
function generate_password($length = 8)
{
    // 密码字符集，可任意添加你需要的字符
    $chars    = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        // 这里提供两种字符获取方式
        // 第一种是使用 substr 截取$chars中的任意一位字符；
        // 第二种是取字符数组 $chars 的任意元素
        // $password .= substr($chars, mt_rand(0, strlen($chars) – 1), 1);
        $password .= $chars[mt_rand(0, strlen($chars) - 1)];
    }
    return $password;
}

function point_controll($uid, $controllname, $id = 0)
{

    $info = database::getInstance()->table('point_rule')->where(['controller' => $controllname])->getList();
    if ($info) {
        foreach ($info as $k => $v) {

            if ($v['type'] == 1) {
                //只有增加的才有次数的限制
                $where['uid']        = $uid;
                $where['controller'] = $controllname;
                $where['type']       = 1;
                $where['scoretype']  = $v['scoretype'];

                $where['create_time|>'] = time() - 24 * 60 * 60;

                $count = database::getInstance()->table('point_note')->where($where)->count();

            } else {
                $count = 0;
            }
            if ($count < $v['num'] || $v['num'] == 0) {
                point_change($uid, $v['scoretype'], $v['score'], $v['type'], $controllname, $id, 0);
            }

        }


    }

}

function roll_point_controll($uid, $controllname, $id = 0)
{
    $info = database::getInstance()->table('point_rule')->where(['controller' => $controllname])->getList();
    if ($info) {


        foreach ($info as $k => $v) {

            if ($v['type'] == 2) {
                $type = 1;

            } else {
                $type = 2;

            }

            point_change($uid, $v['scoretype'], $v['score'], $type, $controllname, $id, 0);
        }

    }

}

function point_change($uid, $scoretype, $score, $type, $controllname, $id = 0, $infouid = 0)
{

    //无操作名的话是正常扣分和加分，

    if ($type == 1) {
        database::getInstance()->table('user')->setIncOrDec(['id' => $uid], $scoretype, $score);


    } else {
        database::getInstance()->table('user')->setIncOrDec(['id' => $uid], $scoretype, $score, '-');


    }

    $info = DB('user')->where(['id' => $uid])->getRow();

    $map['score|<='] = $info['expoint1'];

    $res = DB('usergrade')->where($map)->order('score desc')->limit(1)->getList();


    if (!empty($res)) {
        if ($res[0]['id'] != $info['grades']) {
            $data['grades'] = $res[0]['id'];
            DB('user')->where(['id' => $uid])->update($data);
        }
    }


    $data['uid']         = $uid;
    $data['itemid']      = $id;
    $data['controller']  = $controllname;
    $data['type']        = $type;
    $data['score']       = $score;
    $data['scoretype']   = $scoretype;
    $data['infouid']     = $infouid;
    $data['create_time'] = time();

    database::getInstance()->table('point_note')->insert($data);

}

/**
 * 写入执行信息记录
 */
function write_exe_log($begin = 'app_begin', $end = 'app_end', $type = 0)
{

    if (empty(webconfig('is_write_exe_log'))) : return false; endif;

    $source_url = empty($_SERVER["HTTP_REFERER"]) ? '未知来源' : $_SERVER["HTTP_REFERER"];

    $exe_log['ip']          = request()->ip();
    $exe_log['exe_url']     = request()->url();
    $exe_log['exe_time']    = number_format((float)debug($begin, $end), 6);
    $exe_log['exe_memory']  = number_format((float)debug($begin, $end, 'm'), 2);
    $exe_log['exe_os']      = get_os();
    $exe_log['source_url']  = $source_url;
    $exe_log['session_id']  = session_id();
    $exe_log['browser']     = browser_info();
    $exe_log['status']      = DATA_NORMAL;
    $exe_log['create_time'] = TIME_NOW;
    $exe_log['update_time'] = TIME_NOW;
    $exe_log['type']        = $type;
    $exe_log['login_id']    = is_login();

    $exe_log_path = "./data/exe_log.php";

    file_exists($exe_log_path) && $now_contents = file_get_contents($exe_log_path);

    $arr = var_export($exe_log, true);

    empty($now_contents) ? $contents = "<?php\nreturn array (" . $arr . ");\n" : $contents = str_replace(');', ',' . $arr . ');', $now_contents);

    file_put_contents($exe_log_path, $contents);
}

function friendlyDate($sTime, $type = 'normal', $alt = 'false')
{
    if (!$sTime)
        return '';
    //sTime=源时间，cTime=当前时间，dTime=时间差
    $cTime = time();
    $dTime = $cTime - $sTime;
    $dDay  = intval(date("z", $cTime)) - intval(date("z", $sTime));
    //$dDay     =   intval($dTime/3600/24);
    $dYear = intval(date("Y", $cTime)) - intval(date("Y", $sTime));
    //normal：n秒前，n分钟前，n小时前，日期
    if ($type == 'normal') {
        if ($dTime < 60) {
            if ($dTime < 10) {
                return '刚刚';    //by yangjs
            } else {
                return intval(floor($dTime / 10) * 10) . "秒前";
            }
        } elseif ($dTime < 3600) {
            return intval($dTime / 60) . "分钟前";
            //今天的数据.年份相同.日期相同.
        } elseif ($dYear == 0 && $dDay == 0) {
            //return intval($dTime/3600)."小时前";
            return '今天' . date('H:i', $sTime);
        } elseif ($dYear == 0) {
            return date("m月d日 H:i", $sTime);
        } else {
            return date("Y-m-d", $sTime);
        }
    } elseif ($type == 'mohu') {
        if ($dTime < 60) {
            return $dTime . "秒前";
        } elseif ($dTime < 3600) {
            return intval($dTime / 60) . "分钟前";
        } elseif ($dTime >= 3600 && $dDay == 0) {
            return intval($dTime / 3600) . "小时前";
        } elseif ($dDay > 0 && $dDay <= 7) {
            return intval($dDay) . "天前";
        } elseif ($dDay > 7 && $dDay <= 30) {
            return intval($dDay / 7) . '周前';
        } elseif ($dDay > 30) {
            return intval($dDay / 30) . '个月前';
        }
        //full: Y-m-d , H:i:s
    } elseif ($type == 'full') {
        return date("Y-m-d , H:i:s", $sTime);
    } elseif ($type == 'ymd') {
        return date("Y-m-d", $sTime);
    } else {
        if ($dTime < 60) {
            return $dTime . "秒前";
        } elseif ($dTime < 3600) {
            return intval($dTime / 60) . "分钟前";
        } elseif ($dTime >= 3600 && $dDay == 0) {
            return intval($dTime / 3600) . "小时前";
        } elseif ($dYear == 0) {
            return date("Y-m-d H:i:s", $sTime);
        } else {
            return date("Y-m-d H:i:s", $sTime);
        }
    }
}

/**
 * 获得操作系统
 */
function get_os()
{
    if (!empty($_SERVER['HTTP_USER_AGENT'])) {
        $os = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/win/i', $os)) {
            $os = 'Windows';
        } else if (preg_match('/mac/i', $os)) {
            $os = 'MAC';
        } else if (preg_match('/linux/i', $os)) {
            $os = 'Linux';
        } else if (preg_match('/unix/i', $os)) {
            $os = 'Unix';
        } else if (preg_match('/bsd/i', $os)) {
            $os = 'BSD';
        } else {
            $os = 'Other';
        }
        return $os;
    } else {
        return 'unknow';
    }
}

/**
 * 获得浏览器
 */
function browser_info()
{
    if (!empty($_SERVER['HTTP_USER_AGENT'])) {
        $br = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/MSIE/i', $br)) {
            $br = 'MSIE';
        } else if (preg_match('/Firefox/i', $br)) {
            $br = 'Firefox';
        } else if (preg_match('/Chrome/i', $br)) {
            $br = 'Chrome';
        } else if (preg_match('/Safari/i', $br)) {
            $br = 'Safari';
        } else if (preg_match('/Opera/i', $br)) {
            $br = 'Opera';
        } else {
            $br = 'Other';
        }
        return $br;
    } else {
        return 'unknow';
    }
}

/**
 * 解密函数
 *
 * @param string $txt 需要解密的字符串
 * @param string $key 密匙
 * @return string 字符串类型的返回结果
 */
function decrypt($txt, $key = '', $ttl = 0)
{
    if (empty($txt)) return $txt;
    if (empty($key)) {
        $salt = database::getInstance()->table('user')->where(['id' => 1])->value('salt');
        $key  = md5($salt);
    }
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.";
    $ikey  = "-x6g6ZWm2G9g_vr0Bo.pOq3kRIxsZ6rm";
    $knum  = 0;
    $i     = 0;
    $tlen  = @strlen($txt);
    while (isset($key{$i})) $knum += ord($key{$i++});
    $ch1   = @$txt{$knum % $tlen};
    $nh1   = strpos($chars, $ch1);
    $txt   = @substr_replace($txt, '', $knum % $tlen--, 1);
    $ch2   = @$txt{$nh1 % $tlen};
    $nh2   = @strpos($chars, $ch2);
    $txt   = @substr_replace($txt, '', $nh1 % $tlen--, 1);
    $ch3   = @$txt{$nh2 % $tlen};
    $nh3   = @strpos($chars, $ch3);
    $txt   = @substr_replace($txt, '', $nh2 % $tlen--, 1);
    $nhnum = $nh1 + $nh2 + $nh3;
    $mdKey = substr(md5(md5(md5($key . $ch1) . $ch2 . $ikey) . $ch3), $nhnum % 8, $knum % 8 + 16);
    $tmp   = '';
    $j     = 0;
    $k     = 0;
    $tlen  = @strlen($txt);
    $klen  = @strlen($mdKey);
    for ($i = 0; $i < $tlen; $i++) {
        $k = $k == $klen ? 0 : $k;
        $j = strpos($chars, $txt{$i}) - $nhnum - ord($mdKey{$k++});
        while ($j < 0) $j += 64;
        $tmp .= $chars{$j};
    }
    $tmp = str_replace(['-', '_', '.'], ['+', '/', '='], $tmp);
    $tmp = trim(base64_decode($tmp));
    if (preg_match("/\d{10}_/s", substr($tmp, 0, 11))) {
        if ($ttl > 0 && (time() - substr($tmp, 0, 11) > $ttl)) {
            $tmp = null;
        } else {
            $tmp = substr($tmp, 11);
        }
    }
    return $tmp;
}

/**
 * 加密函数
 *
 * @param string $txt 需要加密的字符串
 * @param string $key 密钥
 * @return string 返回加密结果
 */
function encrypt($txt, $key = '')
{
    if (empty($txt)) return $txt;

    if (empty($key)) {
        $salt = database::getInstance()->table('user')->where(['id' => 1])->value('salt');


        $key = md5($salt);
    }
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.";
    $ikey  = "-x6g6ZWm2G9g_vr0Bo.pOq3kRIxsZ6rm";
    $nh1   = rand(0, 64);
    $nh2   = rand(0, 64);
    $nh3   = rand(0, 64);
    $ch1   = $chars{$nh1};
    $ch2   = $chars{$nh2};
    $ch3   = $chars{$nh3};
    $nhnum = $nh1 + $nh2 + $nh3;
    $knum  = 0;
    $i     = 0;
    while (isset($key{$i})) $knum += ord($key{$i++});
    $mdKey = substr(md5(md5(md5($key . $ch1) . $ch2 . $ikey) . $ch3), $nhnum % 8, $knum % 8 + 16);
    $txt   = base64_encode(time() . '_' . $txt);
    $txt   = str_replace(['+', '/', '='], ['-', '_', '.'], $txt);
    $tmp   = '';
    $j     = 0;
    $k     = 0;
    $tlen  = strlen($txt);
    $klen  = strlen($mdKey);
    for ($i = 0; $i < $tlen; $i++) {
        $k   = $k == $klen ? 0 : $k;
        $j   = ($nhnum + strpos($chars, $txt{$i}) + ord($mdKey{$k++})) % 64;
        $tmp .= $chars{$j};
    }
    $tmplen = strlen($tmp);
    $tmp    = substr_replace($tmp, $ch3, $nh2 % ++$tmplen, 0);
    $tmp    = substr_replace($tmp, $ch2, $nh1 % ++$tmplen, 0);
    $tmp    = substr_replace($tmp, $ch1, $knum % ++$tmplen, 0);
    return $tmp;
}

function systemSetKey($user = '')
{

    if (is_array($user) && !empty($user)) {

        cookie('sys_key', encrypt(serialize($user)), 3600);

    }
}

function format_bytes($size, $delimiter = '')
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    for ($i = 0; $size >= 1024 && $i < 6; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * xrmdir() 强制删除目录，无论目录是否为空
 *
 * @param  string $dir 目录名称
 * @return bool
 */
function xrmdir($dir)
{
    if (!is_dir($dir)) return false;
    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        //删除目录下的文件，如果是文件夹，则递归地删除
        $bool = is_dir("$dir/$file") ? xrmdir("$dir/$file") : unlink("$dir/$file");
        if (!$bool) return false;
    }
    return rmdir($dir);
}

/**
 * 自动创建url
 *
 * @param string $url
 * @param string $vars
 * @param string $suffix
 * @param string $domain
 * @return string
 */
function es_url($url = '', $vars = '', $suffix = true)
{


    $module     = Request::instance()->module();
    $controller = Request::instance()->controller();

    $OPEN_ROUTER = webconfig('OPEN_ROUTER');
    $urlarr      = explode('/', $url);

    $arrcount = count($urlarr);
    $conarr   = [];

    switch ($arrcount) {

        case 1:
            $conarr = ['m' => $module, 'c' => $controller, 'a' => $urlarr[0]];

            break;
        case 2:
            $conarr = ['m' => $module, 'c' => $urlarr[0], 'a' => $urlarr[1]];
            break;
        case 3:
            $conarr = ['m' => $urlarr[0], 'c' => $urlarr[1], 'a' => $urlarr[2]];
            break;
        default:
            break;


    }

    $depr = config('config.pathinfo_depr');
    if ($OPEN_ROUTER == 1) {
        if ($module == 'admin') {
            $url = '/' . $conarr['c'] . '/' . $conarr['a'];
        } else {
            $url = $conarr['c'] . '/' . $conarr['a'];
        }

        $url = str_replace('/', $depr, $url);


    } else {
        $url = '?c=' . $conarr['c'] . '&a=' . $conarr['a'];


    }
// 解析参数
    if (is_string($vars)) {
        // aaa=1&bbb=2 转换成数组
        parse_str($vars, $vars);
    }
// URL后缀
    $suffix = in_array($url, ['/', '']) ? '' : parseSuffix($suffix);


    // 参数组装
    if (!empty($vars)) {
        // 添加参数
        if ($OPEN_ROUTER == 0) {

            $vars = urldecode(http_build_query($vars));

            $url .= '&' . $vars;


        } else {

            $paramType = config('config.url_param_type');
            foreach ($vars as $var => $val) {
                if ('' !== trim($val)) {
                    if ($paramType) {
                        $url .= $depr . urlencode($val);
                    } else {
                        $url .= $depr . $var . $depr . urlencode($val);
                    }
                }
            }
            $url .= $suffix;
        }
    } else {
        if ($OPEN_ROUTER == 1) {
            $url .= $suffix;
        }
    }

    if ($module == 'admin') {
        $modulephp = 'admin.php';
    } else {

        if ($OPEN_ROUTER == 1) {

        } else {
            $modulephp = 'index.php';
        }


    }
    // URL组装
    //$url = rtrim(Request::instance()->root(), '/') .  $url;

    $url = getbaseurl() . $modulephp . $url;
    //$url = $modulephp.$url;
    return $url;


}

// 解析URL后缀
function parseSuffix($suffix)
{
    if ($suffix) {
        $suffix = true === $suffix ? config('config.url_html_suffix') : $suffix;
        if ($pos = strpos($suffix, '|')) {
            $suffix = substr($suffix, 0, $pos);
        }
    }
    return (empty($suffix) || 0 === strpos($suffix, '.')) ? $suffix : '.' . $suffix;
}

/**
 * camelcase2underline() 将使用驼峰法命名的字符串转为下划线命名
 *
 * @param  string $str 驼峰命名字符串
 * @return string      下划线命名字符串
 */
function camelcase2underline($str)
{
    return strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', '_', $str));
}

/**
 * 把返回的数据集转换成Tree
 *
 * @param array  $list  要转换的数据集
 * @param string $pid   parent标记字段
 * @param string $level level标记字段
 * @return array
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
{

    // 创建Tree
    $tree = [];

    if (!is_array($list)):
        return false;
    endif;

    // 创建基于主键的数组引用
    $refer = [];

    foreach ($list as $key => $data) {

        $refer[$data[$pk]] =& $list[$key];
    }

    foreach ($list as $key => $data) {

        // 判断是否存在parent
        $parentId = $data[$pid];

        if ($root == $parentId) {

            $tree[] =& $list[$key];

        } else if (isset($refer[$parentId])) {

            is_object($refer[$parentId]) && $refer[$parentId] = $refer[$parentId]->toArray();

            $parent =& $refer[$parentId];

            $parent[$child][] =& $list[$key];
        }
    }

    return $tree;
}

/**
 * 获取验证码
 */
function getcode($id = '')
{

    $_vc = new ValidateCode();
    $_vc->doimg();
    $_vc->getCode($id);//验证码保存到SESSION中


}

/**
 * 验证验证码是否正确
 */
function captcha_check($value, $id = "")
{
    $captcha = new ValidateCode();
    return $captcha->check($value, $id);
}


// 解密user_token
function decoded_user_token($token = '')
{

    $decoded = JWT::decode($token, API_KEY . JWT_KEY, ['HS256']);

    return (array)$decoded;
}

// 获取解密信息中的data
function get_member_by_token($token = '')
{

    $result = decoded_user_token($token);

    return $result['data'];
}

/**
 * 获取单例对象
 */
function get_sington_object($object_name = '', $class = null, $name = '')
{


    $request = Request::instance();

    if (!$request->__isset($object_name . $name)) {


        $request->bind($object_name . $name, new $class($name));


    }

    return $request->__get($object_name . $name);
}

/**
 * config() 读取配置
 *
 * @param  string $key [可选]配置名
 */
function config($key = '', $value = '')
{


    static $config = [];
    if (!strpos($key, '.')) {

        if ($key && is_string($key)) {

            $name = strtolower($key);

        } else {

            $name = 'config';
        }


    } else {
        // 二维数组设置和获取支持
        $key   = explode('.', $key, 2);
        $name  = strtolower($key[0]);
        $value = $key[1];

    }

    $config = load_config_file($name . '.php');
    if ($value && is_string($value)) {

        return $config[$value];
    } else {

        return $config;
    }

}

/**
 * load_config_file() 载入 配置目录中的配置文件
 *
 * @param  string $file 文件名
 * @return array        配置数组
 */
function load_config_file($file)
{

    $config = [];

    if (file_exists(__ROOT__ . 'data/config/' . $file)) {
        $config = @load_config(__ROOT__ . 'data/config/' . $file) ?: [];
    }


    if ($file == 'config.php') {

        $config = array_xmerge(load_config(__CORE__ . $file) ?: [], $config);
    }

    return $config;
}

/**
 * load_config() 加载配置
 *
 * @param  string $file 配置文件名，支持 php, ini, json 和 xml
 * @return array        配置构成的多维数组，加载失败则返回 false
 */
function load_config($file)
{
    $ext = extname($file);

    if ($ext == 'ini') //载入 ini
        return parse_ini_file($file) ?: false;
    elseif ($ext == 'json') //载入 json
        return json_decode(file_get_contents($file)) ?: false;
    elseif ($ext == 'xml') //载入 XML
        return xml2array(file_get_contents($file)) ?: false;
    else //载入 php
        return include ($file) ?: false;
}

/**
 * webconfig() 读取后台配置
 *
 * @param  string $file 文件名
 * @return array        配置数组
 */
function webconfig($name)
{

    $cache = cache('webconfig');

    if (!empty($cache)) {
        if (isset($cache [$name] ['value'])) {
            return $cache [$name] ['value'];
        }

    } else {

        $db = database::getInstance();

        $data = $db->table('config')->getList();


        foreach ($data as $key => $vo) {

            $n [$vo ['name']] = $vo;
        }

        cache('webconfig', $n);

        return $n [$name] ['value'];
    }
}


/**
 * extname() 获取一个文件的扩展名(始终小写)
 *
 * @param  string $filename 指定文件名
 * @return string           文件扩展名
 */
function extname($filename)
{
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * array_xmerge() 递归、深层增量地合并数组
 *
 * @param  array $array 待合并的数组
 * @return array        合并后的数组
 */
function array_xmerge(array $array)
{
    switch (func_num_args()) {
        case 1:
            return $array;
            break;
        case 2:
            $args    = func_get_args();
            $args[2] = [];
            if (is_array($args[0]) && is_array($args[1])) {
                foreach (array_unique(array_merge(array_keys($args[0]), array_keys($args[1]))) as $k) {
                    if (isset($args[0][$k]) && isset($args[1][$k]) && is_array($args[0][$k]) && is_array($args[1][$k]))
                        $args[2][$k] = array_xmerge($args[0][$k], $args[1][$k]);
                    elseif (isset($args[0][$k]) && isset($args[1][$k]))
                        $args[2][$k] = $args[1][$k];
                    elseif (isset($args[0][$k]) || !isset($args[1][$k]))
                        $args[2][$k] = $args[0][$k];
                    elseif (!isset($args[0][$k]) || isset($args[1][$k]))
                        $args[2][$k] = $args[1][$k];
                }
                return $args[2];
            } else {
                return $args[1];
                break;
            }
        default:
            $args    = func_get_args();
            $args[1] = array_xmerge($args[0], $args[1]);
            array_shift($args);
            return call_user_func_array('array_xmerge', $args); //递归并将 $args 作为多个参数转入
            break;
    }
}

/**
 * 数组 转 对象
 *
 * @param array $arr 数组
 * @return object
 */
function array_to_object($arr)
{
    if (gettype($arr) != 'array') {
        return;
    }
    foreach ($arr as $k => $v) {
        if (gettype($v) == 'array' || getType($v) == 'object') {
            $arr[$k] = (object)array_to_object($v);
        }
    }

    return (object)$arr;
}

/**
 * 对象 转 数组
 *
 * @param object $obj 对象
 * @return array
 */
function object_to_array($obj)
{
    $obj = (array)$obj;
    foreach ($obj as $k => $v) {
        if (gettype($v) == 'resource') {
            return;
        }
        if (gettype($v) == 'object' || gettype($v) == 'array') {
            $obj[$k] = (array)object_to_array($v);
        }
    }

    return $obj;
}

/** detect_site_url() 检测网站根目录地址 */
function detect_site_url($header = '', $host = '')
{
    static $siteUrl = ''; //将网站地址保存到内存中
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
    if ($siteUrl && !$header) return $siteUrl;
    if ($header) { //非客户端请求，如 Socket 服务器中使用
        /** @var closure 获取 Document Root */
        $getDocRoot = function ($path) use (&$getDocRoot) {
            if (!$path || $path == '/') return __ROOT__;
            $i = strrpos(__ROOT__, $path);
            if ($i !== false && $i == (strlen(__ROOT__) - strlen($path))) {
                return substr(__ROOT__, 0, $i);
            } else {
                $path = rtrim($path, '/');
                $i    = strrpos($path, '/');
                $path = substr($path, 0, $i ? $i + 1 : 0);
                return $getDocRoot($path); //递归获取
            }
        };
        $header     = explode(' ', $header); //$header 即 HTTP 请求头中的第一行
        $path       = strstr($header[1], '?', true) ?: $header[1];
        $path       = substr($path, 1, strrpos($path, '/') + 1);
        $docRoot    = $getDocRoot($path);
        $scheme     = is_ssl() ? 'https' : 'http'; //协议类型
    } else { //客户端请求
        $docRoot = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'])); //获取 Document Root
        if ($docRoot) $docRoot = $docRoot . '/';
        if (!path_starts_with(__ROOT__, $docRoot)) { //ModPHP 运行在符号链接目录中
            $scriptFile = str_replace('\\', '/', realpath($_SERVER['SCRIPT_FILENAME']));
            $i          = strrpos($scriptFile, $script);
            $docRoot    = substr($scriptFile, 0, $i + 1);
        }
        extract(parse_url(url()));
    }
    if (path_starts_with(__ROOT__, $docRoot)) {
        $sitePath = substr(__ROOT__, strlen($docRoot)); //网站目录
    } else {
        $sitePath = substr($script, 1, strrpos($script, '/') + 1);
    }
    return isset($scheme) ? $siteUrl = $scheme . '://' . $host . (!empty($port) ? ':' . $port : '') . '/' . $sitePath : '';
}

/**
 * url() 获取当前 URL 地址(不包括 # 及后面的的内容)
 *
 * @return string URL 地址
 */
function url($url = '', $vars = '', $suffix = true)
{
    if ($url != '') {
        return es_url($url, $vars, $suffix);
    } else {
        if (!is_agent()) return false;
        if (is_proxy_server()) return $_SERVER['REQUEST_URI']; //代理地址
        $protocol = strstr(strtolower($_SERVER['SERVER_PROTOCOL']), '/', true);
        $protocol .= is_ssl() ? 's' : ''; //SSL 使用 https
        return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }


}

/**
 * is_proxy_server() 判断应用程序是否运行为代理服务器
 *
 * @return boolean
 */
function is_proxy_server()
{
    return !empty($_SERVER['HTTP_PROXY_CONNECTION']) || (!empty($_SERVER['REQUEST_URI']) && (stripos($_SERVER['REQUEST_URI'], 'http://') === 0 || stripos($_SERVER['REQUEST_URI'], 'https://') === 0));
}

/**
 * is_ssl() 判断当前请求是否使用 SSL 协议
 *
 * @return boolean
 */
function is_ssl()
{
    return isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on';
}

/**
 * is_agent() 判断当前是否为客户端请求
 *
 * @param  mixed $agent [可选]客户端类型，或者设置为 true 判断是否有 User-Agent 请求头
 * @return boolean
 */
function is_agent($agent = '')
{
    if (PHP_SAPI == 'cli') return false;
    $hasAgent = !empty($_SERVER['HTTP_USER_AGENT']);
    if ($agent === true || $agent === 1) return $hasAgent;
    return $agent ? $hasAgent && stripos($_SERVER['HTTP_USER_AGENT'], $agent) !== false : true;
}

/**
 * path_starts_with() 判断一个路径是否以指定的字符串开头，在 Windows 中不区分大小写
 *
 * @param  string $path 待检测的路径
 * @param  string $find 查找的字符串
 * @return boolean
 */
function path_starts_with($path, $find)
{
    return PHP_OS == 'WINNT' ? stripos($path, $find) === 0 : strpos($path, $find) === 0;
}

/**
 * set_content_type() 设置文档类型和编码
 *
 * @param string $type     文档类型
 * @param string $encoding [可选]编码，默认 UTF-8
 */
function set_content_type($type, $encoding = 'UTF-8')
{
    if (!headers_sent()) {
        header("Content-Type: $type; charset=$encoding"); //在响应头中设置
    } else {
        echo "<meta http-equiv=\"content-type\" content=\"$type; charset=$encoding\">\n"; //在元信息中设置
    }
}

if (!function_exists('cache')) {
    /**
     * 缓存管理
     *
     * @param mixed  $name    缓存名称，如果为数组表示进行缓存设置
     * @param mixed  $value   缓存值
     * @param mixed  $options 缓存参数
     * @param string $tag     缓存标签
     * @return mixed
     */
    function cache($name, $value = '', $options = null, $tag = null)
    {

        $cache = new Cache();

        if ('' === $value) {
            // 获取缓存

            return $cache->get($name);
        } elseif (is_null($value)) {
            // 删除缓存
            return $cache->clear($name);
        } else {
            // 缓存数据
            return $cache->set($name, $value);

        }
    }
}
if (!function_exists('session')) {
    /**
     * Session管理
     *
     * @param string|array $name   session名称，如果为数组表示进行session设置
     * @param mixed        $value  session值
     * @param string       $prefix 前缀
     * @return mixed
     */
    function session($name, $value = '', $prefix = null)
    {
        if (is_array($name)) {
            // 初始化
            Session::init($name);
        } elseif (is_null($name)) {
            // 清除
            Session::clear('' === $value ? null : $value);
        } elseif ('' === $value) {
            // 判断或获取
            return 0 === strpos($name, '?') ? Session::has(substr($name, 1), $prefix) : Session::get($name, $prefix);
        } elseif (is_null($value)) {
            // 删除
            return Session::delete($name, $prefix);
        } else {
            // 设置
            return Session::set($name, $value, $prefix);
        }
    }
}

if (!function_exists('cookie')) {
    /**
     * Cookie管理
     *
     * @param string|array $name   cookie名称，如果为数组表示进行cookie设置
     * @param mixed        $value  cookie值
     * @param mixed        $option 参数
     * @return mixed
     */
    function cookie($name, $value = '', $option = null)
    {
        if (is_array($name)) {
            // 初始化
            Cookie::init($name);
        } elseif (is_null($name)) {
            // 清除
            Cookie::clear($value);
        } elseif ('' === $value) {
            // 获取
            return 0 === strpos($name, '?') ? Cookie::has(substr($name, 1), $option) : Cookie::get($name, $option);
        } elseif (is_null($value)) {
            // 删除
            return Cookie::delete($name);
        } else {
            // 设置
            return Cookie::set($name, $value, $option);
        }
    }
}
if (!function_exists('validate')) {
    /**
     * 实例化验证器
     *
     * @param string $name         验证器名称
     * @param string $layer        业务层名称
     * @param bool   $appendSuffix 是否添加类名后缀
     */
    function validate($name = '', $layer = 'validate', $appendSuffix = false)
    {
        return Loader::validate($name, $layer, $appendSuffix);
    }
}
if (!function_exists('request')) {
    /**
     * 获取当前Request对象实例
     *
     * @return Request
     */
    function request()
    {
        return Request::instance();
    }
}
if (!function_exists('debug')) {
    /**
     * 记录时间（微秒）和内存使用情况
     *
     * @param string         $start 开始标签
     * @param string         $end   结束标签
     * @param integer|string $dec   小数位 如果是m 表示统计内存占用
     * @return mixed
     */
    function debug($start, $end = '', $dec = 6)
    {
        if ('' == $end) {
            Debug::remark($start);
        } else {
            return 'm' == $dec ? Debug::getRangeMem($start, $end) : Debug::getRangeTime($start, $end, $dec);
        }
    }
}
if (!function_exists('input')) {
    /**
     * 获取输入数据 支持默认值和过滤
     *
     * @param string $key     获取的变量名
     * @param mixed  $default 默认值
     * @param string $filter  过滤方法
     * @return mixed
     */
    function input($key = '', $default = null, $filter = '')
    {
        if (0 === strpos($key, '?')) {
            $key = substr($key, 1);
            $has = true;
        }
        if ($pos = strpos($key, '.')) {
            // 指定参数来源
            list($method, $key) = explode('.', $key, 2);
            if (!in_array($method, ['get', 'post', 'put', 'patch', 'delete', 'param', 'request', 'session', 'cookie', 'server', 'env', 'path', 'file'])) {
                $key    = $method . '.' . $key;
                $method = 'param';
            }
        } else {
            // 默认为自动判断
            $method = 'param';
        }
        if (isset($has)) {
            return request()->has($key, $method, $default);
        } else {
            return request()->$method($key, $default, $filter);
        }
    }
}