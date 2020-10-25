<?php
//检查图片是否存在 grysoft/
function ispic($picUrl,$nopicUrl="/inc/img/system/nopic.jpg")
{
	$tempUrl = explode('|',$picUrl);
	$tempPic = explode('/',$tempUrl[0]); //分割图片地址信息
	if($tempPic[0]!='http:')
	{
		if(is_file(ABSPATH.$tempUrl[0]))
		return get_root_path().$tempUrl[0];
		else
		return get_root_path().$nopicUrl;  
	}
	else
	{
		return $tempUrl[0];
	}
}
//检查文件是否存在 2011-09-10
function isfile($fileUrl)
{
	$tempPic = explode('/',$fileUrl); 
	if($tempPic[0]!='http:')
	{
		return is_file(ABSPATH.$fileUrl)?true:false;  
	}else{
		return false;
	}
}
//检查请求字符串
function checkme($power,$go_404=false)
{
	if(empty($_SESSION[TB_PREFIX.'admin_name']) or $_SESSION[TB_PREFIX.'admin_roleId']<$power)
	{
		if($go_404){
		redirect($tag['path.root'] .'/404.html');exit;}
		print_error('You do not have permission to access this page!');
	}
}
function get_str($string)
{
	if (!get_magic_quotes_gpc()) {
		return addslashes($string);
	}
	return $string;
}
function cleanArrayForMysql($data)
{
	if(!get_magic_quotes_gpc())
		return (is_array($data))?array_map('cleanArrayForMysql', $data):mysql_real_escape_string($data);
	else
		return $data;
}
function checkSqlStr($string)
{
	$string = strtolower($string);
	return preg_match('/select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile|_user/i', $string);
}
//重定向到某页
function redirect($url)
{
	//echo  "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; URL=".$url."\">";
	echo "<script>window.location.href='".$url."'</script>";
	exit;
}
//重定向到某页
function redirect_to($model,$action='index',$query='')
{
	$url='./index.php?p='.$model.'&a='.$action;
	$url.=empty($query)?'':'&'.$query;
	//echo  "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; URL=".$url."\">";
	echo "<script>window.location.href='".$url."'</script>";
	exit;
}
//弹框信息并跳转指定页
function alertHref($msg,$url)
{
		//echo  "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; URL=".$url."\">";
	echo "<script>alert('".$msg."');window.location.href='".$url."';</script>";
	exit();
}
//弹框信息并返回上一页
function alertGo($msg)
{
		//echo  "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; URL=".$url."\">";
	echo "<script>alert('".$msg."');history.go(-1)</script>";
	exit();
}
function print_error($info)
{
	echo $info;
	exit;
}
function success()
{
	echo '<script>alert("授权文件安装成功！");location.href="./";</script>';
	exit;
}
//生成新的文件($str为字符串,$filePath为生成时的文件路径包括文件名)
function string2file($str,$filePath)
{
	$fp=fopen($filePath,'w+');
	fwrite($fp,$str);
	fclose($fp);
}
//从文件中读取字符
function file2string($filePath)
{
	$fp = fopen($filePath,"r");
	$content_= fread($fp, filesize($filePath));
	fclose($fp);
	return $content_;

}
/*
	Add 2007-07-07
	获取中英文混合字符串的长度
	autor:suny
*/
function cnStrLen($str)
{
	$i = 0;
	$tmp = 0;
	while ($i < strlen($str))
	{
		if (ord(substr($str,$i,1)) >127)
		{
			$tmp = $tmp+1;
			$i = $i + 3;
		}
		else
		{
			$tmp = $tmp + 1;;
			$i = $i + 1;
		}
	}
	return $tmp;
}
/*
	Add 2007-07-07
	获取中英文混合字符在字符串中的位置
	autor:suny
*/
function cnStrPos($str,$keyword)
{
	$i = 0;
	$tem = 0;
	$temStr = strpos($str,$keyword);
	while ($i < $temStr)
	{
		if (ord(substr($str,$i,1)) >127)
		{
			$tmp = $tmp+1;
			$i = $i + 3;
		}
		else
		{
			$tmp = $tmp + 1;;
			$i = $i + 1;
		}
	}
	return $tmp;
}
//截取字符数
//$str-字符串
//$N-多少字符
function cnSubStr($str, $start, $lenth)
{
	$len = strlen($str);
	$r = array();
	$n = 0;
	$m = 0;
	for($i = 0; $i < $len; $i++) {
		$x = substr($str, $i, 1);
		$a = base_convert(ord($x), 10, 2);
		$a = substr('00000000'.$a, -8);
		if ($n < $start){
			if (substr($a, 0, 1) == 0) {
			}elseif (substr($a, 0, 3) == 110) {
				$i += 1;
			}elseif (substr($a, 0, 4) == 1110) {
				$i += 2;
			}
			$n++;
		}else{
			if (substr($a, 0, 1) == 0) {
				$r[] = substr($str, $i, 1);
			}elseif (substr($a, 0, 3) == 110) {
				$r[] = substr($str, $i, 2);
				$i += 1;
			}elseif (substr($a, 0, 4) == 1110) {
				$r[] = substr($str, $i, 3);
				$i += 2;
			}else{
				$r[] = '';
			}
			if (++$m >= $lenth){
				break;
			}
		}
	}
	return join('', $r);

} // End subString_UTF8
//去除HTML字符标记
function trimTags($string)
{
	$string=strip_tags($string);
	$string=str_replace(" ","",$string);
	$string=trim($string);
	return $string;
}
function mkdirs($path, $mode = 0777) //creates directory tree recursively
{
	$path=str_replace('\\','/',$path);

	$dirs = explode('/',$path);
	$pos = strrpos($path, ".");
	if ($pos === false) { // note: three equal signs
		// not found, means path ends in a dir not file
		$subamount=0;
	}
	else {
		$subamount=1;
	}

	for ($c=0;$c < count($dirs) - $subamount; $c++) {
		$thispath="";
		for ($cc=0; $cc <= $c; $cc++) {
			$thispath.=$dirs[$cc].'/';
		}
		if (!file_exists($thispath)) {
			//print "$thispath<br>";
			mkdir($thispath,$mode);
		}
	}
}
//验证器
function validates_presence_of($fieldName,$info)
{
	global $request;
	if(empty($request[$fieldName]))
	{
		echo $info.'was required to field!<br />';
		exit;
	}
}

function validates_email_of($fieldName,$info)
{
	if(function_exists(checkdnsrr))
	{
		global $request;
		if (!preg_match('/^[0-9a-z_\-\.]+@([0-9a-z\-]+.)+([a-z]){2,4}$/i', $request[$fieldName]))
		{		
			echo "E-mail address wrong.";
			exit;
		}
		else
		{
			list($name,$domain)=split("@",$request[$fieldName]);
			if(!checkdnsrr($domain,'MX'))
			{
				echo "E-mail not exist.";
				exit;
			}
		}
	}
}
function select($str_arr,$name,$select=null,$ev=null)
{
	if($ev)
	echo '<select id="'.$name.'" name="'.$name.'" '.$ev.'>';
	else
	echo '<select name="'.$name.'">';
	foreach ($str_arr as $k=>$v)
	{
		$selected=($select==$k)?' selected="selected" ':'';
		?>
    	<option value="<?php echo $k ?>"<?php echo $selected ?>><?php echo $v ?></option>
		<?php
	}
	echo '</select>';
}
function db_select_box($str_arr,$key_feild,$value_feild,$name,$select=null,$ev=null)
{
	if($ev)
	echo '<select id="'.$name.'" name="'.$name.'" '.$ev.'>';
	else
	echo '<select id="'.$name.'" name="'.$name.'">';
	foreach ($str_arr as $o)
	{
		$selected=($select==$o->$key_feild)?' selected="selected" ':' ';
		?>
    	<option value="<?php echo $o->$key_feild ?>" <?php echo $selected ?>><?php echo $o->$value_feild ?></option>
		<?php
	}
	echo '</select>';
}
function db_radio_box($str_arr,$key_feild,$value_feild,$name,$select=null)
{
	foreach ($str_arr as $o)
	{
		$selected=($select==$o->$key_feild)?' checked="checked" ':' ';
		?>
    	<span><input type="radio" <?php echo $selected ?> id="<?php echo $name ?>" name="<?php echo $name ?>" value="<?php echo $o->$key_feild ?>"><?php echo $o->$value_feild ?></span>
		<?php
	}
}
/*
	Add 2007-07-07
	截取一段字符串中的字符并标示出来
	autor:suny
*/
function get_keyword_str($str,$keyword,$getstrlen)
{
	if(cnStrLen($str)> $getstrlen) 
	{
		$strlen = cnStrLen($keyword);
		$strpos = cnStrPos($str,$keyword);
		$halfStr = intval(($getstrlen-$strlen)/2);
		$str = cnSubStr($str,($strpos - $halfStr),$halfStr).$keyword.cnSubStr($str,($strpos + $strlen),$halfStr);
		return str_replace($keyword,'<span style="font-size: 12px; color: #F30;">'.$keyword.'</span>',$str).'...';
	}
	else
	{
		return str_replace($keyword,'<span style="font-size: 12px; color: #F30;">'.$keyword.'</span>',$str);
	}
}
/*获取目录信息*/
function getDirSize($path)
{
  $totalsize = 0;
  $totalcount = 0;
  $dircount = 0;
  if ($handle = opendir ($path))
  {
    while (false !== ($file = readdir($handle)))
    {
      $nextpath = $path . '/' . $file;
      if ($file != '.' && $file != '..' && !is_link ($nextpath))
      {
        if (is_dir ($nextpath))
        {
          $dircount++;
          $result = getDirSize($nextpath);
          $totalsize += $result['size'];
          $totalcount += $result['count'];
          $dircount += $result['dircount'];
        }
        elseif (is_file ($nextpath))
        {
          $totalsize += filesize ($nextpath);
          $totalcount++;
        }
      }
    }
  }
  closedir ($handle);
  $total['size'] = $totalsize;    //目录总大小
  $total['count'] = $totalcount;  //目录中的文件数量
  $total['dircount'] = $dircount; //目录中的文件夹数量
  return $total;
}
/*检验文件大小*/
function DisplayFileSize($filesize){
	$array = array(
	'YB' => 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024,
	'ZB' => 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024, 'EB' => 1024 * 1024 * 1024 * 1024 * 1024 * 1024,
	'PB' => 1024 * 1024 * 1024 * 1024 * 1024,
	'TB' => 1024 * 1024 * 1024 * 1024,
	'GB' => 1024 * 1024 * 1024,
	'MB' => 1024 * 1024,
	'KB' => 1024,     );
	if($filesize <= 1024)
	{
		$filesize = $filesize . ' B';
	}
	foreach($array AS $name => $size)
	{
		if($filesize > $size || $filesize == $size)
		{
			$filesize = round((round($filesize / $size * 100) / 100), 0) . ' ' . $name;
		}
	}
	return $filesize;
}
/*产生$length位随机数*/
function random($length) {
	$hash = '';
	$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
	$max = strlen($chars) - 1;
	for($i = 0; $i < $length; $i++) {
		$hash .= $chars[mt_rand(0, $max)];
	}
	return $hash;
}
//获得客户端IP,并转换为long型
function getip()
{
	if(getenv('HTTP_CLIENT_IP'))
	{
		$client_ip = getenv('HTTP_CLIENT_IP');
	}
	elseif(getenv('HTTP_X_FORWARDED_FOR'))
	{
		$client_ip = getenv('HTTP_X_FORWARDED_FOR');
	}
	elseif(getenv('REMOTE_ADDR'))
	{
		$client_ip = getenv('REMOTE_ADDR');
	}
	else
	{
		$client_ip = $HTTP_SERVER_VAR['REMOTE_ADDR'];
	}
	return ip2long($client_ip);
} 
//获得文件格式前缀
function extend_1($file_name)
{
	$extend =explode("." , $file_name); 
	$va=count($extend)-2;
	return $extend[$va];
}
//获得文件格式后缀
function extend_2($file_name)
{
	$extend =explode("." , $file_name); 
	$va=count($extend)-1;
	return $extend[$va];
}
//列出某文件夹内所有文件
function rec_listFiles($from = '.',$type='php')
{
    if(! is_dir($from))
        return false;
   
    $files = array();
    if( $dh = opendir($from))
    {
        while( false !== ($file = readdir($dh)))
        {
            // Skip '.' and '..'
            if( $file == '.' || $file == '..'||extend_2($file)!=$type)
                continue;
            //$path = $from . '/' . $file;
            $path = $file;
            if( is_dir($path) )
                //$files += rec_listFiles($path);
				continue;
            else
                $files[] = $path;
        }
        closedir($dh);
    }
    return $files;
}
//对整个目录进行拷贝
function dir_copy($fdir,$tdir)
{   
	if(is_dir($fdir))
	{
		if (!is_dir($tdir))
		{
			mkdir($tdir);
		}
		$handle =opendir($fdir);
		while(false!==($filename=readdir($handle)))
		{	  
			if($filename!="."&&$filename!="..")dir_copy($fdir."/".$filename,$tdir."/".$filename);	 
		}
		closedir($handle);		
		return true;
	}
	else 
	{
		copy($fdir,$tdir);
		return true;
	}	
}
function RemoveXSS($val) { 
    // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed 
    // this prevents some character re-spacing such as <java\0script> 
    // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some          // inputs 
    $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val); 
    
    // straight replacements, the user should never need these since they're normal characters 
    // this prevents like <IMG SRC=@avascript:alert('XSS')> 
    $search = 'abcdefghijklmnopqrstuvwxyz'; 
    $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
    $search .= '1234567890!@#$%^&*()'; 
    $search .= '~`";:?+/={}[]-_|\'\\'; 
    for ($i = 0; $i < strlen($search); $i++) { 
        // ;? matches the ;, which is optional 
        // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars 

        // @ @ search for the hex values 
        $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val);//with a ; 
        // @ @ 0{0,7} matches '0' zero to seven times 
        $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ; 
    } 

    // now the only remaining whitespace attacks are \t, \n, and \r 
    $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base'); 
    $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload'); 
    $ra = array_merge($ra1, $ra2); 
   
    $found = true; // keep replacing as long as the previous round replaced something 
    while ($found == true) { 
        $val_before = $val; 
        for ($i = 0; $i < sizeof($ra); $i++) { 
            $pattern = '/'; 
            for ($j = 0; $j < strlen($ra[$i]); $j++) { 
                if ($j > 0) { 
                    $pattern .= '('; 
                    $pattern .= '(&#[xX]0{0,8}([9ab]);)'; 
                    $pattern .= '|'; 
                    $pattern .= '|(&#0{0,8}([9|10|13]);)'; 
                    $pattern .= ')*'; 
                } 
                $pattern .= $ra[$i][$j]; 
            } 
            $pattern .= '/i'; 
            $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag 
            $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags 
            if ($val_before == $val) { 
                // no replacements were made, so exit the loop 
                $found = false; 
            } 
        } 
    } 
    return $val; 
}
function filter_submitpath($path)
{
 	$path= preg_replace('/[\.]{2,}/', '', $path);//去除 .. 禁止提交访问上级目录的路径
 	return preg_replace('/[\/]{2,}/', '/', $path);//校正路径 
}
function filter_submitname($path)
{
	$path= preg_replace('/[\.]{2,}/', '', $path);//去除 .. 
 	return preg_replace('/[\/]{1,}/', '', $path);
}
function check_path($path)
{
 	return preg_replace('/[\/]{2,}/', '/', $path);//校正路径    
}

function get_style_file($func_style,$func_name,$style=0)
{
    global $stylename;
    $style_file1 = ABSPATH.'/'.SKINROOT.'/'.$stylename.'/index/'.$func_style.'/'.$func_name.'_'.$style.'.php';
	
	$style_file2 = ABSPATH.'content/index/style/index_'.$func_name.'_0.php';
	return is_file($style_file1)?$style_file1:$style_file2;
}
/*注销db*/
function destorydb(){
	global $db,$tempdb;
	$tempdb=$db;
	$db=null;
}
/*还原db*/
function recoverdb(){
	global $db,$tempdb;
	$db=$tempdb;
	$tempdb=null;
}
/*截取字串 */
function sys_substr($str,$strcount,$isellipsis)
{
	if($strcount>0){
		if(!$isellipsis)
			return cnSubstr( $str,0,$strcount-1 ); //截取标题字数
		elseif($isellipsis && cnStrLen($str)>$strcount)
			return cnSubstr( $str,0,$strcount-1 )."...";
		else 
			return $str; //保留完整标
	}else{
		return $str; 
	}
}
function string_join($var,$join='-'){
		$join=$join?" $join ":"";
		return $var?$var.$join:'';
}

function encryCookie($cookie,$key='&^%$yrfgp',$cookieName='doc_basket'){//使用时修改密钥$key 涉及金额结算请重新设计cookie存储格式
	require_once (ABSPATH.'/inc/class.syscrypt.php');
	$cookie=serialize($cookie);
	$key=md5($key);
	$sc = new SysCrypt($key);
	$cookie=$sc->php_encrypt($cookie);
	//setcookie("shl_basket",$cookie, time()+3600,'/','',false);//失效时间   0关闭浏览器即失效
	setcookie($cookieName,$cookie, 0,'/','',false);
}
function parseCookie($cookie,$key='&^%$yrfgp'){
	require_once (ABSPATH.'/inc/class.syscrypt.php');
	$key=md5($key);
	$sc = new SysCrypt($key);
	$cookie=$sc->php_decrypt($cookie);
	return unserialize($cookie);
}
/* 标签参数验证  grysoft(狗头巫师)*/
function label_check($opts = array())
{
	if(isset($opts['channelId']))
	{
		if(checkSqlStr($opts['channelId']) || !$opts['channelId'])return ('标签引用非法ID in '.$opts['tbxstyle'].'()!');
	}
	if(isset($opts['n']))
	{
		if(!is_int($opts['n']))return ('parameters $n is not integer in '.$opts['fun'].'()!');
	}
	if(isset($opts['style']))
	{
		if(!is_file(get_abs_skin_root().'index/'.$opts['tbxstyle'].'/'.$opts['tbxstyle'].'_'.$opts['style'].'.php'))
		return '加载'.get_skin_root().'index/'.$opts['tbxstyle'].'/'.$opts['tbxstyle'].'_'.$opts['style'].'.php 样式资源文件失败，程序意外终止。';
	}
	if(isset($opts['strcount']))
	{
		if(!is_int($opts['strcount']))return ('parameters $strcount is not integer in '.$opts['fun'].'()!');
	}
	if(isset($opts['strcount1']))
	{
		if(!is_int($opts['strcount1']))return ('parameters $strcount1 is not integer in '.$opts['fun'].'()!');
	}
	if(isset($opts['strcount2']))
	{
		if(!is_int($opts['strcount2']))return ('parameters $strcount2 is not integer in '.$opts['fun'].'()!');
	}
	if(isset($opts['isellipsis']))
	{
		if(!is_bool($opts['isellipsis']))return ('parameters $isellipsis is not bool in '.$opts['fun'].'()!'); 
	}
	if(isset($opts['hastag']))
	{
		if(!is_bool($opts['hastag']))return ('parameters $hastag is not bool in '.$opts['fun'].'()!'); 
	}
	if(isset($opts['ordering']))
	{
		if(checkSqlStr($opts['ordering']))return ('parameters $ordering is not allowed in '.$opts['fun'].'()!');
	}
	if(isset($opts['fromcount']))
	{
		if(!is_int($opts['fromcount']))return ('parameters $fromcount is not integer in '.$opts['fun'].'()!');
	}
}
function isMobile(){ 
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if(isset($_SERVER['HTTP_X_WAP_PROFILE'])){
        return true;
    } 
    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if(isset($_SERVER['HTTP_VIA'])){ 
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    } 
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高
    if(isset($_SERVER['HTTP_USER_AGENT'])){
        $clientkeywords = array ('nokia','sony','ericsson','mot', 'samsung','htc','sgh','lg','sharp','sie-','philips', 'panasonic','alcatel','lenovo','iphone','ipod', 'blackberry', 'meizu', 'android','netfront', 'symbian', 'ucweb','windowsce','palm','operamini','operamobi','openwave', 'nexusone', 'cldc','midp', 'wap','mobile'); 
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if(preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))){
            return true;
        } 
    } 
    // 协议法，因为有可能不准确，放到最后判断
    if(isset($_SERVER['HTTP_ACCEPT'])){ 
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))){
            return true;
        } 
    } 
    return false;
}