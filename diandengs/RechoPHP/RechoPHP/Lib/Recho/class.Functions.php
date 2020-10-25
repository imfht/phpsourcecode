<?php
// +----------------------------------------------------------------------
// | RechoPHP [ WE CAN DO IT JUST Better ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2014 http://recho.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: recho <diandengs@gmail.com>
// +----------------------------------------------------------------------

class functions{
	
	/**
	 * 设置COOKIE
	 * @param string $name
	 * @param mixed $value
	 * @param int $time 过期时间,�?则关闭浏览器失效
	 */
	static public function setCookie($name, $value, $time=0){
	    $expires = $time ? self::time()+(int)$time : 0;
	    setcookie($name, $value, $expires, '/');
    }
    
    static public function header(){
        header("Content-Type:text/html;charset=utf-8");
    }
    
    static public function nocache(){
        header("Pragma:no-cache");
        header("Cache-Type:no-cache, must-revalidate");
        header("Expires: -1");
    }
    
    static public function dp3p(){
	   header("P3P:CP='ALL DSP CURa ADMa DEVa CONi OUT DELa IND PHY ONL PUR COM NAV DEM CNT STA PRE'");
    }
    
    /**
     * 在HTTp协议请求和响应中加入这条就能维持长连接
     *
     */
    static public function keep(){
    	header("Connection:keep-alive");
    }
    
    static function getip(){
    	if($_SERVER['REMOTE_ADDR']) {
    		$ip = $_SERVER['REMOTE_ADDR'];
    	}else if($_SERVER['HTTP_CLIENT_IP']){
    		$ip = $_SERVER['HTTP_CLIENT_IP'];
    	}else if($_SERVER['HTTP_X_FORWARDED_FOR']){
    		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    	}
    	return ip2long( $ip);
    }
    
    /**
     * 返回浏览器信息 firefox3 msie6
     */
    static function getbrowser(){
		
    	$ret = 'Other0';
    	
    	$browsers = array('navigator', 'firefox', 'msie', 'opera', 'chrome', 'safari', 
		                'mozilla', 'seamonkey', 'konqueror', 
		                'gecko', 'navigator', 'mosaic', 'lynx', 'amaya', 
		                'omniweb', 'avant', 'camino', 'flock', 'aol'); 
		
		$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
		foreach($browsers as $browser){
			if(preg_match("#($browser)[/ ]?([0-9.]*)#", $agent, $match)){ 
			    $aInfo['nav'] = $match[1];
			    $aInfo['ver'] = $match[2];
			    $ret = $match[1] . intval( $match[2]);
			    break;
			}
		}
		
		return $ret;
    }
    
    /**
     * 获取浏览器主语言: zh zh-ch zh-tw等
     */
    static function getlanguage(){
		$languages = strtolower( $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
		$languages = str_replace( ' ', '', $languages);
		$languages = explode( ",", $languages);
		return (string)$languages[0];
	}

	/**
	 * 获取操作系统
	 */
	static function getos() {
		
		$Agent = $_SERVER['HTTP_USER_AGENT'];
		if(@eregi('win',$Agent) && strpos($Agent, '95')) {
		    $os="Windows 95";
		}elseif (@eregi('win 9x',$Agent) && strpos($Agent, '4.90')) {
		    $os="Windows ME";
		}elseif (@eregi('win',$Agent) && @eregi('98',$Agent)) {
		    $os="Windows 98";
		}elseif (@eregi('win',$Agent) && @eregi('nt 5.0',$Agent)) {
		    $os="Windows 2000";
		}elseif (@eregi('win',$Agent) && @eregi('nt 5.1',$Agent)) {
		    $os="Windows XP";
		}elseif (@eregi('win',$Agent) && @eregi('nt',$Agent)) {
		    $os="Windows NT";
		}elseif (@eregi('win',$Agent) && @eregi('32',$Agent)) {
		    $os="Windows 32";
		}elseif(@eregi('mac',$Agent)){
			$os="Apple";
		}elseif (@eregi('linux',$Agent)) {
		    $os="Linux";
		}elseif (@eregi('unix',$Agent)) {
		    $os="Unix";
		}elseif (@eregi('sun',$Agent) && @eregi('os',$Agent)) {
		    $os="SunOS";
		}elseif (@eregi('ibm',$Agent) && @eregi('os',$Agent)) {
		    $os="IBM OS/2";
		}elseif (@eregi('Mac',$Agent) && @eregi('PC',$Agent)) {
		    $os="Macintosh";
		}elseif (@eregi('PowerPC',$Agent)) {
		    $os="PowerPC";
		}elseif (@eregi('AIX',$Agent)) {
		    $os="AIX";
		}elseif (@eregi('HPUX',$Agent)) {
		    $os="HPUX";
		}elseif (@eregi('NetBSD',$Agent)) {
		    $os="NetBSD";
		}elseif (@eregi('BSD',$Agent)) {
		    $os="BSD";
		}elseif (@eregi('OSF1',$Agent)) {
		    $os="OSF1";
		}elseif (@eregi('IRIX',$Agent)) {
		    $os="IRIX";
		}elseif (@eregi('FreeBSD',$Agent)) {
		    $os="FreeBSD";
		}elseif(@eregi('iPhone',$Agent)){
			$os='iPhone';
		}elseif(@eregi('iPod',$Agent)){
			$os='iPod';
		}elseif(@eregi('BlackBerry',$Agent)){
			$os='BlackBerry';
		}elseif(@eregi('BeOS',$Agent)){
			$os='BeOS';
		}else{
			$os = "Unknown";
		}
		return $os;
	}

    
    static public function magic_quote( $mixVar){
        if( get_magic_quotes_gpc()){
        	$temp = '';
            if(is_array( $mixVar)){
                foreach ( $mixVar as $key => $value){
                    $temp[$key] = self::magic_quote( $value);
                }
            }else{
                $temp = stripslashes( $mixVar); 
            }
            return $temp;
        }else{
        	return $mixVar;
        }
    }
    
    /**
     * 计算字符串的CRC32值.范围为0~4294967296
     */
    static function crc32( $str){
    	return sprintf("%u", crc32( $str));
    }
	
	/**
     * arr的长和宽等比例缩小至$arrTo resize(array($array['width'],$array['height']), array(160,120))
     * @return unknown
     */
    static function resize($arr, $arrTo ){
        $arr[0] = $arr[0]>10 ? $arr[0] : $arrTo[0];
        $arr[1] = $arr[1]>10 ? $arr[1] : $arrTo[1]; 
        $arrTo[0] = $arrTo[0]<=0 ? 160 : $arrTo[0];
        $arrTo[1] = $arrTo[1]<=0 ? 120 : $arrTo[1];
        $temp = $arr;
        
        if( $arr[0] > $arrTo[0]){ //如果宽度超出
            $temp[0] = $arrTo[0];
            $temp[1] = (int)($temp[0]*$arr[1]/$arr[0]);
            if( $temp[1] > $arrTo[1]){
                $temp[1] = $arrTo[1];
                $temp[0] = (int)($arr[0]*$temp[1]/$arr[1]);
            }            
        }
        
        if( $arr[1] > $arrTo[1] ){ //如果高度超出
            $temp[1] = $arrTo[1];
            $temp[0] = (int)($arr[0]*$temp[1]/$arr[1]);
            if( $temp[0] > $arrTo[0]){
                $temp[0] = $arrTo[0];
                $temp[1] = (int)($temp[0]*$arr[1]/$arr[0]);
            }
        }
        return $temp;
    }
    
    /**
     * 返回UNIX时间�?     * @param boolen $float 是否精确到微�?     * @return int/float
     */
	static public function time( $float=false){
		return $float ? microtime( true) : time();
	}
	
	static public function uint( $num){
		return max(0, (int)$num);
	}

	/**
	 * 获取子目录
	 *
	 * @param unknown_type $uid
	 * @return unknown
	 */
	static function get_subdir( $uid) {
		$uid = abs(intval($uid));
	    $uid = sprintf("%09d", $uid);
	    $dir1 = substr($uid, 0, 3);
	    $dir2 = substr($uid, 3, 2);
	    $dir3 = substr($uid, 5, 2);
	    return $dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2) .'/';
    }

	/**
	 * 序列化16位MD5(主要用于生成点卡时的显示及列表显示)
	 * @param <type> $arr(md51,md52....)
	 * @return <type> $arr(string1,string2,.....)
	 */
	static public function cardSerializeMD5($arr) {
		if (!is_array($arr)) {
			return false;
		}
		$aTem = array();
		$code = '';
		$iTem = 0;
		$numToword = array(0 => 'A', 1 => 'B', 2 => 'C', 3 => 'D', 4 => 'E', 5 => 'F', 6 => 'G', 7 => 'H', 8 => 'I', 9 => 'J');
		$cfg = array(0 => 'Z', 2 => 'A', 5 => 'B', 7 => 'C', 8 => 'D', 9 => 'E', 10 => 'F', 11 => 'G', 13 => 'H', 14 => 'I', 15 => 'J', 16 => 'K', 17 => 'L', 19 => 'M', 22 => 'N', 24 => 'O');
		foreach ($arr as $val) {
			$count = strlen($val = strtoupper($val));
			for ($i = 0; $i < $count; $i++) {
				if (is_numeric($val[$i])) {
					$val[$i] = $numToword[$val[$i]];
					if ($i == 0 || $i == 4 || $i == 8 || $i == 12) {
						$iTem += 9;
					} else if ($i == 1 || $i == 5 || $i == 9 || $i == 13)
						$iTem += 2;
					else if ($i == 2 || $i == 6 || $i == 10 || $i == 14)
						$iTem += 5;
					else
						$iTem += 8;
				}
				if ($i == 3 || $i == 7 || $i == 11 || $i == 15) {
					$code .= "$cfg[$iTem]";
					$iTem = 0;
				}
			}
			$val = "{$val[0]}{$val[1]}{$val[2]}{$val[3]}{$code[0]}-{$val[4]}{$val[5]}{$val[6]}{$val[7]}{$code[1]}-{$val[8]}{$val[9]}{$val[10]}{$val[11]}{$code[2]}-{$val[12]}{$val[13]}{$val[14]}{$val[15]}{$code[3]}";
			//$aTem .= $val . "<br>";
			$aTem[] = $val;
			$code = '';
		}
		return $aTem;
	}

	/**
	 * 反序列化字符串成MD5(主要用于点卡使用时)
	 * @param <type> $str SDFGG-WERDA-SDFED-ASFDF
	 * @return string(md5串)
	 */
	static public function cardunSerializeStr($str) {
		$wordTonum = array('A' => '0', 'B' => '1', 'C' => '2', 'D' => '3', 'E' => '4', 'F' => '5', 'G' => '6', 'H' => '7', 'I' => '8', 'J' => '9');
		$cfg = array('Z' => '0', 'A' => '2', 'B' => '5', 'C' => '7', 'D' => '8', 'E' => '9', 'F' => '10', 'G' => '11', 'H' => '13', 'I' => '14', 'J' => '15', 'K' => '16', 'L' => '17', 'M' => '19', 'N' => '22', 'O' => '24');
		$hash = array(0 => '', 2 => '1', 5 => '2', 7 => '12', 8 => '3', 9 => '0', 10 => '13', 11 => '01', 13 => '23', 14 => '02', 15 => '123', 16 => '012', 17 => '03', 19 => '013', 22 => '023', 24 => '0123');//获取需要替换的成数字的位数
		$code = array($cfg[$str[4]], $cfg[$str[10]], $cfg[$str[16]], $cfg[$str[22]]);//得到(换位所需字母)
		$count = strlen($str);
		$t = '';
		for ($i = 0; $i < $count; $i++) {
			if ($i != 4 && $i != 5 && $i != 10 && $i != 11 && $i != 16 && $i != 17 && $i != 22) {
				$t .= $str[$i];
			}
		}

		//SDFG WERD SDFE ASFD;
		$arr = array($hash[$code[0]], $hash[$code[1]], $hash[$code[2]],$hash[$code[3]]);//根据(换位所需字母)得到每个字段需要换哪些位
		for($i=0;$i<4;$i++){
			$len = strlen($arr[$i]);//得到每个字段要换多少位
			for($j=0;$j<$len;$j++){
				$bit = $arr[$i][$j];//得到要换哪个位
				if($i==1) $bit += 4;
				if($i==2) $bit += 8;
				if($i==3) $bit += 12;
				$t[$bit] = $wordTonum[$t[$bit]];//根据位上的字母映射成数字
			}
		}
		return $t;
	}

	/**
	 * 文件上传
	 * @param <type> $path		上传路径
	 * @param <type> $file		客户端传过来的文件变量
	 * @param <type> $size		最大文件尺寸(kb单位)
	 * @param <type> $types		允许的文件类型(多个类型间用豆号隔开)
	 * @param <type> $newName	新文件名
	 * @param bool	 $isreplace	是复替换同名文件
	 * @return array(code) code:1000不存在此路径、1001不是文件、1002文件过大、1003类型有误、1004上传出错、1文件合法/上传成功
	 */
	static function fileUpload($path, $file, $size, $types, $newName=false, $isupload=false, $isreplace=false) {
		if( !is_dir( $path)){
			return array( '1000', '不存在此路径');
		}
		if( !isset( $file['error']) || $file['error']){
			return array( '1001', '不是文件');
		}
		if( $file['size'] > 1024*$size){
			return array( '1002', '文件过大');
		}
		//$fileType = self::getFileType( $file['tmp_name']);
		$fileType = pathinfo( $file['name'], PATHINFO_EXTENSION);
		$newName =  $newName.'.'.$fileType;
		if( !in_array( $fileType, explode( ',', $types))){
			return array( '1003', '类型有误');
		}
		if( $isreplace){
			if( file_exists( $newName)){
				return array( '1005',' 文件已存在');
			}
		}
		if( $isupload){
			if( move_uploaded_file( $file['tmp_name'], $path.$newName)){
				return array( '1', '上传成功', $newName);
			}else{
				return array( '1004', '上传出错');
			}
		}
		return array( '1', '上传成功', $newName);
	}

	/**
	 * 获取文件类型
	 * @param <type> $filename 文件路径
	 * @return boolean：不支持此类型，string：返回文件类型的字符串
	 */
	static function getFileType($filename) {
		if (!is_file($filename))
			return false;
		$file = fopen($filename, "rb");
		$bin = fread($file, 2);
		fclose($file);
		$strInfo = @unpack("C*char", $bin);
		$typeCode = intval($strInfo['char1'] . $strInfo['char2']);
		$aFileType = array( 6787 => 'swf', 7087 => 'swf',7790 => 'exe', 7784 => 'midi', 8297 => 'rar', 255216 => 'jpg', 7173 => 'gif', 6677 => 'bmp', 13780 => 'png');
		return isset($aFileType[$typeCode]) ? $aFileType[$typeCode] : false;
	}
	
	/**
	 * 将支付宝/财付通返回的本站订单映射成DB订单id
	 * @param <type> $id 订单id的第1位为业务类型，第2-8位为订单日期，剩余的为DB订单id
	 * @return <type>
	 */
	static function getOrderId( $id){
	    return substr($id, 9, strlen($id) - 9);
	}
	static function creteOrderId( $orderId){
		$orderId = '0' . date(Ymh) . $orderId;
		$orderId = (string)$orderId;
		return $orderId;
	}
	
	/**
	 * 邮件发送
	 *
	 * @param unknown_type $fromName	发件人昵称
	 * @param unknown_type $address		收件人
	 * @param unknown_type $subject		发件主题
	 * @param unknown_type $body		邮件内容
	 * @return unknown
	 */
	static function sendMail( $fromName, $address, $subject, $body){
		//-- mail config --
		require_once( dirname(__FILE__).'/phpmailer/class.phpmailer.php');
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->IsHTML(true);
		$mail->CharSet = 'UTF-8';
		$mail->Host = C('EMAIL_HOST');
		$mail->SMTPAuth = true;
		$mail->Username = C('EMAIL_USERNAME');
		$mail->Password = C('EMAIL_PASSWORD');
		$mail->From = C('EMAIL_USERNAME');

		//-- send config --
		$mail->FromName = $fromName;
		$mail->AddAddress($address, "");
		$mail->Subject = $subject;
		$mail->Body = $body;
		if(!$mail->Send())
			return array(false,$mail->ErrorInfo);
		else
			return array(true);
	}
	
	/**
	 * 模拟POST数据
	 * @param unknown_type $remote_server
	 * @param unknown_type $post_string
	 */
	static function request_by_curl($remote_server,$post_string){
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$remote_server);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$post_string);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_USERAGENT,"Jimmy's CURL Example beta");
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	
	/**
	 * 验证字符串是否是EMAIL格式
	 *
	 * @param unknown_type $val	EMAIL
	 * @param unknown_type $min	EMAIL最少字符数
	 * @param unknown_type $max	EMAIL最多字符数
	 * @return unknown true or false
	 */
	static function isEmail($val,$min=0,$max=200)
	{
		if(@ereg('^[0-9a-z_]+@([0-9a-z]+\.)+[a-z]{2,}$',$val))
		{
			if($max==0)
				return true;
			$len = strlen($val);
			if($len>=$min && $len<=$max)
				return true;
			else
				return false;
		}
		return false;
	}
	
	/**
	 * 验证手机号是否正确
	 * @param unknown_type $num	号码
	 */
	static function isMobile( $num){
		return (bool)preg_match( "/^13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/", $num);
	}
	
	/**
	 * 数组数据分页化截取读取(放入所有数据)
	 *
	 * @param unknown_type $arrData		一维数组数据（顺序数字索引）
	 * @param unknown_type $pageSize	每页记录
	 * @param unknown_type $page		当前页
	 * @param unknown_type $searchMode	搜索模式（索值式）
	 * @return array('aList'=>array(array(,),),
	 *		 'pageInfo'=>array('rows'=>rows, 'page'=>page, 'pages'=>pages, 'aPage'=>array(page1,), ))
	 */
	static function arrayDataPages( $arrData, $pageSize, $page){
		if( !max(0, (int)$pageSize)){
			return false;
		}
		
		//-- S数据准备 --
		$page = max(0, (int)$page);
		$page = !$page ? 1:$page;
		$rows = count($arrData);
		$pages = (int)ceil($rows/$pageSize);
		$page = $page>$pages ? $pages : $page;
		$baseurl = preg_replace("/#.+$|p=[0-9]+/",'',$_SERVER['REQUEST_URI']);
		$baseurl = $baseurl.(strpos($baseurl,'?')?'':"?");
		$baseurl = preg_replace("/&{1,}/",'&',$baseurl);
		//-- E数据准备 --
		
		//-- S截取计算 --
		$aList = array();
		$start = ($page-1)*$pageSize;
		$end = $start + $pageSize;
		for( $i=$start;$i<$end && isset($arrData[$i]);$i++){
			$aList[] = $arrData[$i];
		}
		//-- E截取计算 --
		
		//-- S分页计算 --
		if( count($aList)>0){
			$aPage = array();
			$aPage[] = 1;
			if( $pages>=2){
		  		if( !in_array( 2, $aPage)) $aPage[] = 2;
		   		if( !in_array( $page-1, $aPage) && $page-1>0) $aPage[]=$page-1;
		   		if( !in_array( $page, $aPage)) $aPage[]=$page;
		    	if( !in_array( $page+1, $aPage) && $page+1<=$pages) $aPage[]=$page+1;
		    	if( !in_array( $pages-1, $aPage) ) $aPage[]=$pages-1;
		    	if( !in_array( $pages, $aPage)) $aPage[]=$pages;
			}
			$pageInfo = array('rows'=>$rows, 'page'=>$page, 'pages'=>$pages, 'aPage'=>$aPage, 'baseurl'=>$baseurl);
		}else{
			$pageInfo = false;
		}
		//-- E分页计算 --
		
		$aListAndPageInfo = array( 'aList'=>$aList, 'pageInfo'=>$pageInfo);
		return $aListAndPageInfo;
	}
	
	/**
	 * RC通用分页
	 * @param unknown_type $rows
	 * @param unknown_type $pageSize
	 * @param unknown_type $page
	 * @return Ambigous <boolean, multitype:number mixed Ambigous <number, unknown> multitype:number Ambigous <number, unknown>  >
	 */
	static function pageInfo( $rows, $pageSize, $page){
		//-- S数据准备 --
		$pageSize = self::uint( $pageSize) ? $pageSize:C('PAGESIZE');
		$page = max(0, (int)$page);
		$page = !$page ? 1:$page;
		$rows = max(0, (int)$rows);
		$pages = (int)ceil($rows/$pageSize);
		$page = $page>$pages ? $pages : $page;
		//$baseurl = eregi_replace("(#.+$|p=[0-9]+)",'',$_SERVER['REQUEST_URI']);
		//$baseurl = $baseurl.(strpos($baseurl,'?')?'':"?");
		//$baseurl = eregi_replace("(&+)",'&',$baseurl);
		//$baseurl = preg_replace("/#.+$|p=[0-9]+/",'',$_SERVER['REQUEST_URI']);
		//$baseurl = $baseurl.(strpos($baseurl,'?')?'':"?");
		//$baseurl = preg_replace("/&{1,}/",'&',$baseurl);
		$baseurl = self::filterUrl( 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		//-- E数据准备 --
	
		//-- S分页计算 --
		if( count($rows)>0){
			$aPage = array();
			$aPage[] = 1;
			if( $pages>=2){
				if( !in_array( 2, $aPage)) $aPage[] = 2;
				if( !in_array( $page-1, $aPage) && $page-1>0) $aPage[]=$page-1;
				if( !in_array( $page, $aPage)) $aPage[]=$page;
				if( !in_array( $page+1, $aPage) && $page+1<=$pages) $aPage[]=$page+1;
				if( !in_array( $pages-1, $aPage) ) $aPage[]=$pages-1;
				if( !in_array( $pages, $aPage)) $aPage[]=$pages;
			}
			$pageInfo = array('rows'=>$rows, 'page'=>$page, 'pages'=>$pages, 'aPage'=>$aPage, 'baseurl'=>$baseurl);
		}else{
			$pageInfo = false;
		}
		//-- E分页计算 --
		return $pageInfo;
	}
	
	/**
	 * 对字符串进行加密（双向）
	 *
	 * @param unknown_type $str
	 * @return unknown
	 */
	static function encryptStr( $str){
		return $str;
	}
	
	/**
	 * 对字符串进行解密（双向）
	 *
	 * @param unknown_type $str
	 * @return unknown
	 */
	static function uncryptStr( $str){
		return $str;
	}
	
	/**
	 * 获取IP所在地
	 *
	 * @param unknown_type $ip
	 * @return unknown	JSON对象
	 */
	static function get_location($ip){
	    $curl = curl_init();
	    curl_setopt($curl,CURLOPT_URL, "http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=".$ip);
	    $location = curl_exec($curl);
	    $location = json_decode($location);
	    if($location===FALSE) return "";
	    	return empty($location->desc) ? $location->province.$location->city.$location->district.$location->isp : $location->desc;
	}
	
	/**
	 * 数据格式转换
	 *
	 * @param unknown_type $data
	 * @param unknown_type $to
	 * @return unknown
	 */
	static function encoding( $data, $to){ 
		$encode_arr = array('UTF-8','ASCII','GBK','GB2312','BIG5','JIS','eucjp-win','sjis-win','EUC-JP'); 
		$encoded = mb_detect_encoding($data, $encode_arr); 
		$data = mb_convert_encoding($data,$to,$encoded); 
		return $data;
	}
	
	/**
	 * HTML代码过滤器
	 * @param $value	要过滤的值，可以是数组
	 * @return $value , array()
	 */
	static function addslashes_filter( $value, $mode=false){

		$filter = array(
		'/position[ ]*:[ ]*absolute/',
		'/position[ ]*:[ ]*fixed/',
		'/position[ ]*:[ ]*relative/',
		'/position[ ]*:[ ]*inherit/',
		'/<script.*>/',
		'/<\/script>/',
		"/\n/",
		"/  /",
		"/              /",
		);
		if( is_array($mode)){
			$filter = array_merge( $filter, $mode);
		}
		$count = count($filter);
		$replace = array();
		for( $i=0;$i<$count;$i++){
			$replace[$i] = '';
		}
		if( empty($value)){
			return $value;
		}else{
			if( is_array($value) ){
				return array_map('addslashes_filter', $value);
			}else{
				$value = preg_replace($filter, $replace, $value);
				return $value;
			}
		}
	}
	
	/**
	 * 去除字符串右侧可能出现的乱码
	 * @param   string      $str        字符串
	 * @return  string
	 */
	static function trimRight($str){
		$len = strlen($str);
		
		/* 为空或单个字符直接返回 */
		if ($len == 0 || ord($str{$len-1}) < 127){
			return $str;
		}
		
		/* 有前导字符的直接把前导字符去掉 */
		if (ord($str{$len-1}) >= 192){
			return substr($str, 0, $len-1);
		}
		
		/* 有非独立的字符，先把非独立字符去掉，再验证非独立的字符是不是一个完整的字，不是连原来前导字符也截取掉 */
		$r_len = strlen(rtrim($str, "\x80..\xBF"));
		if ($r_len == 0 || ord($str{$r_len-1}) < 127){
			return sub_str($str, 0, $r_len);
		}
		$as_num = ord(~$str{$r_len -1});
		if ($as_num > (1<<(6 + $r_len - $len))){
			return $str;
		}
		else{
			return substr($str, 0, $r_len-1);
		}
	}
	
	/**
	 * 获得用户的真实IP地址
	 *
	 * @access  public
	 * @return  string
	 */
	static function realIp(){
		static $realip = NULL;

		if ($realip !== NULL){
			return $realip;
		}

		if (isset($_SERVER)){
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
				$arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

				/* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
				foreach ($arr AS $ip){
					$ip = trim($ip);

					if ($ip != 'unknown'){
						$realip = $ip;
						break;
					}
				}
			}
			elseif (isset($_SERVER['HTTP_CLIENT_IP'])){
				$realip = $_SERVER['HTTP_CLIENT_IP'];
			}
			else{
				if (isset($_SERVER['REMOTE_ADDR'])){
					$realip = $_SERVER['REMOTE_ADDR'];
				}
				else{
					$realip = '0.0.0.0';
				}
			}
		}
		else{
			if (getenv('HTTP_X_FORWARDED_FOR')){
				$realip = getenv('HTTP_X_FORWARDED_FOR');
			}
			elseif (getenv('HTTP_CLIENT_IP')){
				$realip = getenv('HTTP_CLIENT_IP');
			}
			else{
				$realip = getenv('REMOTE_ADDR');
			}
		}
		preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
		$realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';

		return $realip;
	}
	
	/**
	 * 无乱码中文字符截取
	 * @param $string	字符串
	 * @param $start	开始位置
	 * @param $length	截取长度
	 */
	static function mySubstr($string,$start,$length){
		if(strlen($string)>$length){
			$str = null;
			$len = $start+$length;
			for($i=$start;$i<$len;$i++){
				if(ord(substr($string,$i,1))>0xa0){
					$str.= substr($string,$i,3);
					$i+=2;
				}
				else{
					$str.= substr($string,$i,1);
				}
			}
			return $str;
		}
		else{
			return $string;
		}
	}
	
	/**
	 * 解析xml
	 * @param $tag	标签名
	 * @param $doc	XML对象
	 */
	static function analysisXml($tag,$doc){
		$str = '';
		if( ! empty($doc->getElementsByTagName( $tag )->item(0)->nodeValue) ) {
			$str= $doc->getElementsByTagName( $tag )->item(0)->nodeValue;
			//乱码解决，如果出现乱码请把下面一行代码注释掉
			//$str = charsetEncode($str,'gbk','改变312');
		}
		return $str;
	}
	
	/**
	 * 格式化商品价格
	 *
	 * @access  public
	 * @param   float   $price  商品价格
	 * @return  string
	 */
	static function price_formats($price, $change_price = true,$case){
	    if ($change_price && defined('ECS_ADMIN') === false){
	        switch ($case){
	            case 0:
	                $price = number_format($price, 2, '.', '');
	                break;
	            case 1: // 保留不为 0 的尾数
	                $price = preg_replace('/(.*)(\\.)([0-9]*?)0+$/', '\1\2\3', number_format($price, 2, '.', ''));
	                if (substr($price, -1) == '.'){
	                    $price = substr($price, 0, -1);
	                }
	                break;
	            case 2: // 不四舍五入，保留1位
	                $price = substr(number_format($price, 2, '.', ''), 0, -1);
	                break;
	            case 3: // 直接取整
	                $price = intval($price);
	                break;
	            case 4: // 四舍五入，保留 1 位
	                $price = number_format($price, 1, '.', '');
	                break;
	            case 5: // 先四舍五入，不保留小数
	                $price = round($price);
	                break;
	        }
	    }
	    else{
	        $price = number_format($price, 2, '.', '');
	    }

	    return sprintf($GLOBALS['CFG']['currency_format'], $price);
	}
	
	/**
	 * 递归方式的对变量中的特殊字符进行转义
	 *
	 * @access  public
	 * @param   mix     $value
	 *
	 * @return  mix
	 */
	static function addslashes_deep($value){
	    if (empty($value)){
	        return $value;
	    }
	    else{
	        return is_array($value) ? array_map('addslashes_deep', $value) : addslashes($value);
	    }
	}
	
	/**
	 * 根据EMAIL判断EMAIL的官方登录地址以及名字
	 * @param $email
	 */
	static function email_type( $email){
		if( preg_match( '/[^@].@qq.com$/', $email)){
			$arr = array('type'=>'QQ', 'url'=>'http://mail.qq.com');
		}elseif( preg_match( '/[^@].@163.com$/', $email)){
			$arr = array('type'=>'网易', 'url'=>'http://mail.163.com/');
		}elseif( preg_match( '/[^@].@gmail.com$/', $email)){
			$arr = array('type'=>'谷哥', 'url'=>'http://www.gmail.com');
		}elseif( preg_match( '/[^@].@126.com$/', $email)){
			$arr = array('type'=>'网易', 'url'=>'http://mail.163.com/');
		}elseif( preg_match( '/[^@].@sina.com$/', $email)){
			$arr = array('type'=>'新浪', 'url'=>'http://mail.sina.com.cn');
		}elseif( preg_match( '/[^@].@yahoo.com.cn$/', $email)){
			$arr = array('type'=>'', 'url'=>'http://mail.yahoo.com.cn');
		}elseif( preg_match( '/[^@].@yahoo.cn$/', $email)){
			$arr = array('type'=>'', 'url'=>'http://mail.yahoo.cn');
		}elseif( preg_match( '/[^@].@sohu.com$/', $email)){
			$arr = array('type'=>'', 'url'=>'http://mail.sohu.com');
		}elseif( preg_match( '/[^@].@yeah.net$/', $email)){
			$arr = array('type'=>'', 'url'=>'http://mail.yeah.net');
		}elseif( preg_match( '/[^@].@139.com$/', $email)){
			$arr = array('type'=>'', 'url'=>'http://mail.139.com');
		}elseif( preg_match( '/[^@].@tom.com$/', $email)){
			$arr = array('type'=>'', 'url'=>'http://mail.tom.com');
		}elseif( preg_match( '/[^@].@21cn.com$/', $email)){
			$arr = array('type'=>'', 'url'=>'http://mail.21cn.com');
		}elseif( preg_match( '/[^@].@hotmail.com$/', $email)){
			$arr = array('type'=>'', 'url'=>'http://www.hotmail.com');
		}elseif( preg_match( '/[^@].@foxmail.com$/', $email)){
			$arr = array('type'=>'', 'url'=>'http://www.foxmail.com');
		}else{
			$arr = array('type'=>'', 'url'=>'###');
		}
		
		return $arr;
	}
	
	/**
	 *　XOR加密
	 * @param unknown_type $string
	 * @param unknown_type $key
	 */
	static function myEncrypt($string, $key){
		for($i=0;$i<strlen($string);$i++){
			for($j=0;$j<strlen($key);$j++){
				$string[$i] = $string[$i]^$key[$j];
			}
		}
		return $string;
	}
	
	/**
	 * 获取当前页
	 */
	static function requestUri(){
		if (isset($_SERVER['REQUEST_URI'])){
			$uri = $_SERVER['REQUEST_URI'];
		}
		else{
			if (isset($_SERVER['argv'])){
				$uri = $_SERVER['PHP_SELF'] .'?'. $_SERVER['argv'][0];
			}
			else{
				$uri = $_SERVER['PHP_SELF'] .'?'. $_SERVER['QUERY_STRING'];
			}
		}
		return $uri;
	}
	
	/**
	 * 判断URL是否有参数
	 */
	static function isHaveQuery(){
		if( strpos( $_SERVER['REQUEST_URI'], '?')){
			return true;
		}
		return false;
	}
	
	/**
	 * 对URL过滤重复值
	 * @param unknown_type $url		地址
	 * @param $cancelEmpty			是否取消空值
	 * @param unknown_type $isJump	是否跳转
	 */
	static public function filterUrl( $url, $cancelEmpty=false, $isJump=false){
		$url = parse_url( $url);
		parse_str( $url['query'], $url['query']);
		if( $cancelEmpty) foreach( $url['query'] as $k=>$v) if( empty($v)) unset($url['query'][$k]);
		$url['query'] = ($url['query']=http_build_query($url['query'])) ? '?'.$url['query']:'';
		$url = empty($url['scheme']) ? "{$url['path']}{$url['query']}":"{$url['scheme']}://{$url['host']}{$url['path']}{$url['query']}";
		if( $isJump) header($url);
		return $url;
	}
	
	/**
	 * XOR解密
	 * @param unknown_type $string
	 * @param unknown_type $key
	 */
	static function myDecrypt($string, $key){
		for($i=0;$i<strlen($string);$i++){
			for($j=0;$j<strlen($key);$j++){
				$string[$i] = $key[$j]^$string[$i];
			}
		}
		return $string;
	}
	
	/**
	 * 获取文件目录列表
	 * @param unknown_type $dir
	 * $type	类型(1目录、2文件、3目录跟文件)
	 * $option  是否读取隐藏.文件(true是)
	 */
	static function getDir($dir, $type, $option=false) {
		$basedir = basename($dir);
		$list[$basedir] = NULL;
		$list = NULL;
		if (false != ($handle = opendir ( $dir ))) {
			$i=0;
			if( $type=='1'){
				while (false!==($file=readdir($handle))) {
					if( (!preg_match('/^\..*$/i', $file) || $option) && is_dir($dir.'/'.$file)){
						$list[$i]=$file;$i++;
					}
				}
			}else if( $type=='2'){
				while (false!==($file=readdir($handle))) {
					if( (!preg_match('/^\..*$/i', $file) || $option) && !is_dir($dir.'/'.$file)){
						$list[$i]=$file;$i++;
					}	
				}
			}else if( $type=='3'){
				while (false!==($file=readdir($handle))) {
					if( !preg_match('/^\..*$/i', $file) || $option){
						if( is_dir( $dir.'/'.$file) && $file!='.' && $file!='..'){
							$list[$file] = self::getDir( $dir.'/'.$file, 3);
						}
						else{
							if( !preg_match('/^_.*/i', $file))
								$list[]=$file;
						}
						$i++;
					}
				}
			}
			closedir ( $handle );
		}
		return $list;
	}
	
	/**
	 * 字节格式化 把字节数格式为 B K M G T 描述的大小
	 * @param unknown_type $size
	 * @param unknown_type $dec
	 * @return string
	 */
	static function byte_format($size, $dec=2){
		$a = array("B", "KB", "MB", "GB", "TB", "PB");
		$pos = 0;
		while ($size >= 1024) {
			$size /= 1024;
			$pos++;
		}
		return round($size,$dec)." ".$a[$pos];
	}
	
	/**
	 * GET UUID
	 * @param unknown_type $prefix
	 * @return string
	 */
	static function uuid($prefix = ''){
		$chars = md5(uniqid(mt_rand(), true));
		$uuid  = substr($chars,0,8);
		$uuid .= substr($chars,8,4);
		$uuid .= substr($chars,12,4);
		$uuid .= substr($chars,16,4);
		$uuid .= substr($chars,20,12);
		return $prefix . $uuid;
	}
}