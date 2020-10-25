<?php
/**
 * 字符截取 支持UTF8/GBK
 * @param $string
 * @param $length
 * @param $dot
 */
function str_cut($string, $length, $dot = '...') {
	$strlen = strlen($string);
	if($strlen <= $length) return $string;
	$string = str_replace(array(' ','&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), array('∵',' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), $string);
	$strcut = '';
	if(strtolower(CHARSET) == 'utf-8') {
		$length = intval($length-strlen($dot)-$length/3);
		$n = $tn = $noc = 0;
		while($n < strlen($string)) {
			$t = ord($string[$n]);
			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1; $n++; $noc++;
			} elseif(194 <= $t && $t <= 223) {
				$tn = 2; $n += 2; $noc += 2;
			} elseif(224 <= $t && $t <= 239) {
				$tn = 3; $n += 3; $noc += 2;
			} elseif(240 <= $t && $t <= 247) {
				$tn = 4; $n += 4; $noc += 2;
			} elseif(248 <= $t && $t <= 251) {
				$tn = 5; $n += 5; $noc += 2;
			} elseif($t == 252 || $t == 253) {
				$tn = 6; $n += 6; $noc += 2;
			} else {
				$n++;
			}
			if($noc >= $length) {
				break;
			}
		}
		if($noc > $length) {
			$n -= $tn;
		}
		$strcut = substr($string, 0, $n);
		$strcut = str_replace(array('∵', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), array(' ', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), $strcut);
	} else {
		$dotlen = strlen($dot);
		$maxi = $length - $dotlen - 1;
		$current_str = '';
		$search_arr = array('&',' ', '"', "'", '“', '”', '—', '<', '>', '·', '…','∵');
		$replace_arr = array('&amp;','&nbsp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;',' ');
		$search_flip = array_flip($search_arr);
		for ($i = 0; $i < $maxi; $i++) {
			$current_str = ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
			if (in_array($current_str, $search_arr)) {
				$key = $search_flip[$current_str];
				$current_str = str_replace($search_arr[$key], $replace_arr[$key], $current_str);
			}
			$strcut .= $current_str;
		}
	}
	return $strcut.$dot;
}
/**
 * 取得文件扩展
 *
 * @param $filename 文件名
 * @return 扩展名
 */
function fileext($filename) {
	return strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
}
/**
 * 对用户的密码进行加密
 * @param $password
 * @param $encrypt //传入加密串，在修改密码时做认证
 * @return array/password
 */
function password($password, $encrypt='') {
	$pwd = array();
	$pwd['encrypt'] =  $encrypt ? $encrypt : create_randomstr();
	$pwd['password'] = md5(md5(trim($password)).$pwd['encrypt']);
	return $encrypt ? $pwd['password'] : $pwd;
}
/**
 * 检查密码长度是否符合规定
 *
 * @param STRING $password
 * @return 	TRUE or FALSE
 */
function is_password($password) {
	$strlen = strlen($password);
	if($strlen >= 6 && $strlen <= 20) return true;
	return false;
}
/**
 * 生成随机字符串
 * @param string $lenth 长度
 * @return string 字符串
 */
function create_randomstr($lenth = 6) {
	return random($lenth, '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ');
}

/**
 * 产生随机字符串
 *
 * @param    int        $length  输出长度
 * @param    string     $chars   可选的 ，默认为 0123456789
 * @return   string     字符串
 */
function random($length, $chars = '0123456789') {
	$hash = '';
	$max = strlen($chars) - 1;
	for($i = 0; $i < $length; $i++) {
		$hash .= $chars[mt_rand(0, $max)];
	}
	return $hash;
}
function list_to_tree($list,$pk='id',$pid='pid',$child='_child',$root=0){
	// 创建Tree
	$tree=array();
	if(is_array($list)){
		// 创建基于主键的数组引用
		$refer=array();
		foreach($list as $key=>$data){
			$refer[$data[$pk]]=& $list[$key];
		}
		foreach($list as $key=>$data){
			// 判断是否存在parent
			$parentId=$data[$pid];
			if($root==$parentId){
				$tree[]=& $list[$key];
			}else{
				if(isset($refer[$parentId])){
					$parent=& $refer[$parentId];
					$parent[$child][]=& $list[$key];
				}
			}
		}
	}
	return $tree;
}
function JsJump($url,$time=0){
	$SleepTime=$time*1000;
	echo '<script language="javascript">window.setTimeout("window.location=\''.$url.'\'", '.$time.');</script>';
	exit();
}
function JsMessage($message,$URL='HISTORY',$charset='utf-8'){
	echo '
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset='.$charset.'" />
			<title>系统提示</title>
			</head>
		
			<body>
			<script type="text/javascript">
			alert("'.$message.'");
			'.(strtoupper($URL)=='HISTORY'?'history.back();':'location.href="'.$URL.'";').'
			</script>
			</body>
			</html>
		';
	exit();
}

//后台验证的一些正则, 只能a-zA-Z0-9_
function checkUsername($string){
	if(preg_match("/^[a-zA-z0-9_]+$/", $string)){
		return true;
	}else{
		return false;
	}
}

//获取一个代理web
function getWebProxy(){
    //清除
    //S('proxy_host', null);
    //S('proxy_port', null);
    
    //获取代理列表
    $url = 'http://www.xicidaili.com/wn';
    $snoopy = new \Lain\Snoopy;
    $snoopy->fetch($url);
    $html_code = $snoopy->results;
    //使用QueryList解析html
    $query_content = \QL\QueryList::Query($html_code, array('proxy_html' => array('#ip_list tr.odd','html')))->data;
    foreach ($query_content as $proxy){
        $proxy_data = \QL\QueryList::Query($proxy['proxy_html'], array('proxy' => array('td:nth-child(3)','html'), 'port' => array('td:nth-child(4)', 'html')))->data;
        //判断IP和端口是否可以访问
        //$proxy_data = array(0 => array('proxy' => '123.138.89.130', 'port'=> '9999'));
        //var_dump($proxy_data);
        if(checkProxy($proxy_data[0]['proxy'], $proxy_data[0]['port'])){
            //保存
            S('proxy_host', $proxy_data['proxy'], 3600*24*7);
            S('proxy_port', $proxy_data['port'], 3600*24*7);
            
            //检测通过, 则跳出
//             echo 'keyong:';
//             var_dump($proxy_data);exit;
            break;
        }
    }
    return true;
}

function checkProxy ($proxy, $port)
{
    //使用百度来检测
    $url = 'http://www.baidu.com/';
    $user_agent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; zh- CN; rv:1.9.0.5) Gecko/2008120122 Firefox/3.0.5 FirePHP/0.2.1";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_PROXY, $proxy);
    curl_setopt($ch, CURLOPT_PROXYPORT, $port); //代理服务器端口
    curl_setopt($ch, CURLOPT_URL, $url);//设置要访问的IP
    //curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);//模拟用户使用的浏览器
    //@curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 ); // 使用自动跳转
    curl_setopt($ch, CURLOPT_TIMEOUT, 3 ); //设置超时时间
    //curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 ); // 自动设置Referer
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $result = curl_exec($ch);
    curl_close($ch);
    //var_dump($result);exit;
    if($result !== false && strpos($result, '百度一下') !== false)
        return true;
    else
        return false;
}

/**
 * 转义 javascript 代码标记
 *
 * @param $str
 * @return mixed
 */
function trim_script($str) {
    if(is_array($str)){
        foreach ($str as $key => $val){
            $str[$key] = trim_script($val);
        }
    }else{
        $str = preg_replace ( '/\<([\/]?)script([^\>]*?)\>/si', '&lt;\\1script\\2&gt;', $str );
        $str = preg_replace ( '/\<([\/]?)iframe([^\>]*?)\>/si', '&lt;\\1iframe\\2&gt;', $str );
        $str = preg_replace ( '/\<([\/]?)frame([^\>]*?)\>/si', '&lt;\\1frame\\2&gt;', $str );
        $str = str_replace ( 'javascript:', 'javascript：', $str );
    }
    return $str;
}

function array2select(array $array, $valueField = 'id', $nameField = 'name'){
    if(!$array)
        return;
    foreach ($array as $key => $value) {
        $datas[$value[$valueField]] = $value[$nameField];
    }
    return $datas;
}

/**
* 将字符串转换为数组
*
* @param    string  $data   字符串
* @return   array   返回数组格式，如果，data为空，则返回空数组
*/
function string2array($data) {
    if($data == '') return array();
    if(is_array($data)) return $data;
    
    if(is_array(unserialize($data))){
        $array = unserialize($data);
    }elseif(json_decode($data, true)){
        $array = json_decode($data, true);
    }else{
        eval("\$array = $data;");
    }
    return $array;
}
/**
* 将数组转换为字符串
*
* @param    array   $data       数组
* @param    bool    $isformdata 如果为0，则不使用new_stripslashes处理，可选参数，默认为1
* @return   string  返回字符串，如果，data为空，则返回空
*/
function array2string($data, $isformdata = 1) {
    if($data == '') return '';
    // if($isformdata) $data = new_stripslashes($data);
    // return addslashes(var_export($data, TRUE));
    return serialize($data);
}


function getcache(){
    return [];
}

function upload_key(){
    return '';
}
function is_ie(){
    
}

/**
 * 检查id是否存在于数组中
 *
 * @param $id
 * @param $ids
 * @param $s
 */
function check_in($id, $ids = '', $s = ',') {
    if(!$ids) return false;
    $ids = explode($s, $ids);
    return is_array($id) ? array_intersect($id, $ids) : in_array($id, $ids);
}
/**
 * 返回经htmlspecialchars处理过的字符串或数组
 * @param $obj 需要处理的字符串或数组
 * @return mixed
 */
function new_html_special_chars($string) {
    $encoding = 'utf-8';
    if(!is_array($string)) return htmlspecialchars($string,ENT_QUOTES,$encoding);
    foreach($string as $key => $val) $string[$key] = new_html_special_chars($val);
    return $string;
}

function new_html_entity_decode($string) {
    $encoding = 'utf-8';
    return html_entity_decode($string,ENT_QUOTES,$encoding);
}

/**
 * 安全过滤函数
 *
 * @param $string
 * @return string
 */
function safe_replace($string) {
    $string = str_replace('%20','',$string);
    $string = str_replace('%27','',$string);
    $string = str_replace('%2527','',$string);
    $string = str_replace('*','',$string);
    $string = str_replace('"','&quot;',$string);
    $string = str_replace("'",'',$string);
    $string = str_replace('"','',$string);
    $string = str_replace(';','',$string);
    $string = str_replace('<','&lt;',$string);
    $string = str_replace('>','&gt;',$string);
    $string = str_replace("{",'',$string);
    $string = str_replace('}','',$string);
    $string = str_replace('\\','',$string);
    return $string;
}
/**
 * xss过滤函数
 *
 * @param $string
 * @return string
 */
function remove_xss($string) { 
    $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $string);

    $parm1 = Array('javascript', 'vbscript', 'expression', 'applet', 'xml', 'blink', 'link', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');

    $parm2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');

    $parm = array_merge($parm1, $parm2); 

    for ($i = 0; $i < sizeof($parm); $i++) { 
        $pattern = '/'; 
        for ($j = 0; $j < strlen($parm[$i]); $j++) { 
            if ($j > 0) { 
                $pattern .= '('; 
                $pattern .= '(&#[x|X]0([9][a][b]);?)?'; 
                $pattern .= '|(&#0([9][10][13]);?)?'; 
                $pattern .= ')?'; 
            }
            $pattern .= $parm[$i][$j]; 
        }
        $pattern .= '/i';
        $string = preg_replace($pattern, ' ', $string); 
    }
    return $string;
}


/**
 * 生成缩略图函数
 * @param  $imgurl 图片路径
 * @param  $width  缩略图宽度
 * @param  $height 缩略图高度
 * @param  $autocut 是否自动裁剪 默认裁剪，当高度或宽度有一个数值为0是，自动关闭
 * @param  $smallpic 无图片是默认图片路径
 */
function thumb($imgurl, $width = 100, $height = 100 ,$autocut = 1, $smallpic = 'nopic.gif', $webp = true) {
    global $image;
    $upload_url = C('UPLOAD_URL'); //附件路径
    $upload_path = C('UPLOAD_PATH');
    if(empty($imgurl)) return 'IMG_PATH'.$smallpic;

    //判断是否是http(s)://的地址
    if(strpos($imgurl, '://') !== false){
        $imgurl_replace= str_ireplace($upload_url, '', $imgurl);
    }else{
        //如果保存的是相对地址,且和系统设置的不一样, '/Public/images/002.jpg'
        if(strpos($imgurl, substr($upload_path, 1)) !== 0){
            //取出第二个/的位置
            $upload_url = substr($imgurl, 0, strpos(substr($imgurl, 1), '/')+2);
            $upload_path = '.'.$upload_url;
        }
        $imgurl_replace= str_ireplace(substr($upload_path, 1), '', $imgurl);
    }
    if(!extension_loaded('gd') || strpos($imgurl_replace, '://')) return $imgurl;
    if(!file_exists($upload_path.$imgurl_replace)) return IMG_PATH.$smallpic;



    list($width_t, $height_t, $type, $attr) = getimagesize($upload_path.$imgurl_replace);
    if(($width>=$width_t || $height>=$height_t) && !$webp) return $imgurl;

    $newimgurl = dirname($imgurl_replace).'/thumb_'.$width.'_'.$height.'_'.basename($imgurl_replace);

    if($webp){
        $webp_url = str_replace(pathinfo($newimgurl)['extension'], 'webp', $newimgurl);
        if(file_exists($upload_path.$webp_url)) return $upload_url.$webp_url;
    }else{
        if(file_exists($upload_path.$newimgurl)) return $upload_url.$newimgurl;
    }

    

    if(!is_object($image)) {
        $image = new \Lain\Phpcms\image(1);
    }
    return $image->thumb($upload_path.$imgurl_replace, $upload_path.$newimgurl, $width, $height, '', $autocut) ? $upload_url.$newimgurl : $imgurl;
}

function svg($imgurl, $color='', $type = '.svg') {
    global $image;
    $upload_url = C('UPLOAD_URL'); //附件路径
    $upload_path = C('UPLOAD_PATH');
    if(empty($imgurl)) return 'IMG_PATH'.$smallpic;

    //判断是否是http(s)://的地址
    if(strpos($imgurl, '://') !== false){
        $imgurl_replace= str_ireplace($upload_url, '', $imgurl);
    }else{
        //如果保存的是相对地址,且和系统设置的不一样, '/Public/images/002.jpg'
        if(strpos($imgurl, substr($upload_path, 1)) !== 0){
            //取出第二个/的位置
            $upload_url = substr($imgurl, 0, strpos(substr($imgurl, 1), '/')+2);
            $upload_path = '.'.$upload_url;
        }
        $imgurl_replace= str_ireplace(substr($upload_path, 1), '', $imgurl);
    }
    if(!extension_loaded('gd') || strpos($imgurl_replace, '://')) return $imgurl;
    if(!file_exists($upload_path.$imgurl_replace)) return IMG_PATH.$smallpic;
    $color = str_replace('#', '', $color);

    $newimgurl = dirname($imgurl_replace).'/color_'.$color.'_'.basename($imgurl_replace);

    if($webp){
        $webp_url = str_replace(pathinfo($newimgurl)['extension'], 'webp', $newimgurl);
        if(file_exists($upload_path.$webp_url)) return $upload_url.$webp_url;
    }else{
        if(file_exists($upload_path.$newimgurl)) return $upload_url.$newimgurl;
    }

    

    if(!is_object($image)) {
        $image = new \Lain\Phpcms\image(1);
    }
    return $image->thumb($upload_path.$imgurl_replace, $upload_path.$newimgurl, $width, $height, '', $autocut) ? $upload_url.$newimgurl : $imgurl;
}

# 1.通过函数访问api，获取数据
#  JSON格式
/*
    参数说明：  $url  为API访问的url
                $type 为请求类型，默认为get
                $data 为传递的数组数据
                $timeout 设置超时时间
    返回值：    返回API返回的数据
*/
function getCurlData($url, $param = [], $type="get",$data=array(),$timeout = 10){
    //对空格进行转义
    $url = str_replace(' ','+',$url);

    if(!empty($param) && is_array($param)){
        $arr = [];
        foreach($param as $k=>$v){
            if(strstr($url,"?")){
                $url .= "&". $k."=".urlencode($v);
            }else{
                $url .= "?".$k."=".urlencode($v);
            }
        }
    }
    $data = json_encode($data);
    // $url = urldecode($url);
    //echo $url ;exit;
    $ch = curl_init();
    //设置选项，包括URL
    curl_setopt($ch, CURLOPT_URL, "$url");

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch,CURLOPT_TIMEOUT,$timeout);  //定义超时3秒钟  
    if($type == "post"){
        // POST数据
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ]);
        // 把post的变量加上
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    //执行并获取url地址的内容
    $output = curl_exec($ch);
    //echo $output ;
    //释放curl句柄
    curl_close($ch);
    //var_dump($output);exit;
    return $output;
}