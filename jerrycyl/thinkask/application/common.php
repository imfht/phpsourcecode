<?php
/*
+--------------------------------------------------------------------------
|   thinkask [#开源系统#]
|   ========================================
|   http://www.thinkask.cn
|   ========================================
|   如果有兴趣可以加群{开发交流群} 485114585
|   ========================================
|   更改插件记得先备份，先备份，先备份，先备份
|   ========================================
+---------------------------------------------------------------------------
 */
/**
 * 系统缓存缓存管理
 * @param mixed $name 缓存名称
 * @param mixed $value 缓存值
 * @param mixed $options 缓存参数
 * @return mixed
 */
// 设置异常错误报错级别 关闭notice错误
error_reporting(E_ERROR | E_PARSE );




/**
 * [getIp 此方法相对比较好]
 * @return [type] [description]
 */
   function fetch_ip(){  
          $realip = '';  
          $unknown = 'unknown';  
          if (isset($_SERVER)){  
              if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown)){  
                  $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);  
                  foreach($arr as $ip){  
                      $ip = trim($ip);  
                      if ($ip != 'unknown'){  
                          $realip = $ip;  
                          break;  
                      }  
                  }  
              }else if(isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']) && strcasecmp($_SERVER['HTTP_CLIENT_IP'], $unknown)){  
                  $realip = $_SERVER['HTTP_CLIENT_IP'];  
              }else if(isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR']) && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)){  
                  $realip = $_SERVER['REMOTE_ADDR'];  
              }else{  
                  $realip = $unknown;  
              }  
          }else{  
              if(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), $unknown)){  
                  $realip = getenv("HTTP_X_FORWARDED_FOR");  
              }else if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), $unknown)){  
                  $realip = getenv("HTTP_CLIENT_IP");  
              }else if(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), $unknown)){  
                  $realip = getenv("REMOTE_ADDR");  
              }else{  
                  $realip = $unknown;  
              }  
          }  
          $realip = preg_match("/[\d\.]{7,15}/", $realip, $matches) ? $matches[0] : $unknown;  
          return $realip;  
      }  
/**
 * 验证 IP 地址是否为内网 IP
 * 
 * @param string
 * @return string
 */
function valid_internal_ip($ip)
{ 

	$ip_address = explode('.', $ip);
	
	if ($ip_address[0] == 10)
	{
		return true;
	}
	
	if ($ip_address[0] == 172 and $ip_address[1] > 15 and $ip_address[1] < 32)
	{
		return true;
	}
	
	if ($ip_address[0] == 192 and $ip_address[1] == 168)
	{
		return true;
	} 
	
	return false;
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
function convert_encoding($string, $from_encoding = 'GBK', $target_encoding = 'UTF-8')
{
	if (function_exists('mb_convert_encoding'))
	{
		return mb_convert_encoding($string, str_replace('//IGNORE', '', strtoupper($target_encoding)), $from_encoding);
	}
	else
	{
		if (strtoupper($from_encoding) == 'UTF-16')
		{
			$from_encoding = 'UTF-16BE';
		}
		
		if (strtoupper($target_encoding) == 'UTF-16')
		{
			$target_encoding = 'UTF-16BE';
		}
		
		if (strtoupper($target_encoding) == 'GB2312' or strtoupper($target_encoding) == 'GBK')
		{
			$target_encoding .= '//IGNORE';
		}
		
		return iconv($from_encoding, $target_encoding, $string);
	}
}

/**
 * 兼容性转码 (数组)
 * 
 * 系统转换编码调用此函数, 会自动根据当前环境采用 iconv 或 MB String 处理, 支持多维数组转码
 * 
 * @param  array
 * @param  string
 * @param  string 
 * @return array
 */
function convert_encoding_array($data, $from_encoding = 'GBK', $target_encoding = 'UTF-8')
{
	return eval('return ' . convert_encoding(var_export($data, true) . ';', $from_encoding, $target_encoding));    
}
/**
 * 双字节语言版 strlen
 * 
 * 使用方法同 strlen()
 * 
 * @param  string
 * @param  string
 * @return string
 */
function cjk_strlen($string, $charset = 'UTF-8')
{
	if (function_exists('mb_strlen'))
	{
		return mb_strlen($string, $charset);
	}
	else
	{
		return iconv_strlen($string, $charset);
	}
}

/**
 * 双字节语言版 strpos
 * 
 * 使用方法同 strpos()
 * 
 * @param  string
 * @param  string
 * @param  int
 * @param  string
 * @return string
 */
function cjk_strpos($haystack, $needle, $offset = 0, $charset = 'UTF-8')
{
	if (function_exists('iconv_strpos'))
	{
		return iconv_strpos($haystack, $needle, $offset, $charset);
	}
	
	return mb_strpos($haystack, $needle, $offset, $charset);
}

/**
 * 双字节语言版 substr
 * 
 * 使用方法同 substr(), $dot 参数为截断后带上的字符串, 一般场景下使用省略号
 * 
 * @param  string
 * @param  int
 * @param  int
 * @param  string
 * @param  string
 * @return string
 */
function cjk_substr($string, $start, $length, $charset = 'UTF-8', $dot = '')
{
	if (cjk_strlen($string, $charset) <= $length)
	{
		return $string;
	}
	
	if (function_exists('mb_substr'))
	{
		return mb_substr($string, $start, $length, $charset) . $dot;
	}
	else
	{
		return iconv_substr($string, $start, $length, $charset) . $dot;
	}
}

/**
 * 字符串截取，支持中文和其他编码
 * @param  [string]  $str     [字符串]
 * @param  integer $start   [起始位置]
 * @param  integer $length  [截取长度]
 * @param  string  $charset [字符串编码]
 * @param  boolean $suffix  [是否有省略号]
 * @return [type]           [description]
 */
function msubstr($str, $start=0, $length=50, $charset="utf-8", $suffix=true) {
    if(function_exists("mb_substr")) {
    	  if($suffix&&mb_strlen($str,'UTF-8')>$length){
    	  	return mb_substr($str, $start, $length, $charset)."...";
    	  }else{
    	  	return mb_substr($str, $start, $length, $charset);
    	  }
        
    } elseif(function_exists('iconv_substr')) {
        return iconv_substr($str,$start,$length,$charset);
    }
    $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("",array_slice($match[0], $start, $length));

    if($suffix) {
        return $slice."......";
    }
    return $slice;
}


/**
 * 递归创建目录
 * 
 * 与 mkdir 不同之处在于支持一次性多级创建, 比如 /dir/sub/dir/
 * 
 * @param  string
 * @param  int
 * @return boolean
 */
function make_dir($dir, $permission = 0777)
{
	$dir = rtrim($dir, '/') . '/';
	
	if (is_dir($dir))
	{
		return TRUE;
	}
	
	if (! make_dir(dirname($dir), $permission))
	{
		return FALSE;
	}
	
	return @mkdir($dir, $permission);
}

/**
 * jQuery jsonp 调用函数
 * 
 * 用法同 json_encode
 * 
 * @param  array
 * @param  string
 * @return string
 */
function jsonp_encode($json = array(), $callback = 'jsoncallback')
{
	if ($_GET[$callback])
	{
		return $_GET[$callback] . '(' . json_encode($json) . ')';
	}
	
	return json_encode($json);
}

/**
 * 时间友好型提示风格化（即微博中的XXX小时前、昨天等等）
 * 
 * 即微博中的 XXX 小时前、昨天等等, 时间超过 $time_limit 后返回按 out_format 的设定风格化时间戳
 * 
 * @param  int
 * @param  int
 * @param  string
 * @param  array
 * @param  int
 * @return string
 */
function date_friendly($timestamp, $time_limit = 604800, $out_format = 'Y-m-d H:i', $formats = null, $time_now = null)
{
	// if (get_setting('time_style') == 'N')
	// {
	// 	return date($out_format, $timestamp);
	// }
	
	if ($formats == null)
	{
		$formats = array('YEAR' => '%s 年前', 'MONTH' => '%s 月前', 'DAY' => '%s 天前', 'HOUR' => '%s 小时前', 'MINUTE' => '%s 分钟前', 'SECOND' => '%s 秒前');
	}
	
	$time_now = $time_now == null ? time() : $time_now;
	$seconds = $time_now - $timestamp;
	
	if ($seconds == 0)
	{
		$seconds = 1;
	}
	
	if ($time_limit != null && $seconds > $time_limit)
	{
		return date($out_format, $timestamp);
	}
	
	$minutes = floor($seconds / 60);
	$hours = floor($minutes / 60);
	$days = floor($hours / 24);
	$months = floor($days / 30);
	$years = floor($months / 12);
	
	if ($years > 0)
	{
		$diffFormat = 'YEAR';
	}
	else
	{
		if ($months > 0)
		{
			$diffFormat = 'MONTH';
		}
		else
		{
			if ($days > 0)
			{
				$diffFormat = 'DAY';
			}
			else
			{
				if ($hours > 0)
				{
					$diffFormat = 'HOUR';
				}
				else
				{
					$diffFormat = ($minutes > 0) ? 'MINUTE' : 'SECOND';
				}
			}
		}
	}
	
	$dateDiff = null;
	
	switch ($diffFormat)
	{
		case 'YEAR' :
			$dateDiff = sprintf($formats[$diffFormat], $years);
			break;
		case 'MONTH' :
			$dateDiff = sprintf($formats[$diffFormat], $months);
			break;
		case 'DAY' :
			$dateDiff = sprintf($formats[$diffFormat], $days);
			break;
		case 'HOUR' :
			$dateDiff = sprintf($formats[$diffFormat], $hours);
			break;
		case 'MINUTE' :
			$dateDiff = sprintf($formats[$diffFormat], $minutes);
			break;
		case 'SECOND' :
			$dateDiff = sprintf($formats[$diffFormat], $seconds);
			break;
	}
	
	return $dateDiff;
}
/**
 * 获得几天前，几小时前，几月前
 * @param int $time 时间戳
 * @param array $unit 时间单位
 * @return bool|string
 */
function date_before($time, $unit = null) {
	$time = intval($time);
	$unit = is_null($unit) ? array("年", "月", "星期", "天", "小时", "分钟", "秒") : $unit;
	switch (true) {
		case $time < (NOW - 31536000) :
			return floor((NOW - $time) / 31536000) . $unit[0] . '前';
		case $time < (NOW - 2592000) :
			return floor((NOW - $time) / 2592000) . $unit[1] . '前';
		case $time < (NOW - 604800) :
			return floor((NOW - $time) / 604800) . $unit[2] . '前';
		case $time < (NOW - 86400) :
			return floor((NOW - $time) / 86400) . $unit[3] . '前';
		case $time < (NOW - 3600) :
			return floor((NOW - $time) / 3600) . $unit[4] . '前';
		case $time < (NOW - 60) :
			return floor((NOW - $time) / 60) . $unit[5] . '前';
		default :
			return floor(NOW - $time) . $unit[6] . '前';
	}
}
/**
 * 根据一个时间戳得到详细信息
 * @param  [type] $time [时间戳]
 * @return [type]      
 * @author [yangsheng@yahoo.com]
 */
function getDateInfo($time){
    $day_of_week_cn=array("日","一","二","三","四","五","六"); //中文星期
    $week_of_month_cn = array('','第1周','第2周','第3周','第4周','第5周','第6周');#本月第几周
    $tenDays= getTenDays(date('j',$time)); #获得旬
    $quarter = getQuarter(date('n',$time),date('Y',$time));# 获取季度
     
    $dimDate = array(
        'date_key' => strtotime(date('Y-m-d',$time)), #日期时间戳
        'date_day' => date('Y-m-d',$time), #日期YYYY-MM-DD
        'current_year' => date('Y',$time),#数字年
        'current_quarter' => $quarter['current_quarter'], #季度
        'quarter_cn' =>$quarter['quarter_cn'],
        'current_month' =>date('n',$time),#月
        'month_cn' =>date('Y-m',$time), #月份
        'tenday_of_month' =>$tenDays['tenday_of_month'],#数字旬
        'tenday_cn' =>$tenDays['tenday_cn'],#中文旬
        'week_of_month' =>ceil(date('j',$time)/7), #本月第几周
        'week_of_month_cn' =>$week_of_month_cn[ceil(date('j',$time)/7)],#中文当月第几周
        'day_of_year' =>date('z',$time)+1,  #年份中的第几天
        'day_of_month' =>date('j',$time),#得到几号
        'day_of_week' =>date('w',$time)>0 ? date('w',$time):7,#星期几
        'day_of_week_cn' =>'星期'.$day_of_week_cn[date('w',$time)],
     );
    return $dimDate;
}
/**
 * 获得日期是上中下旬
 * @param  [int] $j [几号]
 * @return [array]    [description]
 * @author [yangsheng@yahoo.com]
 */
function getTenDays($j)
{  
    $j = intval($j);
     if($j < 1 || $j > 31){
        return false;#不是日期
    }
   $tenDays = ceil($j/10);
    switch ($tenDays) {
        case 1:#上旬
            return array('tenday_of_month'=>1,'tenday_cn'=>'上旬',);
            break;
        case 2:#中旬
             return array('tenday_of_month'=>2,'tenday_cn'=>'中旬',);
            break;       
        default:#下旬
            return array('tenday_of_month'=>3,'tenday_cn'=>'下旬',);
            break;
    }
    return false;
}
/**
 * 根据月份获得当前第几季度
 * @param  [int] $n [月份]
 * @param  [int] $y [年]
 * @return [array]    [description]
 */
function getQuarter($n,$y=null){
     $n = intval($n);
    if($n < 1 || $n > 12){
        return false;   #不是月份
    }
    $quarter = ceil($n/3);
    switch ($quarter) {
        case 1: #第一季度
            return array('current_quarter' => 1, 'quarter_cn'=>$y?$y.'-Q1':'Q1');
            break;
        case 2: #第二季度
            return array('current_quarter' => 2, 'quarter_cn'=>$y?$y.'-Q2':'Q2');
            break;
         case 3: #第三季度
            return array('current_quarter' => 3, 'quarter_cn'=>$y?$y.'-Q3':'Q3');
            break;
         case 4: #第四季度
            return array('current_quarter' => 4, 'quarter_cn'=>$y?$y.'-Q4':'Q4');
            break;
    }
     return false;
}







/**
 * 判断文件或目录是否可写
 * 
 * @param  string
 * @return boolean
 */
function is_really_writable($file)
{
	// If we're on a Unix server with safe_mode off we call is_writable
	if (DIRECTORY_SEPARATOR == '/' and @ini_get('safe_mode') == FALSE)
	{
		return is_writable($file);
	}
	
	// For windows servers and safe_mode "on" installations we'll actually
	// write a file then read it.  Bah...
	if (is_dir($file))
	{
		$file = rtrim($file, '/') . '/is_really_writable_' . md5(rand(1, 100));
		
		if (! @file_put_contents($file, 'is_really_writable() test file'))
		{
			return FALSE;
		}
		else
		{
			@unlink($file);
		}
		
		return TRUE;
	}
	else if (($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE)
	{
		return FALSE;
	}
	
	return TRUE;
}

/**
 * 生成密码种子
 * 
 * @param  integer
 * @return string
 */
function fetch_salt($length = 4)
{
	for ($i = 0; $i < $length; $i++)
	{
		$salt .= chr(rand(97, 122));
	}
	
	return $salt;
}

/**
 * 根据 salt 混淆密码
 *
 * @param  string
 * @param  string
 * @return string
 */
function compile_password($password, $salt)
{
	// md5 password...
	if (strlen($password) == 32)
	{
		return md5($password . $salt);
	}
	
	$password = md5(md5($password) . $salt);
	
	return $password;
}







/**
 * 获取数组中随机一条数据
 * 
 * @param  array
 * @return mixed
 */
function array_random($arr)
{
	shuffle($arr);
	
	return end($arr);
}

/**
 * 获得二维数据中第二维指定键对应的值，并组成新数组 (不支持二维数组)
 * 
 * @param  array
 * @param  string
 * @return array
 */
function fetch_array_value($array, $key)
{
	if (! is_array($array) || empty($array))
	{
		return array();
	}
	
	$data = array();
	
	foreach ($array as $_key => $val)
	{
		$data[] = $val[$key];
	}
	
	return $data;
}

/**
 * 强制转换字符串为整型, 对数字或数字字符串无效
 * 
 * @param  mixed
 */
function intval_string(&$value)
{
	if (! is_numeric($value))
	{
		$value = intval($value);
	}
}

/**
 * 获取时差
 * 
 * @return string
 */
function get_time_zone()
{
	$time_zone = 0 + (date('O') / 100);
	
	if ($time_zone == 0)
	{
		return '';
	}
	
	if ($time_zone > 0)
	{
		return '+' . $time_zone;
	}
	
	return $time_zone;
}



/**
 * 递归读取文件夹的文件列表
 * 
 * 读取的目录路径可以是相对路径, 也可以是绝对路径, $file_type 为指定读取的文件后缀, 不设置则读取文件夹内所有的文件
 * 
 * @param  string
 * @param  string
 * @return array
 */
function fetch_file_lists($dir, $file_type = null)
{
	if ($file_type)
	{
		if (substr($file_type, 0, 1) == '.')
		{
			$file_type = substr($file_type, 1);
		}
	}
	
	$base_dir = realpath($dir);
	$dir_handle = opendir($base_dir);
	
	$files_list = array();
	
	while (($file = readdir($dir_handle)) !== false)
	{		
		if (substr($file, 0, 1) != '.' AND !is_dir($base_dir . '/' . $file))
		{
			if (($file_type AND H::get_file_ext($file, false) == $file_type) OR !$file_type)
			{
				$files_list[] = $base_dir . '/' . $file;
			}
		}
		else if (substr($file, 0, 1) != '.' AND is_dir($base_dir . '/' . $file))
		{
			if ($sub_dir_lists = fetch_file_lists($base_dir . '/' . $file, $file_type))
			{
				$files_list = array_merge($files_list, $sub_dir_lists);
			}	
		} 
	}
	
	return $files_list;
}

/**
 * 判断是否是合格的手机客户端
 * 
 * @return boolean
 */
function is_mobile()
{
	$user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
	
	if (preg_match('/playstation/i', $user_agent) OR preg_match('/ipad/i', $user_agent) OR preg_match('/ucweb/i', $user_agent))
	{
		return false;
	}
	
	if (preg_match('/iemobile/i', $user_agent) OR preg_match('/mobile\ssafari/i', $user_agent) OR preg_match('/iphone\sos/i', $user_agent) OR preg_match('/android/i', $user_agent) OR preg_match('/symbian/i', $user_agent) OR preg_match('/series40/i', $user_agent))
	{
		return true;
	}
	
	return false;
	// return true;
}

/**
 * 判断是否处于微信内置浏览器中
 * 
 * @return boolean
 */
function in_weixin()
{
	$user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
	
	if (preg_match('/micromessenger/i', $user_agent))
	{
		return true;
	}
	
	return false;
}

/**
 * CURL 获取文件内容
 * 
 * 用法同 file_get_contents
 * 
 * @param string
 * @param integerr
 * @return string
 */
function curl_get_contents($url, $timeout = 10)
{
	if (!function_exists('curl_init'))
	{
		throw new Zend_Exception('CURL not support');
	}

	$curl = curl_init();
	
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

	if (substr($url, 0, 8) == 'https://')
	{
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	}
	
	$result = curl_exec($curl);
	
	curl_close($curl);
	
	return $result;
}
/**
 * CURL 获取文件内容
 * 
 * 用法同 curl_post_contents
 *  $url = "http://localhost/web_services.php";
 *  $post_data = array ("username" => "bob","key" => "12345");
 * @param string
 * @param integerr
 * @return string
 */
function curl_post_contents($url = '', $param = ''){
        if (empty($url) || empty($param)) {
            return false;
        }
        $postUrl = $url;
        $curlPost = $param;
        $ch = curl_init(); //初始化curl
        curl_setopt($ch, CURLOPT_URL, $postUrl); //抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0); //设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1); //post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch); //运行curl
        curl_close($ch);
        show($data);
       //　print_r($output);
}
function curl_multi($url){
	 if (!is_array($urls) or count($urls) == 0) {
        return false;
    }
    $curl = $text = array();
    $handle = curl_multi_init();
    foreach($urls as $k => $v) {
        $nurl[$k]= preg_replace('~([^:\/\.]+)~ei', "rawurlencode('\\1')", $v);
        $curl[$k] = curl_init($nurl[$k]);
        curl_setopt($curl[$k], CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl[$k], CURLOPT_HEADER, 0);
        curl_multi_add_handle ($handle, $curl[$k]);
    }
    $active = null;
    do {
        $mrc = curl_multi_exec($handle, $active);
    } while ($mrc == CURLM_CALL_MULTI_PERFORM);
    while ($active && $mrc == CURLM_OK) {
        if (curl_multi_select($handle) != -1) {
            do {
                $mrc = curl_multi_exec($handle, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
        }
    }
    foreach ($curl as $k => $v) {
        if (curl_error($curl[$k]) == "") {
        $text[$k] = (string) curl_multi_getcontent($curl[$k]);
        }
        curl_multi_remove_handle($handle, $curl[$k]);
        curl_close($curl[$k]);
    }
    curl_multi_close($handle);
    return $text;

}


function curlsssst($url){
	$ch = curl_init();
    // $header = array(
    //     'apikey: ',
    // );
    $header ="";
    // 添加apikey到header
    curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // 执行HTTP请求
    curl_setopt($ch , CURLOPT_URL , $url);
    $res = curl_exec($ch);
    show($res);
    die;
    // var_dump(json_decode($res));
}
// 　　

/**
 * 删除网页上看不见的隐藏字符串, 如 Java\0script
 *
 * @param	string
 */
function remove_invisible_characters(&$str, $url_encoded = TRUE)
{
	$non_displayables = array();
	
	// every control character except newline (dec 10)
	// carriage return (dec 13), and horizontal tab (dec 09)
	
	if ($url_encoded)
	{
		$non_displayables[] = '/%0[0-8bcef]/';	// url encoded 00-08, 11, 12, 14, 15
		$non_displayables[] = '/%1[0-9a-f]/';	// url encoded 16-31
	}
	
	$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

	do
	{
		$str = preg_replace($non_displayables, '', $str, -1, $count);
	}
	while ($count);
}
/**
 * 打印输出数据
 * @param void $var
 */
function show($var) {
	if (is_bool($var)) {
		var_dump($var);
	} else if (is_null($var)) {
		var_dump(NULL);
	} else {
		echo "<pre style='padding:10px;border-radius:5px;background:#F5F5F5;border:1px solid #aaa;font-size:14px;line-height:18px;'>" . print_r($var, true) . "</pre>";
	}
}
/**
 * 获得浏览器版本
 */
function browser_info() {
	$agent = strtolower($_SERVER["HTTP_USER_AGENT"]);
	$browser = null;
	if (strstr($agent, 'msie 9.0')) {
		$browser = 'msie9';
	} else if (strstr($agent, 'msie 8.0')) {
		$browser = 'msie8';
	} else if (strstr($agent, 'msie 7.0')) {
		$browser = 'msie7';
	} else if (strstr($agent, 'msie 6.0')) {
		$browser = 'msie6';
	} else if (strstr($agent, 'firefox')) {
		$browser = 'firefox';
	} else if (strstr($agent, 'chrome')) {
		$browser = 'chrome';
	} else if (strstr($agent, 'safari')) {
		$browser = 'safari';
	} else if (strstr($agent, 'opera')) {
		$browser = 'opera';
	}
	return $browser;
}

/**
 * 跳转网址
 * @param string $url 跳转urlg
 * @param int $time 跳转时间
 * @param string $msg
 */
function go($url, $time = 0, $msg = '') {
	$url = U($url);
	if (!headers_sent()) {
		$time == 0 ? header("Location:" . $url) : header("refresh:{$time};url={$url}");
		exit($msg);
	} else {
		echo "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
		if ($time)
			exit($msg);
	}
}
/**
 * 是否为AJAX提交
 * @return boolean
 */
function ajax_request() {
	if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		return true;
	return false;
}
/**
 * 对数组或字符串进行转义处理，数据可以是字符串或数组及对象
 * @param void $data
 * @return type
 */
function addslashes_d($data) {
	if (is_string($data)) {
		return addslashes($data);
	}
	if (is_numeric($data)) {
		return $data;
	}
	if (is_array($data)) {
		$var = array();
		foreach ($data as $k => $v) {
			if (is_array($v)) {
				$var[$k] = addslashes_d($v);
				continue;
			} else {
				$var[$k] = addslashes($v);
			}
		}
		return $var;
	}
}

/**
 * 去除转义
 * @param type $data
 * @return type
 */
function stripslashes_d($data) {
	if (empty($data)) {
		return $data;
	} elseif (is_string($data)) {
		return stripslashes($data);
	} elseif (is_array($data)) {
		$var = array();
		foreach ($data as $k => $v) {
			if (is_array($v)) {
				$var[$k] = stripslashes_d($v);
				continue;
			} else {
				$var[$k] = stripslashes($v);
			}
		}
		return $var;
	}
}


/**
 * 将数组转为字符串表示形式
 * @param array $array 数组
 * @param int $level 等级不要传参数
 * @return string
 */
function array_to_String($array, $level = 0) {
	if (!is_array($array)) {
		return "'" . $array . "'";
	}
	$space = '';
	//空白
	for ($i = 0; $i <= $level; $i++) {
		$space .= "\t";
	}
	$arr = "Array\n$space(\n";
	$c = $space;
	foreach ($array as $k => $v) {
		$k = is_string($k) ? '\'' . addcslashes($k, '\'\\') . '\'' : $k;
		$v = !is_array($v) && (!preg_match("/^\-?[1-9]\d*$/", $v) || strlen($v) > 12) ? '\'' . addcslashes($v, '\'\\') . '\'' : $v;
		if (is_array($v)) {
			$arr .= "$c$k=>" . array_to_String($v, $level + 1);
		} else {
			$arr .= "$c$k=>$v";
		}
		$c = ",\n$space";
	}
	$arr .= "\n$space)";
	return $arr;
}

/**
 * 获得随机字符串
 * @param int $len 长度
 * @return string
 */
function rand_str($len = 6) {
	$data = 'abcdefghijklmnopqrstuvwxyz0123456789';
	$str = '';
	while (strlen($str) < $len)
		$str .= substr($data, mt_rand(0, strlen($data) - 1), 1);
	return $str;
}
/**
     * php 获取一定范围内不重复的随机数字，在1-10间随机产生5个不重复的值
     * @param int $begin
     * @param int $end
     * @param int $limit
     * @return array
     */
 function getRand($begin=0,$end=10,$limit=5){
   	 	$rand_array=range($begin,$end);//把$begin到$end列成一个数组
    	shuffle($rand_array);//将数组顺序随机打乱，shuffle是系统的数组随机排列函数
    return array_slice($rand_array,0,$limit);//array_slice取该数组中的某一段，这里截取0到$limit个，即前$limit个
   }
/**
 * 用户定义常量
 * @param bool $view 是否显示
 * @param bool $tplConst 是否只获取__WEB__这样的常量
 * @return array
 */
function print_const($view = true, $tplConst = false) {
	$define = get_defined_constants(true);
	$const = $define['user'];
	if ($tplConst) {
		$const = array();
		foreach ($define['user'] as $k => $d) {
			if (preg_match('/^__/', $k)) {
				$const[$k] = $d;
			}
		}
	}
	if ($view) {
		p($const);
	} else {
		return $const;
	}
}
/**
 * 获取url
 * @return [type] [description]
 */
function getUrl(){
  $pageURL = 'http';
  if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
    $pageURL .= "s";
  }
  $pageURL .= "://";
  if ($_SERVER["SERVER_PORT"] != "80") {
    $pageURL .= $_SERVER["HTTP_HOST"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
  } else {
    $pageURL .= $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
  }
  return $pageURL;
}
/**
 * 获取当前站点的访问路径根目录
 * @return [type] [description]
 */
function getSiteUrl() {
    $uri = $_SERVER['REQUEST_URI']?$_SERVER['REQUEST_URI']:($_SERVER['PHP_SELF']?$_SERVER['PHP_SELF']:$_SERVER['SCRIPT_NAME']);
    return 'http://'.$_SERVER['HTTP_HOST'].substr($uri, 0, strrpos($uri, '/')+1);
}

/**
 * 下载
 * @param  [type] $filename [description]
 * @param  string $dir      [description]
 * @return [type]           [description]
 */
function downloads($filename,$dir='./'){
    $filepath = $dir.$filename;
    if (!file_exists($filepath)){
        header("Content-type: text/html; charset=utf-8");
        echo "File not found!";
        exit;
    } else {
        $file = fopen($filepath,"r");
        Header("Content-type: application/octet-stream");
        Header("Accept-Ranges: bytes");
        Header("Accept-Length: ".filesize($filepath));
        Header("Content-Disposition: attachment; filename=".$filename);
        echo fread($file, filesize($filepath));
        fclose($file);
    }
}
/**
 * 创建一个目录树
 * @param  [type]  $dir  [description]
 * @param  integer $mode [description]
 * @return [type]        [description]
 */
function mkdirs($dir, $mode = 0777) {
    if (!is_dir($dir)) {
        mkdirs(dirname($dir), $mode);
        return mkdir($dir, $mode);
    }
    return true;
}

    /*********************************************************************
    $id = "http://www.xiaoshuoshu.org/files/article/html/0/160/index.html";
     
    $token = encrypt($id, 'E', 'qingdou');
     
    echo '加密:'.encrypt($id, 'E', 'qingdou');
    echo '<br />';
     
    echo '解密：'.encrypt($token, 'D', 'qingdou');
     
    函数名称:encrypt
    函数作用:加密解密字符串
    使用方法:
    加密     :encrypt('str','E','qingdou');
    解密     :encrypt('被加密过的字符串','D','qingdou');
    参数说明:
    $string   :需要加密解密的字符串
    $operation:判断是加密还是解密:E:加密   D:解密
    $key      :加密的钥匙(密匙);
    *********************************************************************/
    function encrypt($string,$operation,$key='')
    {
        $src  = array("/","+","=");
        $dist = array("_a","_b","_c");
        if($operation=='D'){$string  = str_replace($dist,$src,$string);}
        $key=md5($key);
        $key_length=strlen($key);
        $string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string;
        $string_length=strlen($string);
        $rndkey=$box=array();
        $result='';
        for($i=0;$i<=255;$i++)
        {
            $rndkey[$i]=ord($key[$i%$key_length]);
            $box[$i]=$i;
        }
        for($j=$i=0;$i<256;$i++)
        {
            $j=($j+$box[$i]+$rndkey[$i])%256;
            $tmp=$box[$i];
            $box[$i]=$box[$j];
            $box[$j]=$tmp;
        }
        for($a=$j=$i=0;$i<$string_length;$i++)
        {
            $a=($a+1)%256;
            $j=($j+$box[$a])%256;
            $tmp=$box[$a];
            $box[$a]=$box[$j];
            $box[$j]=$tmp;
            $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
        }
        if($operation=='D')
        {
            if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8))
            {
                return substr($result,8);
            }
            else
            {
                return'';
            }
        }
        else
        {
            $rdate  = str_replace('=','',base64_encode($result));
            $rdate  = str_replace($src,$dist,$rdate);
            return $rdate;
        }
    }

 /**
 * “抽奖”函数
 *
 * @param integer $first    起始编号
 * @param integer $last     结束编号
 * @param integer $total    获奖人数
 *
 * @return string
 *
*/
function isWinner($first, $last, $total)
{
    $winner = array();
    for ($i=0;;$i++)
    {
        $number = mt_rand($first, $last);
        if (!in_array($number, $winner))
            $winner[] = $number;    // 如果数组中没有该数，将其加入到数组
        if (count($winner) == $total)   break;
    }
    return implode(' ', $winner);
}
// for test
// echo isWinner(1, 100, 30);
/**
 * [msginfo 消息提示,]
 * @param  [type] $msg       [STR, 字符型,提示信息]
 * @param  string $urlNumber [数字,-1为上一页,-2为上两页]
 * @return [type]            [description]
 */
function msginfo($msg,$urlNumber='-1'){
	echo "<script>alert('".$msg."');history.go(".$urlNumber.");</script>";
}



    

  

  
  
/**
 * 打印输出数据到文件
 * @param type $data 需要打印的数据
 * @param type $replace 是否要替换打印
 * @param string $pathname 打印输出文件位置
 * @author Anyon Zou <cxphp@qq.com>
 */
function p($data, $replace = false, $pathname = NULL) {
	is_null($pathname) && $pathname = RUNTIME_PATH . date('Ymd') . '_print.txt';
	$model = $replace ? FILE_APPEND : FILE_USE_INCLUDE_PATH;
	if (is_array($data)) {
		file_put_contents($pathname, print_r($data, TRUE), $model);
	} else {
		file_put_contents($pathname, $data, $model);
	}
}



/**
 * 简单对称加密算法之加密
 * @param String $string 需要加密的字串
 * @param String $skey 加密EKY
 * @return String 加密后的字符串
 * @author Anyon Zou <cxphp@qq.com>
 */
function encode($string = '', $skey = '6f918e') {
	$skey = str_split(base64_encode($skey));
	$strArr = str_split(base64_encode($string));
	$strCount = count($strArr);
	foreach ($skey as $key => $value) {
		$key < $strCount && $strArr[$key].=$value;
	}
	return str_replace('=', '6f918e', join('', $strArr));
}

/**
 * 简单对称加密算法之解密
 * @param String $string 需要解密的字串
 * @param String $skey 解密KEY
 * @return String 解密后的字符串
 * @author Anyon Zou <cxphp@qq.com>
 */
function decode($string = '', $skey = '6f918e') {
	$skey = str_split(base64_encode($skey));
	$strArr = str_split(str_replace('6f918e', '=', $string), 2);
	$strCount = count($strArr);
	foreach ($skey as $key => $value) {
		if ($key < $strCount && $strArr[$key][1] === $value) {
			$strArr[$key] = $strArr[$key][0];
		} else {
			break;
		}
	}
	return base64_decode(join('', $strArr));
}


/**
 * 快速时间格式生成
 * @param type $time 时间载
 * @param type $format 时间格式
 * @return type 格式化后的时间
 */
function toDate($time = null, $format = 'Y-m-d H:i:s') {
	is_null($time) && $time = time();
	return date($format, $time);
}





/**
 * 生成参数列表,以数组形式返回
 */
function sp_param_lable($tag = '') {
	$param = array();
	$array = explode(';', $tag);
	foreach ($array as $v) {
		list($key, $val) = explode(':', trim($v));
		$param[trim($key)] = trim($val);
	}
	return $param;
}


/**
 * 全局获取验证码图片 生成的是个HTML的img标签
 * length=4&size=20&width=238&height=50
 * length:字符长度
 * size:字体大小
 * width:生成图片宽度
 * heigh:生成图片高度
 * @param type $imgparam 图片的属性设置
 * @param type $imgattrs IMG标签
 * @return type
 */
function show_verify_img($imgparam = 'length=4&size=15&width=238&height=50', $imgattrs = 'style="cursor: pointer;" title="点击获取"') {
	$src = U('Api/Index/show_verify', $imgparam);
	return $img = <<<hello
<img onclick='this.src+="?"'  src="$src" $imgattrs/>
hello;
}







/**
 * [GrabImage 保存远程图片至本地]
 * @param [type] $url      [远程图片地址]
 * @param string $filename [保存图片名]
 */
function GrabImage($url,$filename="") {
	if($url=="") return false;

	if($filename=="") {
	$ext=strrchr($url,".");
	if($ext!=".gif" && $ext!=".jpg" && $ext!=".png") return false;
	$filename=date("YmdHis").$ext;
	}

	ob_start();
	readfile($url);
	$img = ob_get_contents();
	ob_end_clean();
	$size = strlen($img);

	$fp2=@fopen($filename, "a");
	fwrite($fp2,$img);
	fclose($fp2);

	return $filename;
}
function e($value){
	//null合并运算符
	$value = $value?$value:"";
	echo htmlspecialchars_decode($value);
}
/**
 * 时间差计算
 *
 * @param Timestamp $time
 * @return String Time Elapsed
 * @author Shelley Shyan
 * @copyright http://phparch.cn (Professional PHP Architecture)
 */
function time2Units ($time)
{
   $year   = floor($time / 60 / 60 / 24 / 365);
   $time  -= $year * 60 * 60 * 24 * 365;
   $month  = floor($time / 60 / 60 / 24 / 30);
   $time  -= $month * 60 * 60 * 24 * 30;
   $week   = floor($time / 60 / 60 / 24 / 7);
   $time  -= $week * 60 * 60 * 24 * 7;
   $day    = floor($time / 60 / 60 / 24);
   $time  -= $day * 60 * 60 * 24;
   $hour   = floor($time / 60 / 60);
   $time  -= $hour * 60 * 60;
   $minute = floor($time / 60);
   $time  -= $minute * 60;
   $second = $time;
   $elapse = '';

   $unitArr = array('年'  =>'year', '个月'=>'month',  '周'=>'week', '天'=>'day',
                    '小时'=>'hour', '分钟'=>'minute', '秒'=>'second'
                    );

   foreach ( $unitArr as $cn => $u )
   {
       if ( $$u > 0 )
       {
           $elapse = $$u . $cn;
           break;
       }
   }

   return $elapse;
}


/**********************
一个简单的目录递归函数 目录遍历
第一种实现办法：用dir返回对象
***********************/
function treeDir($directory) 
{ 
	$mydir = dir($directory); 
	echo "<ul>\n"; 
	while($file = $mydir->read())
	{ 
		if((is_dir("$directory/$file")) AND ($file!=".") AND ($file!="..")) 
		{
			echo "<li><font color=\"#ff00cc\"><b>$file</b></font></li>\n"; 
			tree("$directory/$file"); 
		} 
		else 
		echo "<li>$file</li>\n"; 
	} 
	echo "</ul>\n"; 
	$mydir->close(); 
} 
//开始运行

// echo "<h2>目录为粉红色</h2><br>\n"; 
// tree("./nowamagic"); 

/***********************
第二种实现办法：用readdir()函数 目录遍历
************************/
function listDir($dir)
{
	if(is_dir($dir))
   	{
     	if ($dh = opendir($dir)) 
		{
        	while (($file = readdir($dh)) !== false)
			{
     			if((is_dir($dir."/".$file)) && $file!="." && $file!="..")
				{
     				echo "<b><font color='red'>文件名：</font></b>",$file,"<br><hr>";
     				listDir($dir."/".$file."/");
     			}
				else
				{
         			if($file!="." && $file!="..")
					{
         				echo $file."<br>";
      				}
     			}
        	}
        	closedir($dh);
     	}
   	}
}
/**
 * [findfile 查找指定目录下面的所有文件]
 * @param  [type] $dir [description]
 * @return [type]      [description]
 */
function findfile($dir)
{
	if(is_dir($dir))
   	{
     	if ($dh = opendir($dir)) 
		{
        	while (($file = readdir($dh)) !== false)
			{
     			if((is_dir($dir."/".$file)) && $file!="." && $file!="..")
				{
     				// echo "<b><font color='red'>文件名：</font></b>",$file,"<br><hr>";
     				// listDir($dir."/".$file."/");
     			}
				else
				{
         			if($file!="." && $file!="..")
					{
         				// echo $file."<br>";
         				$files[] = $file;
      				}
     			}
        	}
        	closedir($dh);
     	}
     	return $files;
   	}
}
/**
 * [finddirfromdir 查找出莫个PATH下面的文件夹]
 * @param  [type] $dir [description]
 * @return [type]      [description]
 */
function finddirfromdir($dir)
{
	$dirs = array();
	if(is_dir($dir))
   	{
     	if ($dh = opendir($dir)) 
		{
        	while (($file = readdir($dh)) !== false)
			{
     			if((is_dir($dir."/".$file)) && $file!="." && $file!="..")
				{
					$dirs[]=$file;
     			}
			
        	}
        	closedir($dh);
     	}
     	return $dirs;
   	}
}

/**
 * [findfieldfromdir 找出目录下面的文件，返出数组]
 * @param  [type] $path   [路径]
 * @param  string $prefix [以什么开头的文件，如以_function开头]
 * @return [type]         [array]
 */
function findfilefromdir($path,$prefix=""){
	$files = findfile($path);
	$prefixlen = mb_strlen($prefix);
	$newfiles = array();
	foreach ($files as $k => $v) {
		if(msubstr($v,0,$prefixlen,"utf-8",false)==$prefix){
			$newfiles[] = $v;
		}	
	}
	return $newfiles;
}
/**
 * [Scache 缓存方法，为了解决MEMCACHE等其它非文件缓存下面的缓存清除问题]
 * @param [type] $cachename [缓存名]
 * @param [type] $db        [缓存数据]
 * @param [type] $postion   [提交的位置|数据源]
 */
function Scache($cachename,$db,$postion="未设置"){
	// echo "string";
	// show($db);
	if(!empty($db)){
		//在执行S方法之前，将缓存列表先缓存起来
		$data = F('cachelist','',C('PUBLIC_CONF_PATH'));

		if(!empty($data)){
			if(is_array($data[$cachename])){
				$data[$cachename]['scachename'] = $cachename;
				$data[$cachename]['catchtime'] = date('Y-m-d H:i:s');
				$data[$cachename]['postion'] = $postion;
				$data[$cachename]['lastcachetime'] = $data[$cachename]['catchtime'];
				$data[$cachename]['lastpostion'] = $data[$cachename]['postion'];
				F('cachelist',$data,C('PUBLIC_CONF_PATH'));
			}else{
				    $data[$cachename]['scachename'] = $cachename;
					$data[$cachename]['catchtime'] = date('Y-m-d H:i:s');
					$data[$cachename]['postion'] = $postion;
					$data[$cachename]['lastcachetime'] = "";
					$data[$cachename]['lastpostion'] = "";
				F('cachelist',$data,C('PUBLIC_CONF_PATH'));
			}
			
		}else{

			$data[$cachename]['scachename'] = $cachename;
			$data[$cachename]['catchtime'] = date('Y-m-d H:i:s');
			$data[$cachename]['postion'] = $postion;
			$data[$cachename]['lastcachetime'] = "";
			$data[$cachename]['lastpostion'] = "";

			F('cachelist',$data,C('PUBLIC_CONF_PATH'));
		}
		return S($cachename,$db);
	}else{
		//执行S方法
		return S($cachename);
	}

}

// 随机生成一组字符串
function build_count_rand ($number,$length=4,$mode=1) {
    if($mode==1 && $length<strlen($number) ) {
        //不足以生成一定数量的不重复数字
        return false;
    }
    $rand   =  array();
    for($i=0; $i<$number; $i++) {
        $rand[] =   rand_string($length,$mode);
    }
    $unqiue = array_unique($rand);
    if(count($unqiue)==count($rand)) {
        return $rand;
    }
    $count   = count($rand)-count($unqiue);
    for($i=0; $i<$count*3; $i++) {
        $rand[] =   rand_string($length,$mode);
    }
    $rand = array_slice(array_unique ($rand),0,$number);
    return $rand;
}
function rand_email(){
	$postfix =['qq.com','sina.com','sina.com.cn','163.cn','wanli.com','163.com','aliyun.com','qqcs.com','gmail.com','arcs.com','qq.com.cn','vip.qq.com','vip.sina.cn','exrs.com','disre.net','sraxw.net','saraxw.org.cn','szp.org.cn','hqds.org.cn','huwei.cn','ares.cc'];
	return rand_string(rand(5,10),rand(0,3))."@".$postfix[rand(0,count($postfix)-1)];
}
function rand_phone(){
	$prefix = [133,138,152,177,182,192,150,131,130,134,137,139,151,153,155,156,158,159,170,171,172,173,174,175,177,179,160,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197];
	return $prefix[rand(0,count($postfix)-1)].rand_string(8,1);
}
function rand_user(){
	return rand_string(rand(3,7),rand(0,3));
}
/**
 * 产生随机字串，可用来自动生成密码 默认长度6位 字母和数字混合
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
 * @return string
 */
function rand_string($len=6,$type='',$addChars='') {
    $str ='';
    switch($type) {
        case 0:
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$addChars;
            break;
        case 1:
            $chars= str_repeat('0123456789',3);
            break;
        case 2:
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$addChars;
            break;
        case 3:
            $chars='abcdefghijklmnopqrstuvwxyz'.$addChars;
            break;
        case 4:
            $chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借".$addChars;
            break;
        default :
            // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
            $chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$addChars;
            break;
    }
    if($len>10 ) {//位数过长重复字符串一定次数
        $chars= $type==1? str_repeat($chars,$len) : str_repeat($chars,5);
    }
    if($type!=4) {
        $chars   =   str_shuffle($chars);
        $str     =   substr($chars,0,$len);
    }else{
        // 中文随机字
        for($i=0;$i<$len;$i++){
          $str.= msubstr($chars, floor(mt_rand(0,mb_strlen($chars,'utf-8')-1)),1);
        }
    }
    return $str;
}
/**
 * 检查字符串是否是UTF8编码
 * @param string $string 字符串
 * @return Boolean
 */
function is_utf8($string) {
    return preg_match('%^(?:
         [\x09\x0A\x0D\x20-\x7E]            # ASCII
       | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
       |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
       | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
       |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
       |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
       | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
       |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
    )*$%xs', $string);
}
// 自动转换字符集 支持数组转换
function auto_charset($fContents, $from='gbk', $to='utf-8') {
    $from = strtoupper($from) == 'UTF8' ? 'utf-8' : $from;
    $to = strtoupper($to) == 'UTF8' ? 'utf-8' : $to;
    if (strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents))) {
        //如果编码相同或者非字符串标量则不转换
        return $fContents;
    }
    if (is_string($fContents)) {
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($fContents, $to, $from);
        } elseif (function_exists('iconv')) {
            return iconv($from, $to, $fContents);
        } else {
            return $fContents;
        }
    } elseif (is_array($fContents)) {
        foreach ($fContents as $key => $val) {
            $_key = auto_charset($key, $from, $to);
            $fContents[$_key] = auto_charset($val, $from, $to);
            if ($key != $_key)
                unset($fContents[$key]);
        }
        return $fContents;
    }
    else {
        return $fContents;
    }
} 
function iconvUtog($strInput) {
    return iconv('utf-8','gb2312',$strInput);//页面编码为utf-8时使用，否则导出的中文为乱码
}
function iconvGtou($strInput){
	return iconv('gb2312','utf-8',$strInput);

}
/**
 * 把返回的数据集转换成Tree
 * @access public
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 */
function list_to_tree($list, $pk='id',$pid = 'pid',$child = '_child',$root=0) {
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
 * 对查询结果集进行排序
 * @access public
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param array $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 * @return array
 */
function list_sort_by($list,$field, $sortby='asc') {
   if(is_array($list)){
       $refer = $resultSet = array();
       foreach ($list as $i => $data)
           $refer[$i] = &$data[$field];
       switch ($sortby) {
           case 'asc': // 正向排序
                asort($refer);
                break;
           case 'desc':// 逆向排序
                arsort($refer);
                break;
           case 'nat': // 自然排序
                natcasesort($refer);
                break;
       }
       foreach ( $refer as $key=> $val)
           $resultSet[] = &$list[$key];
       return $resultSet;
   }
   return false;
}

/**
 * 在数据列表中搜索
 * @access public
 * @param array $list 数据列表
 * @param mixed $condition 查询条件
 * 支持 array('name'=>$value) 或者 name=$value
 * @return array
 */
function list_search($list,$condition) {
    if(is_string($condition))
        parse_str($condition,$condition);
    // 返回的结果集合
    $resultSet = array();
    foreach ($list as $key=>$data){
        $find   =   false;
        foreach ($condition as $field=>$value){
            if(isset($data[$field])) {
                if(0 === strpos($value,'/')) {
                    $find   =   preg_match($value,$data[$field]);
                }elseif($data[$field]==$value){
                    $find = true;
                }
            }
        }
        if($find)
            $resultSet[]     =   &$list[$key];
    }
    return $resultSet;
}

/**
 * 加载文件并缓存
 * @param null $path 导入的文件
 * @return bool
 */
function require_cache_hd($path = null)
{
    static $_files = array();
    if (is_null($path)) return $_files;
    //缓存中存在  即代表文件已经加载  停止加载
    if (isset($_files[$path])) {
        return true;
    }
    //区分大小写的文件判断
    if (!file_exists_case_hd($path)) {
        return false;
    }
    require($path);
    $_files[$path] = true;
    return true;
}
/**
 * 区分大小写的判断文件判断
 * @param string $file 需要判断的文件
 * @return boolean
 */
function file_exists_case_hd($file)
{
    if (is_file($file)) {
        //windows环境下检测文件大小写
        if (IS_WIN && C("CHECK_FILE_CASE")) {
            if (basename(realpath($file)) != basename($file)) {
                return false;
            }
        }
        return true;
    }
    return false;
}
// 二维数组的重复项：
// 对于二维数组咱们分两种情况讨论，一种是因为某一键名的值不能重复，删除重复项；另一种因为内部的一维数组不能完全相同，而删除重复项，下面举例说明：
// ㈠因为某一键名的值不能重复，删除重复项
// $aa = array(
    // array('id' => 123, 'name' => '张三'),
    // array('id' => 123, 'name' => '李四'),
    // array('id' => 124, 'name' => '王五'),
    // array('id' => 125, 'name' => '赵六'),
    // array('id' => 126, 'name' => '赵六')
    // );
    // $key = 'id';
    // assoc_unique(&$aa, $key);
    // print_r($aa);
// 
//     显示结果为：Array ( [0] => Array ( [id] => 123 [name] => 张三 ) [1] => Array ( [id] => 124 [name] => 王五 ) [2] => Array ( [id] => 125 [name] => 赵六 ) [3] => Array ( [id] => 126 [name] => 赵六 ) )
  function assoc_unique($arr, $key)
     {
       $tmp_arr = array();
       foreach($arr as $k => $v)
      {
         if(in_array($v[$key], $tmp_arr))//搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
        {
           unset($arr[$k]);
        }
      else {
          $tmp_arr[] = $v[$key];
        }
      }
    sort($arr); //sort函数对数组进行排序
    return $arr;
    }

 // ㈡因内部的一维数组不能完全相同，而删除重复项
 //  $aa = array(
//     array('id' => 123, 'name' => '张三'),
//     array('id' => 123, 'name' => '李四'),
//     array('id' => 124, 'name' => '王五'),
//     array('id' => 123, 'name' => '李四'),
//     array('id' => 126, 'name' => '赵六')
//     );
//     $bb=array_unique_fb($aa);
//     print_r($bb)
// 显示结果：Array ( [0] => Array ( [0] => 123 [1] => 张三 ) [1] => Array ( [0] => 123 [1] => 李四 ) [2] => Array ( [0] => 124 [1] => 王五 ) [4] => Array ( [0] => 126 [1] => 赵六 ) )  
    function array_unique_fb($array2D){
         foreach ($array2D as $v){
             $v = join(",",$v); //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
             $temp[] = $v;
         }
         $temp = array_unique($temp);    //去掉重复的字符串,也就是重复的一维数组
        foreach ($temp as $k => $v){
            $temp[$k] = explode(",",$v);   //再将拆开的数组重新组装
        }
        return $temp;
    }
//=============================插件类 核心方法=====================================
/**
 * 1、电信手机访问，HTTP头会有手机号码值，移动、联通则无。
 *2、文中所提到的插入代码即可获取，纯属子虚乌有，文中的功能是一些做移动网络服务的公司，先向电信、移动、联通官方购买查询接口，该接口是以类似统计代码形式插入到你的网站，然后会有个后台统计系统。最后向其他公司贩卖会员，按数据条数收钱（重复也算），奇贵无比，每次最少续费三万。
 *3、只有移动网络有效（电信手机、移动、联通），其他方式访问无效。
 *（2013-8-16 10:43:10 核总补充：手机型号则是使用 HTTP 头 User-Agent 判断的，非常简单的“技术”，和普通网站程序判断浏览器型号及系统类型的方法一摸一样。）
 *该思路、系统最出自于医疗行业，未来移动互联网是发展方向，估计会扩展到其他行业。
 * [getPhoneNumber 获取访问的手机号码]
 * @return [type] [description]
 */
function getPhoneNumber()

{
       if (isset($_SERVER['HTTP_X_NETWORK_INFO']))
       {
         $str1 = $_SERVER['HTTP_X_NETWORK_INFO'];
         $getstr1 = preg_replace('/(.*,)(13[\d]{9})(,.*)/i','\\2',$str1);
         return $getstr1;
       }
       elseif (isset($_SERVER['HTTP_X_UP_CALLING_LINE_ID']))
       {
         $getstr2 = $_SERVER['HTTP_X_UP_CALLING_LINE_ID'];
         return $getstr2;
       }
       elseif (isset($_SERVER['HTTP_X_UP_SUBNO']))
       {
         $str3 = $_SERVER['HTTP_X_UP_SUBNO'];
         $getstr3 = preg_replace('/(.*)(13[\d]{9})(.*)/i','\\2',$str3);
         return $getstr3;
       }
       elseif (isset($_SERVER['DEVICEID']))
       {
         return $_SERVER['DEVICEID'];
       }
       else
       {
         return false;

       }

}
/**
 * [encode_pwd 加密密码]
 * @param  [type] $password [description]
 * @param  [type] $salt     [description]
 * @return [type]           [description]
 */
function encode_pwd($password, $salt)
{
	$password = md5(md5($password) . $salt);

	return $password;
}


/**
 * [adv_js Js广告内外推处理]
 * @return [type] [description]
 */
function adv_js($id){
	if(!S('adv_js'.$id)){
		$newid = decode($id);
		$advJs = M('adv_js')->where("id = $newid")->find();
		// $str =$advJs['dbtype']=="userdefault"&&?"<div class='sethits' id='$id'>{$advJs['jsdb']}</div>":"";
		//是否为自定义推广的内容--图片
		if($advJs['dbtype']=="userdefault"&&$advJs['type']=="img"){
			$str = "<div><a href='{$advJs['href']}' target='_blank'  class='setHits' ids='$id'><img style='{$advJs['style']}' src='{$advJs['jsdb']}'></a></div>
					<script type='text/javascript'>
						$('.setHits').click(function(event) {
								  $.ajax({
								    url: '/ajax/hits/sethits',
								    type: 'post',
								    dataType: 'json',
								    data: {id: $(this).attr('ids')},
								  })
								});
					</script>

			";
		}
		S('adv_js'.$id,$str);
	}
	return S('adv_js'.$id);
}
/**
 * [upexcel excle 上传至数据库]
 * @param  [type] $tmp [上传后的EXCEL名]
 * @param  [type] $l [列数]
 * @return [type]      [ARRAY-]
 */
function excel($tmp,$l){
require_once(APP_PATH.'common/Lib/plus/excel/reader.php');
//创建对象
$data = new Spreadsheet_Excel_Reader();
//设置文本输出编码
$data->setOutputEncoding('UTF-8');
//读取Excel文件
$data->read($tmp);
error_reporting(E_ALL ^ E_NOTICE);
$dbs =array();
for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
	$str = "";
		//$tablestr .= "<tr>";
		//$tablestr .="<td><input type='checkbox' value = '{$i}' id = {$i}></td>";
		//$tablestr .="<td><span>第{$i}行</span></td>";
	    for ($j = 1; $j <= $l; $j++) {
	           
	            if($data->sheets[0]['cells'][$i][$j]==""){
	            	//为空时，证明无数据，所以填写数据时要注意
	            	$str = null;
	            	continue;

	                // echo $nuk="null";   
	            }
	            $str.=  $data->sheets[0]['cells'][$i][$j].",";
	            //带表格，方便后面显示数据
	            
	           // $tablestr .="<td>".$data->sheets[0]['cells'][$i][$j]."</td>";
	    }
	    $tablestr .="</tr>";
    if(!$str){break;}
    $dbs[]= $str;
    //$dbs['show'] ="<table width='100%'>" . $tablestr . "</table>";
    //带表格，方便后面显示数据
}
	return $dbs;
}

/**
 * [creat_code 二维码生成类----生成本地图片]
 * [creat_code reutn 图片的完整路径]
 * @param  [type]  $url    [二维码写入的内容]
 * @param  [type]  $mix    [二维码图片名的区别，避免覆盖]
 * @param  [type]  $path   [二维码图片路径]
 * creat_code($url,$_GET['rid'],"./Public/Codeimg/re_detail/")
 */
function creat_code($url,$mix,$path){
	//先判断文件夹是否存在
	if(!is_dir($path)){
		make_dir($path);
	}

	import('phpqrcode.phpqrcode',EXTEND_PATH);
	$value = $url; //二维码内容   
	$errorCorrectionLevel = 'L';//容错级别   
	$matrixPointSize = 6;//生成图片大小   
	//生成二维码图片   
	QRcode::png($value, "{$path}code{$mix}.png", $errorCorrectionLevel, $matrixPointSize, 2);  
	$logo = 'logo.png';//准备好的logo图片   
	$QR = 'qrcode'.$mix.'.png';//已经生成的原始二维码图   
	 
	if ($logo !== FALSE) {   
	    $QR = imagecreatefromstring(file_get_contents($QR));   
	    $logo = imagecreatefromstring(file_get_contents($logo));   
	    $QR_width = imagesx($QR);//二维码图片宽度   
	    $QR_height = imagesy($QR);//二维码图片高度   
	    $logo_width = imagesx($logo);//logo图片宽度   
	    $logo_height = imagesy($logo);//logo图片高度   
	    $logo_qr_width = $QR_width / 5;   
	    $scale = $logo_width/$logo_qr_width;   
	    $logo_qr_height = $logo_height/$scale;   
	    $from_width = ($QR_width - $logo_qr_width) / 2;   
	    //重新组合图片并调整大小   
	    imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,   
	    $logo_qr_height, $logo_width, $logo_height);   
	}   
	//输出图片   
	$img = "{$path}code{$mix}.png";
	// show($img);
	return $img;
	//echo '<img src="'.$path.'code'.$mix.'.png" width="'.$width.'" height='.$height.'>';
}




/**
 * [treeMenus 菜单无限级分类管理]
 * @param  [type] $arr      [description]
 * @param  [type] $parentid [description]
 * @return [type]           [description]
 */
 function treeMenus($arr, $parentid){

        $array = array();
 
        foreach ($arr as $k => $v) {
             
            if($v['parentid'] == $parentid){
                $v['children'] = treeMenus($arr, $v['id']);
                 if($parentid!=0){
                	$v['note'] = "├─ ";
                }
                $array[] = $v;
               
                
            }else{
                continue;
            }
        }

        return $array;
    }

 /**
 * [treeMenus 菜单无限级分类Html]
 * @param  [type] $arr      [description]
 * @param  [type] $parentid [description]
 * @return [type]           [description]
 */
 function treeMenusHtml($arr, $parentid,$level="0",$str = ""){

        $array = array();
        $level++;
        $str .= "&nbsp;";
        foreach ($arr as $k => $v) {
             
            if($v['parentid'] == $parentid){
            	
                $v['child'] = treeMenusHtml($arr, $v['id'],$level,$str);
                if(count($v['child'])>0){
                		if($parentid!=0){
	                	$v['note'] = "├─";
	                	$v['level'] = $level;
	                	$v['str'] = $str;
	                }
                }else{
                	$v['note'] = "└─";
	                	$v['level'] = $level;
	                	$v['str'] = $str;
                }
                 
                $array[] = $v;
               
                
            }else{

                continue;
            }
        }

        return $array;
    }



   /**
    * [sendTelephone 手机验证码]
    * @param  [type] $telephone [description]
    * @param  [type] $code      [description]
    * @return [type]            [description]
    */
  function sendTelephone($telephone, $code) {
        if (!$telephone)
            return false;
        $url = 'http://125.208.1.165/appserver/sms/smsmt/send.php?pid=124105&number=&extend=56&password=&mobile='.$telephone.'&message=';
        $data = '您正在参与集团分公司开业活动，您的验证码为：' . $code;
        $message = urlencode(iconv('utf-8', 'gbk', $data.'xx金融集团】'));        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url.$message);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        curl_close($ch);
        
        return $data;
    }
   



/**
 * 获取登录验证码 默认为4位数字
 * @param string $fmode 文件名
 * @return string
 */
function build_verify ($length=4,$mode=1) {
    return rand_string($length,$mode);
}

/**
 * 字节格式化 把字节数格式为 B K M G T 描述的大小
 * @return string
 */
function byte_format($size, $dec=2) {
	$a = array("B", "KB", "MB", "GB", "TB", "PB");
	$pos = 0;
	while ($size >= 1024) {
		 $size /= 1024;
		   $pos++;
	}
	return round($size,$dec)." ".$a[$pos];
}









/**
 * [get_csv_contents 读取CSV文件，EXCEL处理]
 * @param  [type] $file_target [description]
 * @return [type]              [description]
 */
function get_csv_contents( $file_target ){
	$zou = array();
	$ExcelArr = array();
 	$handle  = fopen( $file_target, 'r');
  fwrite($handle,chr(0xEF).chr(0xBB).chr(0xBF));
 while ($data = fgetcsv($handle, "", ",")) {
 
  $num = count($data);
  $str = "";
  $row++;
  for ($c=0; $c < $num; $c++) {
  	 	$str .= iconv("GBK", 'UTF-8', $data[$c]) .',';
  	 	if($c==2){
  	 		include_once(COMMON_LIB_PATH."Ip/IpLocation.class.php");

		    $Ip = new \IpLocation(); // 实例化类
		    $location = $Ip->getlocation(trim($data[$c])); // 获取某个IP地址所在的位置
		  $str .= ",".$location['country'].$location['area'].",";
  	 	}


  }

M('excel_test')->add(array('db'=>$str));

 }

 fclose($handle);
}
/**
 * [outputCsv 导出CSV]
 * @param  [type] $data [字符型]
 * @return [type]       [description]
 * $data .= i($result[$i]['name']).','.i($result[$i]['option'])."\n";  换行用\n
 */
function outputCsv($str){
	    $filename = date('YmdHis').".csv";
	    header("Content-type:text/csv");
	    header("Content-Disposition:attachment;filename=".$filename);
	    header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
	    header('Expires:0');
	    header('Pragma:public');
	    echo $str;

 }




 
/**
 * 插件显示内容里生成访问插件的url
 * @param string $url url
 * @param array $param 参数
 * @author better
 * @useage u_addons('apply://App/Index/addorder',array('id'=>'1'))
 */
function u_addons($url, $param = array()){
    $url = explode('://', $url);
    $addon = $url[0];
    $url = $url[1];
 
    $url = U($url, $param, false);
    return $url . '/addon/' . $addon;
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
	for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
	return round($size, 2) . $delimiter . $units[$i];
}
/**
 +----------------------------------------------------------
 * 功能：计算文件大小
 +----------------------------------------------------------
 * @param int $bytes
 +----------------------------------------------------------
 * @return string 转换后的字符串
 +----------------------------------------------------------
 */
function byteFormat($bytes) {
    $sizetext = array(" B", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
    return round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), 2) . $sizetext[$i];
} 
function getset($name){
	if(is_array($cache = cache('system_setting'))){
		foreach ($cache as $key => $v) {
			if($v['varname']==$name){
				return unserialize($v['value']);
			}
		}
	}else{
		return "";
	}
	
}

function gourl($url){
	echo "<script>window.location.href='".$url."'</script>";
}
/**
 * [val PHP自带的验证规则]
 * @param  [type] $value [description]
 * @return [type]        [description]
 * FILTER_CALLBACK 	调用用户自定义函数来过滤数据。
*FILTER_SANITIZE_STRING 	去除标签，去除或编码特殊字符。
*FILTER_SANITIZE_STRIPPED 	“string” 过滤器的别名。
*FILTER_SANITIZE_ENCODED 	URL-encode 字符串，去除或编码特殊字符。
*FILTER_SANITIZE_SPECIAL_CHARS 	HTML 转义字符 ‘”<>& 以及 ASCII 值小于 32 的字符。
*FILTER_SANITIZE_EMAIL 	删除所有字符，除了字母、数字以及 !#$%&’*+-/=?^_`{|}~@.[]
*FILTER_SANITIZE_URL 	删除所有字符，除了字母、数字以及 $-_.+!*’(),{}|\\^~[]`<>#%”;/?:@&=
*FILTER_SANITIZE_NUMBER_INT 	删除所有字符，除了数字和 +-
*FILTER_SANITIZE_NUMBER_FLOAT 	删除所有字符，除了数字、+- 以及 .,eE。
*FILTER_SANITIZE_MAGIC_QUOTES 	应用 addslashes()。
*FILTER_UNSAFE_RAW 	不进行任何过滤，去除或编码特殊字符。
*FILTER_VALIDATE_INT 	在指定的范围以整数验证值。
*FILTER_VALIDATE_BOOLEAN 	如果是 “1″, “true”, “on” 以及 “yes”，则返回 true，如果是 “0″, “false”, “off”, “no” 以及 “”，则返回 false。否则返回 NULL。
*FILTER_VALIDATE_FLOAT 	以浮点数验证值。
*FILTER_VALIDATE_REGEXP 	根据 regexp，兼容 Perl 的正则表达式来验证值。
*FILTER_VALIDATE_URL 	把值作为 URL 来验证。
*FILTER_VALIDATE_EMAIL 	把值作为 e-mail 来验证。
*FILTER_VALIDATE_IP 	把值作为 IP 地址来验证。
 */
function filter($str,$type){
	switch ($type) {
		case 'email':
			$filter = FILTER_VALIDATE_EMAIL;
			break;
		case 'url':
			$filter = FILTER_VALIDATE_URL;
			break;
		
		case 'boolean':
			$filter = FILTER_VALIDATE_BOOLEAN;
			break;
		case 'float':
			$filter = FILTER_VALIDATE_FLOAT;
			break;
		case 'preg':
			$filter = FILTER_VALIDATE_REGEXP;
			break;
		
		default:
			$filter = FILTER_VALIDATE_EMAIL;
			break;
	}
	return filter_var($str, $filter);
}
 function asset($path){
	return "/static/template/asset/".$path;
}
/**
 * [tostr description]
 * @param  [type] $arr [数组]
 * @return [type]      [返出一维数组的VALUE值]
 */
function tostr($arr){
	// show($arr);
 return trim(current($arr));
}
//安装时使用
function format_textarea($string) {
    $chars = 'utf-8';
    return nl2br(str_replace(' ', '&nbsp;', htmlspecialchars($string,ENT_COMPAT,$chars)));
}
function status($status=true){
	if($status){
		echo "<i class='fa fa-check'></i>";
	}else{
		echo "<i class='fa fa-close'></i>";
	}
}
/**
 * 实时显示提示信息
 * @param  string $msg 提示信息
 * @param  string $class 输出样式（success:成功，error:失败）
 * @author huajie <banhuajie@163.com>
 */
function showmsg($msg, $class = ''){
    echo "<script type=\"text/javascript\">showmsg(\"{$msg}\", \"{$class}\")</script>";
    flush();
    ob_flush();
}

/**
 * 随机字符
 * @param number $length 长度
 * @param string $type 类型
 * @param number $convert 转换大小写
 * @return string
 */
function random($length=10, $type='letter', $convert=0){
    $config = array(
        'number'=>'1234567890',
        'letter'=>'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
        'string'=>'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789',
        'all'=>'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
    );

    if(!isset($config[$type])) $type = 'letter';
    $string = $config[$type];

    $code = '';
    $strlen = strlen($string) -1;
    for($i = 0; $i < $length; $i++){
        $code .= $string{mt_rand(0, $strlen)};
    }
    if(!empty($convert)){
        $code = ($convert > 0)? strtoupper($code) : strtolower($code);
    }
    return $code;
}

/** 
 * 中文分词处理方法 
 *+--------------------------------- 
 * @param stirng  $string 要处理的字符串 
 * @param boolers $sort=false 根据value进行倒序 
 * @param Numbers $top=0 返回指定数量，默认返回全部 
 *+--------------------------------- 
 * @return void 
 */  
function scws($text, $top = 5, $return_array = false, $sep = ',') {  
    include(EXTEND_PATH.'pscws4/pscws4.php');  
    $cws = new pscws4('utf-8');  
    $cws -> set_charset('utf-8');  
    $cws -> set_dict(EXTEND_PATH.'/pscws4/etc/dict.utf8.xdb');  
    $cws -> set_rule(EXTEND_PATH.'/pscws4/etc/rules.utf8.ini');  
    //$cws->set_multi(3);  
    $cws -> set_ignore(true);  
    //$cws->set_debug(true);  
    //$cws->set_duality(true);  
    $cws -> send_text($text);  
    $ret = $cws -> get_tops($top, 'r,v,p');  
    $result = null;  
    foreach ($ret as $value) {  
        if (false === $return_array) {  
            $result .= $sep . $value['word'];  
        } else {  
            $result[] = $value['word'];  
        }  
    }  
    return false === $return_array ? substr($result, 1) : $result;  
}  




