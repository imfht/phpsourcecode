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
use PHPMailer\PHPMailer\PHPMailer;

function get_client_ip_address($ip=''){
    $getlink = curl_init();
    $ip = $ip?$ip:request()->ip();
    curl_setopt($getlink, CURLOPT_URL, "http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=".$ip);
    curl_setopt($getlink, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($getlink, CURLOPT_HEADER, 0);
    $ip_str = curl_exec($getlink);
    curl_close($getlink);
    return json_decode($ip_str,true);
}
function timestamp()
{
    list($t1, $t2) = explode(' ', microtime());
    return (float)sprintf('%.0f', (floatval($t1)+floatval($t2))*1000);
}

function convertUnderline($str)
{
    $str = preg_replace_callback('/([-_]+([a-z]{1}))/i',function($matches){
        return strtoupper($matches[2]);
    },$str);
    return $str;
}

/**
 * 获取缩略image
 * @param $image
 * @return string
 */
function get_m_image($image=''){
    $path = pathinfo($image);
    $_path = isset($path["dirname"])?$path["dirname"]."/m_".$path["basename"]:"";
    return $_path;
}

/**
 * 去除转义字符
 * @param $array
 * @return mixed
 */
function strips_lashes_array(&$array)
{
    while (list($key, $var) = each($array)) {
        if ($key != 'argc' && $key != 'argv' && (strtoupper($key) != $key || ''.intval($key) == "$key")) {
            if (is_string($var)) {
                $array[$key] = stripslashes($var);
            }
            if (is_array($var)) {
                $array[$key] = strips_lashes_array($var);
            }
        }
    }
    return $array;
}

/**
 * 驼峰转下换线
 * @param $str
 * @return null|string|string[]
 */
function humpToLine($str){
    $str = preg_replace_callback('/([A-Z]{1})/',function($matches){
        return '_'.strtolower($matches[0]);
    },$str);
    return $str;
}

/**
 * 判断是否存在汉字
 * @param $str
 * @return bool
 */
function has_chiness($str)
{
    if (preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $str)>0) {
        $flag= true;
    }else {
        $flag =false;
    }
    return $flag;
}
/**
 * 计算时间差
 * @param $start
 * @param $end
 * @return array
 */
function time_diff($start, $end=0)
{
    $end = $end?$end:time();
    $cha = $end -$start;

    $minute=floor($cha/60);
    $hour=floor($cha/60/60);
    $day=floor($cha/60/60/24);
    return [
        'min'=>$minute,
        'hour'=>$hour,
        'day'=>$day
    ];
}

/**
 * 加解密
 * @param $string
 * @param string $operation
 * @param string $key
 * @param int $expiry
 * @return bool|string
 * @example:
 *   $str= '1234';
 *   $auth =  auth_code($str,'ENCODE'); //加密
 *   $str = auth_code($auth,'DECODE'); //解密
 *   p($auth);
 */
function auth_code($string, $operation = 'DECODE', $key = '', $expiry = 0, $auth_key='jswei30')
{
    // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
    $ckey_length = 4;

    // 密匙
    $key = md5($key ? $key : $auth_key);

    // 密匙a会参与加解密
    $keya = md5(substr($key, 0, 16));
    // 密匙b会用来做数据完整性验证
    $keyb = md5(substr($key, 16, 16));
    // 密匙c用于变化生成的密文
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length):
        substr(md5(microtime()), -$ckey_length)) : '';
    // 参与运算的密匙
    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);
    // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，
    //解密时会通过这个密匙验证数据完整性
    // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) :
        sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    // 产生密匙簿
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    // 核心加解密部分
    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        // 从密匙簿得出密匙进行异或，再转成字符
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if ($operation == 'DECODE') {
        // 验证数据有效性，请看未加密明文的格式
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) &&
            substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
        // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
        return $keyc.str_replace('=', '', base64_encode($result));
    }
}

/**
 * @author 魏巍
 * @description 获取当前时间的本周的开始结束时间
 */
function get_first_last_week_day()
{
    //当前日期
    $sdefaultDate = date("Y-m-d");
    //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
    $first=1;
    //获取当前周的第几天 周日是 0 周一到周六是 1 - 6
    $w=date('w', strtotime($sdefaultDate));
    //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
    $week_start=date('Y-m-d', strtotime("$sdefaultDate -".($w ? $w - $first : 6).' days'));
    //本周结束日期
    $week_end=date('Y-m-d', strtotime("$week_start +6 days"));

    return [
        'first'=>$week_start,
        'last'=>$week_end
    ];
}
/**
 * 去除标点符号
 * @param $text
 * @return string
 */
function filter_mark($text)
{
    if (trim($text)=='') {
        return '';
    }
    $text=preg_replace("/[[:punct:]\s]/", ' ', $text);
    $text=urlencode($text);
    $text=preg_replace("/(%7E|%60|%21|%40|%23|%24|%25|%5E|%26|%27|%2A|%28|%29|%2B|%7C|%5C|%3D|\-|_|%5B|%5D|%7D|%7B|%3B|%22|%3A|%3F|%3E|%3C|%2C|\.|%2F|%A3%BF|%A1%B7|%A1%B6|%A1%A2|%A1%A3|%A3%AC|%7D|%A1%B0|%A3%BA|%A3%BB|%A1%AE|%A1%AF|%A1%B1|%A3%FC|%A3%BD|%A1%AA|%A3%A9|%A3%A8|%A1%AD|%A3%A4|%A1%A4|%A3%A1|%E3%80%82|%EF%BC%81|%EF%BC%8C|%EF%BC%9B|%EF%BC%9F|%EF%BC%9A|%E3%80%81|%E2%80%A6%E2%80%A6|%E2%80%9D|%E2%80%9C|%E2%80%98|%E2%80%99|%EF%BD%9E|%EF%BC%8E|%EF%BC%88)+/", ' ', $text);
    $text=urldecode($text);
    return trim($text);
}
/**
 * 去除空格
 * @param $str
 * @return mixed
 */
function trim_all($str)
{
    $q=array(" ","　","\t","\n","\r");
    $h=array("","","","","");
    return str_replace($q, $h, $str);
}

/**
 * 生成订单号
 * @param string $start
 * @return string
 */
function build_order_no($start=''){
//    return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
//    return $start.date('Ymd').substr(implode('-', array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    $yCode = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
    $orderSn = $start?$start:$yCode[intval(date('Y')) - 2011]
        . strtoupper(dechex(date('m')))
        . date('d') . substr(time(), -5)
        . substr(microtime(), 2, 5)
        . sprintf('%02d', rand(0, 99));
    return $orderSn;
}
/**
 * 不重复随机数
 * @param int $begin
 * @param int $end
 * @param int $limit
 * @return string
 */
function no_random($begin=0, $end=20, $limit=4)
{
    $rand_array=range($begin, $end);
    shuffle($rand_array);//调用现成的数组随机排列函数
    return implode('', array_slice($rand_array, 0, $limit));//截取前$limit个
}

/**
 * 发送短信
 * @param string  $to                       发送人
 * @param string $templateId                短信模板id
 * @param int $t                            用户检测
 * @return array
 */
function send_sms($to, $templateId= "37098", $t=0)
{
    if ($t) {	//检测用户
        $member = db('member')->where(['phone'=>$to])->count();
        if ($member) {
            return ['status'=>0,'msg'=>lang('already',[lang('user')])];
        }
    }
    $options['accountsid']= config('app.Ucpaas.accountSid');
    $options['token']=config('app.Ucpaas.authToken');
    $appId = config('app.Ucpaas.appId');
    $d = no_random(0, 9, 4);
    $ucpass = new \service\Ucpaas($options);
    $param ="云上办公,{$d}";	//参数
    $arr=$ucpass->templateSMS($appId, $to, $templateId, $param);
    if (substr($arr, 21, 6) == 000000) {
        if (cookie('?'.$d.'_session_code')) {
            cookie($d.'_session_code', null, time()-60*2);
        }
        cookie($d.'_session_code', $d, 60*5);
       return ['status'=>1,'message'=>lang('done',[lang('send')])];
    }else{
        return ['status'=>0,'message'=>lang('error',[lang('unsend')])];
    }
}
/**
 * * 系统邮件发送函数
 * @param string $to    接收邮件者邮箱
 * @param string $name  接收邮件者名称
 * @param string $subject 邮件主题
 * @param string $body    邮件内容
 * @param string $attachment 附件列表
 * @return boolean
 */
function think_send_mail($to, $name, $subject = '', $body = '', $attachment = null)
{
    $config = config('app.THINK_EMAIL');
    $mail             = new PHPMailer(); //PHPMailer对象
    $mail->CharSet    = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->IsSMTP();  // 设定使用SMTP服务
    $mail->SMTPDebug  = 0;                     // 关闭SMTP调试功能,1 = errors and messages,2 = messages only
    $mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
    //$mail->SMTPSecure = 'ssl';                 // 使用安全协议
    $mail->Host       = $config['SMTP_HOST'];  // SMTP 服务器
    $mail->Port       = $config['SMTP_PORT'];  // SMTP服务器的端口号
    $mail->Username   = $config['SMTP_USER'];  // SMTP服务器用户名
    $mail->Password   = $config['SMTP_PASS'];  // SMTP服务器密码
    $mail->setFrom($config['FROM_EMAIL'], $config['FROM_NAME']);
    $replyEmail       = $config['REPLY_EMAIL']?$config['REPLY_EMAIL']:$config['FROM_EMAIL'];
    $replyName        = $config['REPLY_NAME']?$config['REPLY_NAME']:$config['FROM_NAME'];
    $mail->AddReplyTo($replyEmail, $replyName);
    $mail->Subject    = $subject;
    $mail->MsgHTML($body);
    $mail->isHTML(true);
    $mail->AddAddress($to, $name);
    if (is_array($attachment)) { // 添加附件
        foreach ($attachment as $file) {
            is_file($file) && $mail->AddAttachment($file);
        }
    }
    return $mail->Send() ? true : $mail->ErrorInfo;
}

/**
 * 不重复随机数
 * @param int $begin
 * @param int $end
 * @param int $limit
 * @return string
 */
function no_repeat_random($begin=0, $end=20, $limit=4)
{
    $rand_array=range($begin, $end);
    shuffle($rand_array);//调用现成的数组随机排列函数
    return implode('', array_slice($rand_array, 0, $limit));//截取前$limit个
}

/**
 * Formats a JSON string for pretty printing
 *
 * @param string $json The JSON to make pretty
 * @param bool $html Insert nonbreaking spaces and <br />s for tabs and linebreaks
 * @return string The prettified output
 */
function _format_json($json, $html = false)
{
    $tabcount = 0;
    $result = '';
    $inquote = false;
    $ignorenext = false;
    if ($html) {
        $tab = "   ";
        $newline = "<br/>";
    } else {
        $tab = "\t";
        $newline = "\n";
    }
    for ($i = 0; $i < strlen($json); $i++) {
        $char = $json[$i];
        if ($ignorenext) {
            $result .= $char;
            $ignorenext = false;
        } else {
            switch ($char) {
                case '{':
                    $tabcount++;
                    $result .= $char . $newline . str_repeat($tab, $tabcount);
                    break;
                case '}':
                    $tabcount--;
                    $result = trim($result) . $newline . str_repeat($tab, $tabcount) . $char;
                    break;
                case ',':
                    $result .= $char . $newline . str_repeat($tab, $tabcount);
                    break;
                case '"':
                    $inquote = !$inquote;
                    $result .= $char;
                    break;
                case '\\':
                    if ($inquote) {
                        $ignorenext = true;
                    }
                    $result .= $char;
                    break;
                default:
                    $result .= $char;
            }
        }
    }
    return $result;
}


/**
/**
 * 打印函数
 * @param array $array
 */
function p($array){
    dump($array, 1, '<pre>', 0);
}
/**
 * 浏览器友好的变量输出
 * @param mixed $var 变量
 * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
 * @param string $label 标签 默认为空
 * @param boolean $strict 是否严谨 默认为true
 * @return void|string
 */
function dump($var, $echo=true, $label=null, $strict=true){
    $label = ($label === null) ? '' : rtrim($label) . ' ';
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        } else {
            $output = $label . print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    } else {
        return $output;
    }
}

function array_remove_empty($arr){
    $_arr = [];
    foreach($arr as $key => $val) {
        if (is_array($val)){
            $val = array_remove_empty($val);
            if (count($val)!=0){
                $_arr[$key] = $val;
            }
        }else {
            if (trim($val) != ""){
                $_arr[$key] = $val;
            }
        }
    }
    unset($arr);
    return $_arr;
}