<?php
/*
 * -------------------------
 * str
 * -------------------------
*/
//截断字符串
function str_cut($t0, $t1, $t2 = '...') {
  $str_ext = mb_strlen($t0, 'utf8') > $t1 ? $t2 : '';
  $tmp_str = mb_substr($t0, 0, $t1, 'utf8');
  return $tmp_str . $str_ext;
}
/**
 * 按符号截取字符串的指定部分
 * @param string $str 需要截取的字符串
 * @param string $sign 需要截取的符号
 * @param int $number 数组索引
 * @return string 返回截取的内容
 */
function str_part($str, $sign, $number=0) {
  $array = explode($sign, $str);
  $length = count($array);
  if ($number<0) {
    $new_array = array_reverse($array);
    $abs_number = abs($number);
    if ($abs_number<=$length) {
      return $new_array[$abs_number-1];
    }
  } else {
    if ($number<$length) {
      return $array[$number];
    }
  }
  return 'error';
}
//返回可安全执行的SQL,带html格式
function str_isafe($str) {
  $tmp = array('SELECT ', 'insert ', 'update ', 'delete ', ' and', 'drop table', 'script', '*', '%', 'eval');
  $tmp_re = array('sel&#101;ct ', 'ins&#101;rt ', 'up&#100;ate ', 'del&#101;te ', ' an&#100;', 'dro&#112; table', '&#115;cript', '&#42;', '&#37;', '$#101;val');
  return str_replace($tmp, $tmp_re, trim($str));
}
//返回可安全执行的SQL,不带html格式
function str_safe($str) {
  return str_isafe(htmlspecialchars($str));
}
//返回无空格,tab,html的字串
function str_text($str, $ext = 0) {
  if ($ext == 1) {
    return str_replace(array("\r\n", "\r", "\n", " ", "　", chr(34), chr(13), " ", "&nbsp;"), '', strip_tags(str_isafe($str)));
  } elseif ($ext == 2) {
    return str_replace(array(" ","　",chr(34)), '', strip_tags(str_isafe($str)));
  } else {
    return str_replace(array("\r\n", "\r", "\n", " ", "　", chr(34), chr(13), "  "), '', strip_tags(str_isafe($str)));
  }
}
function array_str($arr, $p=',') {
  if (is_array($arr)) {
    return implode($p, $arr)!=',' ? implode($p, $arr) : '';
  } else {
    return $arr;
  }
}
function str_array($str,$p=',') {
  return explode($p, $str);
}
//高亮显示
function high_light($t0, $t1) {
  return str_replace($t1, '<span class="highlight">' . $t1 . '</span>', $t0);
}
// 随机码
function str_rand($len = 6) {
  $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  // characters to build the password from
  mt_srand((double) microtime() * 1000000 * getmypid());
  // seed the random number generater (must be done)
  $tmp_str = '';
  while (strlen($tmp_str) < $len) {
    $tmp_str .= substr($chars, mt_rand() % strlen($chars), 1);
  }
  return $tmp_str;
}
// 32位非0整数
function str_code($len = 32) {
  $chars = '123456789';
  // characters to build the password from
  mt_srand((double) microtime() * 1000000 * getmypid());
  // seed the random number generater (must be done)
  $tmp_str = '';
  while (strlen($tmp_str) < $len) {
    $tmp_str .= substr($chars, mt_rand() % strlen($chars), 1);
  }
  return $tmp_str;
}
// 掩码处理（默认为手机号处理）
// $t1 从第几位开始 $t2 到第几位结束
// $t3 倒数几位
// 如银行卡号：0,4,3 得出6222************847
function get_mask_str($t0, $t1 = 0, $t2 = 3, $t3 = 4) {
  $strlen = strlen($t0);
  $str_mid = '';
  $str_pre = substr($t0, $t1, $t2);
  $str_suf = substr($t0, -$t3);
  for ($i = 0; $i < $strlen - ($t2 + $t3); $i++) {
    $str_mid .= '*';
  }
  return $str_pre . $str_mid . $str_suf;
}
function color_rand() {
  return '#' . dechex(rand(0, 16777215));
}

function alph_num($char) {
  $char = strtolower($char);
  $array = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
  $len = strlen($char);
  $sum = '';
  for ($i = 0; $i<$len; $i++) {
    $index = array_search($char[$i], $array);
    $sum += ($index+1) * pow(26,$len-$i-1);
  }
  return $sum;
}

function num_alph($n) {
  $n++;
  $array=array(1=>'a', 2=>'b', 3=>'c', 4=>'d', 5=>'e', 6=>'f', 7=>'g', 8=>'h', 9=>'i', 10=>'j', 11=>'k', 12=>'l', 13=>'m', 14=>'n', 15=>'o', 16=>'p', 17=>'q', 18=>'r', 19=>'s', 20=>'t', 21=>'u', 22=>'v', 23=>'w', 24=>'x', 25=>'y', 26=>'z');
  if ($n<=26) {
    return strtoupper($array[$n]);
  }else{
    if ($n%26==0) {
      return strtoupper($array[floor(($n-1)/26)] . $array[26]);
    } else {
      return strtoupper($array[floor($n/26)] . $array[$n%26]);
    }
  }
}

function iif($a, $b) {
  return !empty($a) ? $a : $b;
}

/*
 * -------------------------
 * sql
 * -------------------------
*/
//将数组转换成供insert用的字符串
function arr_insert($arr) {
  foreach ($arr as $k => $v) {
    $tmp_key[] = "`" . str_safe($k) . "`";
    $tmp_value[] = "'" . str_isafe($v) . "'";
  }
  return "(".implode(',', $tmp_key).") VALUES (".implode(',', $tmp_value).")";
}
//将数组转换成供update用的字符串
function arr_update($arr) {
  $tmp = '';
  foreach ($arr as $k => $v) {
    $tmp .= "`" . str_safe($k) . "` = '" . str_isafe($v) . "',";
  }
  return rtrim($tmp, ',');
}
function list_detail($c_sub, $limit, $order='') {
  $order = $order ? $order : 'd_order ASC,id DESC';
  return $GLOBALS['db']->getAll("SELECT * FROM detail WHERE d_parent IN ($c_sub) ORDER BY $order LIMIT $limit");
}
function list_channel($c_id, $order='') {
  $order = $order ? $order : 'c_order ASC,id ASC';
  return $GLOBALS['db']->getAll("SELECT * FROM channel WHERE c_parent = $c_id AND c_navigation = 1 ORDER BY $order");
}
function list_slider($s_parent='', $order='') {
  $order = $order ? $order : 's_order ASC,id ASC';
  $s_parent = $s_parent!='' ? $s_parent : 'global';
  return $GLOBALS['db']->getAll("SELECT * FROM slideshow WHERE s_parent='$s_parent' ORDER BY $order");
}
function list_link($limit, $order='') {
  $order = $order ? $order : 'l_order ASC,id ASC';
  return $GLOBALS['db']->getAll("SELECT * FROM link ORDER BY $order LIMIT $limit");
}
function list_search($keyword, $size, $order='') {
  $order = $order ? $order : 'd_order ASC,id DESC';
  $res['pager'] = page_handle('page', $size, $GLOBALS['db']->getOne("SELECT COUNT(id) FROM detail WHERE d_name LIKE '%$keyword%'"));
  $res['list'] = $GLOBALS['db']->getAll("SELECT * FROM detail WHERE d_name LIKE '%$keyword%' ORDER BY $order LIMIT ".$res['pager'][0].','.$res['pager'][1]);
  return $res;
}
function list_tag($keyword, $size, $order='') {
  $order = $order ? $order : 'd_order ASC,id DESC';
  $res['pager'] = page_handle('page', $size, $GLOBALS['db']->getOne("SELECT COUNT(id) FROM detail WHERE d_tag LIKE '%$keyword%'"));
  $res['list'] = $GLOBALS['db']->getAll("SELECT * FROM detail WHERE d_tag LIKE '%$keyword%' ORDER BY $order LIMIT ".$res['pager'][0].','.$res['pager'][1]);
  return $res;
}
// 获取某表的某值
function get_col($table, $col, $where) {
  return $GLOBALS['db']->getOne("SELECT $col FROM $table WHERE $where");
}
// 获取碎片内容
function get_chip($t0) {
  if (is_numeric($t0)) {
    $t0 = intval($t0);
    $res = $GLOBALS['db']->getOne("SELECT c_content FROM chip WHERE id = $t0");
  } else {
    $t0 = str_safe($t0);
    $res = $GLOBALS['db']->getOne("SELECT c_content FROM chip WHERE c_code = '$t0'");
  }
  return $res;
}

/*
 * -------------------------
 * check
 * -------------------------
*/
// 密码hash
function psw_hash($str){
  if (function_exists('password_hash')){
    return password_hash(str_text($str), PASSWORD_DEFAULT);
  } else {
    return md5(str_text($str));
  }
}
// 效验密码hash
function psw_verify($str, $hash){
  if (function_exists('password_hash')){
    return password_verify(str_text($str), $hash);
  } else {
    return psw_hash($str) == $hash;
  }
}
// 验证数据 支持 in between length regex expire
function check($value, $type='', $rule) {
  $type = strtolower(trim($type));
  switch($type) {
    case 'in': // 验证是否在某个指定范围之内 逗号分隔字符串或者数组
    case 'notin':
      $range = explode(',', $rule);
      return $type == 'in' ? in_array($value, $range) : !in_array($value, $range);
    case 'between': // 验证是否在某个范围
    case 'notbetween': // 验证是否不在某个范围
      list($min, $max) = explode(',', $rule);
      return $type == 'between' ? $value>=$min && $value<=$max : $value<$min || $value>$max;
    case 'length': // 验证长度
      $length = mb_strlen($value, 'utf-8'); // 当前数据长度
      if (strpos($rule, ',')) { // 长度区间
        list($min, $max) = explode(',', $rule);
        return $length >= $min && $length <= $max;
      } else {// 指定长度
        return $length == $rule;
      }
    case 'expire':
      list($start, $end) = explode(',', $rule);
      if (!is_numeric($start)) {$start = local_strtotime($start);}
      if (!is_numeric($end)) {$end = local_strtotime($end);}
      return gmtime() >= $start && gmtime() <= $end;
    case 'email':
      return preg_match("/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/i", $value)!==false ? true : false;
    case 'verifycode':
      return strtolower($value) == $_SESSION['verifycode'];
    case 'regex':
    default:    // 默认使用正则验证 可以使用验证类中定义的验证名称
      // 检查附加规则
      return regex($value, $rule);
  }
}
// 使用正则验证(数据, 规则)
function regex($data, $rule) {
  $validate = array(
    'require'   =>  '/\S+/',
    'email'     =>  '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
    'mobile'     =>  '/^1[3456789]{1}\d{9}$/',
    'cnid'     =>  '/(^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$)|(^[1-9]\d{5}\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{2}$)/',
    'url'       =>  '/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/',
    'currency'  =>  '/^\d+(\.\d+)?$/',
    'number'    =>  '/^\d+$/',
    'zip'       =>  '/^\d{6}$/',
    'integer'   =>  '/^[-\+]?\d+$/',
    'double'    =>  '/^[-\+]?\d+(\.\d+)?$/',
    'english'   =>  '/^[A-Za-z]+$/',
  );
  // 检查是否有内置的正则表达式
  if (isset($validate[strtolower($rule)])) {
    $rule = $validate[strtolower($rule)];
  }
  return preg_match($rule, $data)===1;
}
// 令牌效验
function check_token($data) {
  if (TOKEN_ON) {
    $name = TOKEN_NAME;
    $val = $data[$name][md5($_SERVER['HTTP_REFERER'])];
    if (!empty($val)) {
      // 令牌验证
      list($key, $value) = explode('_', $val);
      if ($_SESSION[$name][$key] == $value) { // 防止重复提交
        return true;
      }
    }
    return false;
  }
  return true;
}
// 验证码
function checkGD() {
  $arr = gd_info();
  if (version_compare('2.0.28', $arr['GD Version'], ">") && $arr['PNG Support']) {
    return true;
  } else {
    return false;
  }
}
// 比对数组数据更新
function comVarUpdate($arr_prev, $arr) {
  foreach ($arr_prev as $key=>$val) {
    if (!array_key_exists($key, $arr)) {
      $arr[$key] = $arr_prev[$key];
    }
    if ($arr_prev[$key] != $arr[$key]) {
      $arr_prev[$key] = $arr[$key];
    }
  }
  return $arr_prev;
}
// 判断移动端
function is_mobile() {
  $regExp = '/nokia|iphone|android|samsung|htc|motorola|blackberry|ericsson|huawei|dopod|amoi|gionee|^sie\\-|^bird|^zte\\-|haier|';
  $regExp .= 'blazer|netfront|helio|hosin|novarra|techfaith|palmsource|^mot\\-|softbank|foma|docomo|kddi|up\\.browser|up\\.link|';
  $regExp .= 'symbian|smartphone|midp|wap|phone|windows ce|CoolPad|webos|iemobile|^spice|longcos|pantech|portalmmm|';
  $regExp .= 'alcatel|ktouch|nexian|^sam\\-|s[cg]h|^lge|philips|sagem|wellcom|bunjalloo|maui|';
  $regExp .= 'jig\\s browser|hiptop|ucweb|ucmobile|opera\\s*mobi|opera\\*mini|mqqbrowser|^benq|^lct';
  $regExp .= '480×640|640x480|320x320|240x320|320x240|176x220|220x176/i';
  if (!isset($_SERVER['HTTP_USER_AGENT'])) {
    return true;
  } else {
    return @$_GET['mobile'] || isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE']) || preg_match($regExp, strtolower($_SERVER['HTTP_USER_AGENT']));
  }
}
function is_wechat_qrcode() {
  return (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false);
}
function is_alipay_qrcode() {
	return (strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false);
}
function is_admin() {
  return strpos($_SERVER['PHP_SELF'], ADMIN_DIR);
}
function is_home($p) {
  return @$p == 'index';
}
function is_404($p) {
  return @$p == '404';
}
function is_500($p) {
  return @$p == '505';
}
function is_search($p) {
  return @$p == 'search';
}
function is_channel() {
  return strpos($_SERVER['PHP_SELF'], 'channel');
}
function is_detail() {
  return strpos($_SERVER['PHP_SELF'], 'detail');
}
function check_browser() {
  if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 8.0') || strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 7.0') || strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0')) {
    return false;
  } else {
    return true;
  }
}
function check_domain($str){
  if (strpos($_SERVER['HTTP_HOST'],':')!==false) {
    $arr = explode(':', $_SERVER['HTTP_HOST']);
    $res = $arr[0];
  } else {
    $res = $_SERVER['HTTP_HOST'];
  }
  if (strpos($str, $res)!==false) {
    return true;
  } else {
    return false;
  }
}

/*
 * -------------------------
 * etc
 * -------------------------
*/
// 替换语言文件内的[]参数
// $p 参数名 $v 参数值 $s 替换字串
function trans_p($p, $v, $s, $t='array') {
  if ($t=='array') {
    $res = $s;
    foreach ($p as $key => $val) {
      $res = str_replace($p[$key], $v[$key], $res);
    }
  } else {
    $res = str_replace($p, $v, $s);
  }
  return $res;
}
// 多维数组转单维
// array(array("0"=>值),array("0"=>值)，...)
// array("0"=>值,"1"=>值,...)
function get_easy_array($many_arr, $many_key) {
  $res = array();
  foreach ($many_arr as $val) {
    $res[] = $val[$many_key];
  }
  return $res;
}

// 多数组排列组合
function get_array_pc($arr) {
  if (count($arr) > 1){
    $tmparr = array();
    $arr1 = array_shift($arr);
    $arr2 = array_shift($arr);
    foreach($arr1 as $v1){
      foreach($arr2 as $v2){
        $tmparr[] = $v1 . ',' . $v2;
      }
    }
    array_unshift($arr, $tmparr);
    $arr = get_array_pc($arr);
  }
  return $arr;
}

// 释放资源
function unset_str($str) {
  if (strpos($str, ',')!==false) {
    $arr = explode(',', $str);
    foreach ($arr as $val) {
      unset($val);
    }
  }else{
    unset($str);
  }
}

function cms($k, $v=NULL) {
  if (!empty($k) && $v===NULL) {
    return @$GLOBALS['cms'][$k];
  } elseif(!empty($k) && $v!==NULL) {
    $GLOBALS['cms'][$k] = $v;
    return ;
  } else {
    return false;
  }
}

function hook($hook, $params=NULL) {
  Hook::listen($hook, $params);
}

//smtp邮件发送
function smtp_mail($mailto, $subject, $body, $system=0) {
  $smtpserver = SMTP_SERVER;
  //SMTP服务器
  $smtpserverport = 25;
  //SMTP服务器端口
  $smtpusermail = SMTP_EMAIL;
  //SMTP服务器的用户邮箱
  $smtpemailto = $mailto;
  //发送给谁
  $smtpuser = SMTP_ID;
  //SMTP服务器的用户帐号
  $smtppass = SMTP_PASS;
  //SMTP服务器的用户密码
  // $mailsubject = $subject;
  $mailsubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
  //邮件主题
  $mailbody = $body;
  if ($system) {
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=utf-8" . "\r\n";

    // 更多报头
    $headers .= 'From: <'.SMTP_EMAIL.'>' . "\r\n";
    return mail($smtpemailto, $mailsubject, $mailbody, $headers);
  } else {
    $smtp = new Smtp($smtpserver, $smtpserverport, $smtpuser, $smtppass);
    $smtp->debug = false;
    return $smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody);
  }
}

function hex_bin($h) {
  return pack("H*", $h);
}

function mysqlinstalled() {
  if (function_exists("mysql_connect")){
    return true;
  } else {
    return false;
  }
}
function mysqliinstalled() {
  if (function_exists("mysqli_connect")){
    return true;
  } else {
    return false;
  }
}

/*
 * -------------------------
 * js
 * -------------------------
*/
function alert($t0) {
  echo '<script type="text/javascript">alert("' . $t0 . '");</script>';
}
function alert_back($t0) {
  die('<script type="text/javascript">alert("' . $t0 . '");window.history.back();</script>');
}
//提示后跳转
function alert_href($t0, $t1) {
  die('<script type="text/javascript">alert("' . $t0 . '");window.location.href="' . $t1 . '"</script>');
}
function back() {
  die('<script type="text/javascript">window.history.back();</script>');
}
function url_back($t0 = '') {
  if (!isset($_COOKIE['cms']['url_back']) || $_COOKIE['cms']['url_back'] == '') {
    if ($t0) {
      alert_href($t0, './');
    } else {
      href('./');
    }
  } else {
    if ($t0) {
      alert_href($t0, $_COOKIE['cms']['url_back']);
    } else {
      href($_COOKIE['cms']['url_back']);
    }
  }
}
function href($t0) {
  die('<script type="text/javascript">window.location.href="' . $t0 . '"</script>');
}
function admin_href($t='') {
  $str = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '/')+1);
  if (substr_count($str, '_') > 1) {
    list($pre, $main) = explode('_', $str);
    $url = $pre . '_' . $main . '.php' . (isset($_GET['cid']) ? '?cid='.$_GET['cid'] : '');
    $t ? alert_href($t, $url) : href($url);
  } else {
    $url = $str . (isset($_GET['cid']) ? '?cid='.$_GET['cid'] : '');
    $t ? alert_href($t, $url) :  href($url);
  }
}
//空值返回
function null_back($t0, $t1) {
  if ($t0 == '' || $t0 === 0 || $t0 === '0' || $t0 === null) {
    alert_back($t1);
  }
}
function n_back($t0, $t1) {
  if ($t0 == '' || $t0 === null) {
    alert_back($t1);
  }
}
//非数字返回
function non_numeric_back($t0, $t1) {
  if (!is_numeric($t0)) {
    alert_back($t1);
  }
}
function non_numeric_href($t0, $t1, $t2) {
  if (!is_numeric($t0)) {
    alert_href($t1, $t2);
  }
}

/*
 * -------------------------
 * 废弃函数处理
 * -------------------------
*/
if(!function_exists('ereg')) {
  function ereg($pattern, $subject, &$matches = NULL) {
    return preg_match('/'.$pattern.'/', $subject, $matches);
  }
}
if(!function_exists('eregi')) {
  function eregi($pattern, $subject, &$matches = NULL) {
    return preg_match('/'.$pattern.'/i', $subject, $matches);
  }
}
if(!function_exists('ereg_replace')) {
  function ereg_replace($pattern, $replacement, $string) {
    return preg_replace('/'.$pattern.'/', $replacement, $string);
  }
}
if(!function_exists('eregi_replace')) {
  function eregi_replace($pattern, $replacement, $string) {
    return preg_replace('/'.$pattern.'/i', $replacement, $string);
  }
}
if(!function_exists('split')) {
  function split($pattern, $subject, $limit = -1) {
    return preg_split('/'.$pattern.'/', $subject, $limit);
  }
}
if(!function_exists('spliti')) {
  function spliti($pattern, $subject, $limit = -1) {
    return preg_split('/'.$pattern.'/i', $subject, $limit);
  }
}
