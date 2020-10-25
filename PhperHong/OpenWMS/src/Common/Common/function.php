<?php
// +----------------------------------------------------------------------
// | openWMS (开源wifi营销平台)
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2025 http://cnrouter.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.gnu.org/licenses/gpl-2.0.html )
// +----------------------------------------------------------------------
// | Author: PhperHong <phperhong@cnrouter.com>
// +----------------------------------------------------------------------
	function DD($name){
		return D('admin/' . $name);
	}
	function user_name($auth_type, $username){
		if ($auth_type == 'akey_verify' || $auth_type == 'weixin_verify'){
            return '无';
        }else{
        	return $username;
        }
	}
	function auth_typeFiler($auth_type){
		$array = array(
        	'akey_verify'=>'一键认证', 
        	'weixin_verify'=>'微信认证', 
        	'mobile'=>'短信认证', 
        	'virtualmobile'=>'虚拟短信认证', 
        	'qq'=>'QQ认证', 
        	'weibo'=>'微博认证',
        );
        return $array[$auth_type];
	}
	function device_typeFiler($device_type){
		$array = array(
        	'Phone'=>'手机', 
        	'computer'=>'电脑', 
        	'Tablet'=>'平板电脑', 
        	
        );
        return $array[$device_type];
	}
	function devices_cjFiler($devices_cj){
		$data = array(
			'unkown'=>'未知', 
            'iPhone'=>'苹果', 
            'GenericPhone'=>'诺基亚', 
            'Samsung'=>'三星',
            'HTC'=>'HTC',
            'iPad'=>'iPad'
		);
		if (!$data[$devices_cj]){
			return '未知';
		}
		return $data[$devices_cj];
            
	}
	//转换时间
	function secondesToDay($time){
		$time = intval($time);
		if ($time == 0){
			return '0';
		}
		$str = '';
        //获取天数
        $day = floor($time/86400);

        $str .= $day > 0 ? $day . '天 ' : '';

        //获取小时 
        $hours = floor(($time % 86400) / 3600);
        $str .= $hours > 0 ? $hours . '小时 ' : '';

        //获取分钟
        $minutes = floor(($time % 86400 % 3600) / 60); 
        $str .= $minutes > 0 ? $minutes . '分 ' : '';
  
        //获取秒
        $seconds = floor(($time % 36400 % 3600 % 60));
        $str .= $seconds > 0 ? $seconds . '秒' : '';
        return $str;  
	}
 	//转换流量
 	function Bytes($input, $type='B'){

            if ($type == 'B'){
                if ($input < 1024){
                    return $input.'B';
                }else if($input >= 1024 && $input < 1048576){
                   return round($input/1024, 1) . 'KB' ; 
                }else if($input >= 1048576 && $input < 1073741824){
                   return round(input/1048576, 1).'MB' ; 
                }else if($input >= 1073741824 && $input < 1099511627776){
                   return round($input/1073741824, 1).'GB' ; 
                }else{
                    return '';
                }
            }else if ($type == 'KB'){
                if ($input < 1024){
                    return $input.'KB';
                }else if($input >= 1024 && $input < 1048576){
                   return round($input/1024, 1).'MB' ; 
                }else if($input >= 1048576 && $input < 1073741824){
                   return round($input/1048576, 1).'GB' ; 
                }else{
                    return '';
                }
            }else if($type == 'MB'){

                if ($input < 1024){
                    return $input.'MB';
                }else if($input >= 1024 && $input < 1048576){
                   return round($input/1024).'GB' ; 
                }else{
                    return '';
                }
            }
 	}

	//发送短信
	function sendSms($mobile, $content){
		$username = C('SMS_USER');
        $password = C('SMS_PASSWORD');
        $is_multi = is_array($mobile);
        $fields = array(
            'user'  	=> $username,
            'pass'    	=> $password,
            'phone'  	=> $is_multi ? join(';', $mobile) : $mobile,
            'content' 	=> $content,
        );
        $uri = "http://www.cl10086.com/garden/interface/sendSMS.action";
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $uri );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );
        $data = curl_exec ( $ch );
        curl_close ( $ch );
        $data = explode('#', $data);
        if ($data[0] == 0){
            return true;
        }
        $err = array(
        	'-1'	=> '验证失败',
        	'-2'	=> '系统异常',
        	'-3'	=> '用户所使用的通道被禁用',
        	'-4'	=> '用户未配置通道',
        	'1'		=> '短信内容为空',
        	'2'		=> '内容超长  最长300字',
        	'3'		=> '内容包含敏感词',
        	'4'		=> '发送号码为空',
        	'5'		=> '号码不符合规则',
        	'6'		=> '余额不足',
        );
        return $err[$data[0]];
	}
  	function multi_array_sort($multi_array,$sort_key,$sort=SORT_ASC){ 
		if(is_array($multi_array)){ 
			foreach ($multi_array as $row_array){ 
				if(is_array($row_array)){ 
					$key_array[] = $row_array[$sort_key]; 
				}else{ 
					return false; 
				} 
			} 
		}else{ 
			return false; 
		} 
		array_multisort($key_array,$sort,$multi_array); 
		return $multi_array; 
	} 

	function cutstr($sourcestr, $startlength, $cutlength){
	   $returnstr='';
	   $i=0;
	   $n=0;
	   $str_length=strlen($sourcestr);            //字符串的字节数
	   while (($n<$cutlength) and ($i<=$str_length))
	   {
		  $temp_str=substr($sourcestr,$i,1);
		  $ascnum=Ord($temp_str);               //得到字符串中第$i位字符的ascii码
		  if ($ascnum>=224) {                  //如果ASCII位高与224，
			 $returnstr=$returnstr.substr($sourcestr,$i,3);  //根据UTF-8编码规范，将3个连续的字符计为单个字符        
			 $i=$i+3;                           //实际Byte计为3
			 $n++;                             //字串长度计1
		  } elseif ($ascnum>=192){              //如果ASCII位高与192，
			 $returnstr=$returnstr.substr($sourcestr,$i,2);  //根据UTF-8编码规范，将2个连续的字符计为单个字符
			 $i=$i+2;                           //实际Byte计为2
			 $n++;                            //字串长度计1
		  } elseif ($ascnum>=65 && $ascnum<=90){       //如果是大写字母，
			 $returnstr=$returnstr.substr($sourcestr,$i,1);
			 $i=$i+1;                           //实际的Byte数仍计1个
			 $n++;                            //但考虑整体美观，大写字母计成一个高位字符
		  } else {                              //其他情况下，包括小写字母和半角标点符号，
			 $returnstr=$returnstr.substr($sourcestr,$i,1);
			 $i=$i+1;                           //实际的Byte数计1个
			 $n=$n+0.5;                        //小写字母和半角标点等与半个高位字符宽...
		  }
		 
	   if ($n <= $startlength){
		$returnstr = '';
		continue;
	   }
		}
	   
		if ($str_length>$cutlength){
		   $returnstr = $returnstr . "";          //超过长度时在尾处加上省略号
		}
		return strip_tags(trim($returnstr));
	}

	

	
	/*
	*将数组中的某个字段转换成字符串，按特殊符号分割
	*@param array $Array 要转换的数组 [必须]
	*@param string $Field 需要转换的字段 [必须]
	*@param string $Separator 分隔符 默认为逗号 [可选]
	*@param int $strat 循环数组开始位置 默认为0 [可选]
	*@param int $length 循环数组结束位置 默认为数组长度 [可选]
	*@return string $String 
	*/
	function ArrayToString($Array , $Field , $Separator = ',' , $strat = 0 ,$length = 0 , $isString = false){
		if ($length == 0)$length = count($Array);
		$String = '';
		$temp = array();
		//获取值并去重复
		for($i = $strat ; $i < $length ; $i ++){
			if ($Array[$i][$Field] != ''){
				$temp[$Array[$i][$Field]] = true;
			}
		}
		
		
		//合并值	
		foreach($temp as $key => $val){
			if ($isString){
				$key = "'" . $key . "'";	
			}
			$String .= $key . $Separator ;		
		}
	
		$String = rtrim($String , $Separator);
		return repeat($String);
	}
	//
	function _ArrayToString($Array , $Field , $Separator = ',' , $strat = 0 ,$length = 0){
		if ($length == 0 )$length = count($Array);
		$String = "";
		for($i = $strat ; $i < $length ; $i ++){
			if ($i != 0) $String .= $Separator  ;	
			$String .= "'".$Array[$i][$Field]."'" ;	
		}	
		return $String;
	}
	
	/*
	 *用于将一个数据数组中的某个字段重新组成数组(多数组)
	*@param array $Array 要转换的数组
	*@param $Field 要组成数组的字段
	*@return array
	*/
	function ArrayDimensionReduction($Array, $Field, $new_name){
		$NewArray = array();
		foreach($Array as $value){
			if (!empty($new_name)){
				$NewArray[$value[$Field]][$new_name][] = $value;
			}else{
				$NewArray[$value[$Field]][] = $value;
			}
		}
		return $NewArray;
	}
	/**
	*	随机数
	*@param $length 随机数长度
	*/
	function random($length) {
		$hash = '';
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
		$max = strlen($chars) - 1;
		mt_srand((double)microtime() * 1000000);
		for($i = 0; $i < $length; $i++) {
			$hash .= $chars[mt_rand(0, $max)];
		}
		return $hash;
	}
	/*
	*用于将一个数据数组的索引转换成数组中的某个字段
	*@param array $Array 要转换的数组
	*@param $Field 要被当作索引的字段
	*@return array
	*/
	function ArraySetIndex($Array , $Field){
		$NewArray = array();
		foreach($Array as $value){
			$NewArray[$value[$Field]] = $value;	
		}
		return $NewArray;
	}
	/*
	*用于将一个数据数组的索引转换成数组中的某个字段(多数组)
	*@param array $Array 要转换的数组
	*@param $Field 要被当作索引的字段
	*@return array
	*/
	function ArraySetIndexMoer($Array , $Field){
		$NewArray = array();
		foreach($Array as $value){
			$NewArray[$value[$Field]][] = $value;	
		}
		return $NewArray;
	}
	/**
	*	将子数组组合进父数组中，该函数只支持二维数组 ，子数组的索引必须是关联字段
	*@param array $ParentArray 父级数组
	*@param array $SubArray 子级数组
	*@param string $AssField 关联字段
	*@param string $NewField 组合进父级数组中的新字段名称(如果$isArray为false的话，该值可以为多个，以逗号隔开，个数必须与$subField相同)
	*@param bool $isArray 是否将子数组中的数据以数组的方式存储在父级数组中，如果为false，则将子数组中的具体字段取出存储到父级数组中，默认为 true 
	*@param string $subField 如果$isArray 为false，该值不能为空，这个值是确定取子数组中哪个字段中的值，默认为空(如果$isArray为false的话，该值可以为多个，以逗号隔开，个数必须与$NewField相同)
	*/
	function CombArray($ParentArray , $SubArray , $AssField , $NewField , $isArray=true , $subField = '' ){
		if (!is_array($ParentArray) || !is_array($SubArray)){
			return 'ParentArray or SubArray 不是一个有效的数组';	
		}
		if ($NewField == '' || $AssField == '' ){
			return '关联字段或者 新字段名称为空';
		}
		
		
		for($i = 0 ; $i < count($ParentArray) ; $i++){
			if(!$isArray){
				$val=$SubArray[$ParentArray[$i][$AssField]];
				if($subField != ''){
					$temp = explode(',' , $subField);
					$temp1 = explode(',' , $NewField);
					for($t = 0 ; $t < count($temp) ; $t++){
						$ParentArray[$i][$temp1[$t]] = $SubArray[$ParentArray[$i][$AssField]][$temp[$t]];	
					}
				}else{
					$ParentArray[$i][$NewField] = $val;
				}
			}else{
				$ParentArray[$i][$NewField] = $SubArray[$ParentArray[$i][$AssField]];			
			}
		}	
		
		
		return $ParentArray;
	}
	/**
	*	用户登陆密码处理
	*@param string $pwd 明文密码
	*@param string $rand 随机数
	*/
	function remixPwd($pwd){
		$pwd = md5($pwd.substr($pwd,strlen($pwd)-4,strlen($pwd)));
		return $pwd;
	}
	/**
	*	注册、添加用户密码处理
	*@param string $pwd 明文密码
	*@param string $rand 随机数
	*/
	function insertPwd($pwd){
		$rand = random(4);
		$array = array('upw' => remixPwd($pwd,$rand),'rand' => $rand);
		return $array;
	}
	/**
	*	根据生日计算年龄
	*@param string $birthday 出生年月日
	*/
	function getAge($birthday){
		$age = date('Y', time()) - date('Y', strtotime($birthday)) - 1;
		if (date('m', time()) == date('m', strtotime($birthday))){
		
			if (date('d', time()) > date('d', strtotime($birthday))){
			$age++;
			}
		}elseif (date('m', time()) > date('m', strtotime($birthday))){
			$age++;
		}
		return $age;
	}
	
	 /**
	  * 获取当前id的子ID
	  * @param array $data 原始数组
	  * @param int $id 当前id
	  * @param int $layer 当前层级
	  */
	 function classify_tree($data, $pid = 0, $level = 0)
	 {
		 if($level == 10) break;
		 $l        = $level*15;
		 $color = array('green', "orange", 'pink', 'green');
		// $l        = $pid == 0 ? $l : $l.'└';
		 static $arrcat    = array();
		 $arrcat    = empty($level) ? array() : $arrcat;
		 foreach($data as $k => $row)
		 {
		 	
		 	 /**
			  * 如果父ID为当前传入的id
			  */
			 if($row['fid'] == $pid)
			 {
				//如果当前遍历的id不为空
				 $row['classify_name']   = $row['classify_name'];
				 $row['color']    = $color[$level];
				 $row['str_repeat'] = $l;
				 $arrcat[]    = $row;
				 classify_tree($data, $row['id'], $level+1);//递归调用
			 }
		 }
		 return $arrcat;
	 }
 
	/*
	*	去出字符串里面的重复字段
	*/
	 function repeat($pidarray){
		$pidarray = explode( ',', $pidarray); //*
		$pidarray = array_unique($pidarray);  //*去掉重复PID, 减少查询次数
		$pidarray = implode( ',', $pidarray); //*
		return $pidarray;	
	}
	/**
	 +----------------------------------------------------------
	 * 把返回的数据集转换成Tree
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param array $list 要转换的数据集
	 * @param string $pid parent标记字段
	 * @param string $level level标记字段
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	 */
	function list_to_tree($list, $pk='id',$pid = 'pid',$child = '_child',$root=0)
	{
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
	//时间戳转日期
	function toDate($time, $format = 'm-d H:i:s') {
		if (empty ( $time )) {
			return '';
		}
		$format = str_replace ( '#', ':', $format );
		return date ($format, $time );
	}
	function deleteHtmlTags($string, $br = false)
    {
        while(strstr($string, '>'))
        {
            $currentBeg = strpos($string, '<');
            $currentEnd = strpos($string, '>');
            $tmpStringBeg = @substr($string, 0, $currentBeg);
            $tmpStringEnd = @substr($string, $currentEnd + 1, strlen($string));
            $string = $tmpStringBeg.$tmpStringEnd;
        }
        return $string;
    }
	/**
	 +----------------------------------------------------------
	 * 对查询结果集进行排序
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param array $list 查询结果
	 * @param string $field 排序的字段名
	 * @param array $sortby 排序类型
	 * asc正向排序 desc逆向排序 nat自然排序
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
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
	 +----------------------------------------------------------
	 * 在数据列表中搜索
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param array $list 数据列表
	 * @param mixed $condition 查询条件
	 * 支持 array('name'=>$value) 或者 name=$value
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
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
					}elseif($data[$field] == $value){
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
	 +----------------------------------------------------------
	 * 转换时间格式
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param date 需要转换的时间
	 +----------------------------------------------------------
	 * @return date 转换后的时间
	 +----------------------------------------------------------
	 */
	 function conversionDate($date){
	 	$DATETIME 	  = C('DATETIME');
		//print_r($DATETIME);
		//exit;
		//拆分时间，组合成系统时间格式
		$systemtime = explode(' ',$date);
		$systemtime = explode('-',$systemtime[0]);
		if (substr($systemtime[1],0,1) != '0' && intval($systemtime[1])<10){
			$systemtime[1] = '0'.$systemtime[1];	
		}
		$systemtime = $systemtime[2] . ' ' . $DATETIME[$systemtime[1]] . ' ' . $systemtime[0];
		return $systemtime;
	 }
	 
	 /**
	 +----------------------------------------------------------
	 * 对象转数组
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $object 需要转换的对象
	 +----------------------------------------------------------
	 * @return $result 转换后的数组
	 +----------------------------------------------------------
	 */
	 function objectToArray($object){
		$result = array();
		$object = is_object($object) ? get_object_vars($object) : $object;	
		foreach ($object as $key => $val) {
			$val = (is_object($val) || is_array($val)) ? objectToArray($val) : $val;		
			$result[$key] = $val;		
		}	
		return $result;	
	}
	/**
	 +----------------------------------------------------------
	 * 定制SOAP请求客户端
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * array $data       提交到服务器的参数一定是数组格式
	 +----------------------------------------------------------
	 */
	 function setSoapClient($data){
		$config = C('SOAP');
		$options=array(  
			'uri'=>$config['uri'],  
			'location'=>'http://'.$data['url'], //注意: 这个location指定的是server端代码在服务器中的具体位置
			'trace'=>true,  
		); 
		try{
			$client = new SoapClient(null, $options);
			$header =new SoapHeader($data['url'], 'auth', $config['soapkey'], false, SOAP_ACTOR_NEXT);
			$client->__setSoapHeaders(array($header));
			$wsdl_array = $data['data'];
			$wsdl_array = json_encode($wsdl_array);
			$output = $client->__soapCall($data['functions'], array($wsdl_array));
			$output = json_decode($output, true); 
		}catch(Exception $e){
			return 	array('status'=>0,'info'=>'通讯地址错误！' . $e->getMessage());
		}
		return $output;
	 }
	 /**
     +----------------------------------------------------------
     * 获取文件内容并以数组的形式返回
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
	 * @param file 需要打开的文件
     +----------------------------------------------------------
     * @return array

     +----------------------------------------------------------
     */	
	function getConfigId($file){
		//获取信息
		$info = file_get_contents($file);
		$info = explode("\n",$info);
		$tempArray = array();
		foreach($info as $key => $val){
			if ($val != ''){
				$temp = explode('=',$val);
				$tempArray[trim($temp[0])] = trim($temp[1]);
			}	
		}
		return $tempArray;	
	}
	
	/**
	 +----------------------------------------------------------
	 * 转换流量单位
	 +----------------------------------------------------------
	 * @param $z 要转换的值
	 +----------------------------------------------------------
	 * @return string $str 
	 +----------------------------------------------------------
	 * @说明 $z必须是从0-N的正整数
	 +----------------------------------------------------------
	 */
	function getMB($z){
		if($z<1024){
			$str =round($z,2).' B';	 
		}else if(1048576 >$z && $z > 1024){
			$str =round($z/1024 , 2).' KB';	
		}else if(1073741824 > $z && $z > 1048576){
			$str =round($z/1048576 ,2).' MB'; 
		}else if(1099511627780 >$z && $z > 1073741824){
			$str =round($z/1073741824 ,2 ).' GB'; 
		}else{
			$str =round($z/1099511627780 ,2).' TB'; 
		}
		return $str;	
	 }
	/**
	 +----------------------------------------------------------
	 * 导出Excel报表
	 +----------------------------------------------------------
	 * @param $title 表头的字段（以逗号分隔开），$result 报表数据集
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return void
	 +----------------------------------------------------------
	 * @说明 $result键的名称必须是从0-N的正整数
	 +----------------------------------------------------------
	 */
	 function downToExcel($title,$result,$head){
		//导入PHPExcel类库
		import("Org.PExcel.PExcel");
        import("Org.PExcel.PHPExcel.Writer.Excel5");
		//将表头字段转换为数组
		$title = explode(',',$title);
		// Create new PHPExcel object  
		$objPHPExcel = new PExcel();  
		
		// Set properties  
		$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");  
		
		$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");  
		
		$objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");  

		// Add some data  
		
		$objPHPExcel->setActiveSheetIndex(0);  
		
		//定义EXCEL表头字段数组，一般这几个就够了
		$cell = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N');
		
		//循环设置EXCEL表头
		for($i=0;$i<count($title);$i++){
			$objPHPExcel->getActiveSheet()->SetCellValue($cell[$i].'1',$title[$i]);
			$objPHPExcel->getActiveSheet()->getColumnDimension($cell[$i])->setWidth(20);
		}
		
		//重新排序数组下标
		$result = array_merge($result);

		//遍历数据集
		foreach($result as $k => $v){	
			//遍历表头字段
			foreach($cell as $kk => $vv){
				if($v[$kk] !== NULL){
					//填充数据
					$objPHPExcel->getActiveSheet()->SetCellValue($vv.($k+2), deleteHtmlTags($v[$kk]));	
				}
			}
		}
		// Rename sheet  

		$objPHPExcel->getActiveSheet()->setTitle($head);  
		
		// Save Excel 2007 file  
	
		//$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);  
		$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel); 

		header("Content-type: text/csv");//重要

		header("Pragma: public");  
		
		header("Expires: 0");   
		
		header("Cache-Control:must-revalidate,post-check=0,pre-check=0");  
		
		header("Content-Type:application/force-download");  
		
		header("Content-Type:application/vnd.ms-execl");  
		
		header("Content-Type:application/octet-stream");  
		
		header("Content-Type:application/download");  
		
		header("Content-Disposition:attachment;filename=".iconv('utf-8','gbk',$head).".xls");  
		
		header("Content-Transfer-Encoding:binary");  
		
		$objWriter->save("php://output");  
	}
        
  
      
    
    
    
    /**
      +----------------------------------------------------------
     * 添加水印
      +----------------------------------------------------------
     * @param  $spath  打水印的图片
     * @param  @$topath 水印图片保存路径
     * @param  @$wpath 水印图
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return array
      +----------------------------------------------------------
     */
      function water($spath, $topath, $wpath) {
        import('@.ORG.WaterMask');
        //打水印
        $obj = new WaterMask($spath);
        $obj->waterType = 1;
        //水印保存名
        $obj->toName = $topath;
        //居中
        $obj->pos = 5;
        //水印图片
        $obj->waterImg = $wpath;
        $obj->output();
    }
    
    
 /**
 *字符串替换规则
 */         
function  replace_rual($string){
        $string=trim($string);
        if(empty($string)){
            return $string;
        }
        $string =  str_replace("'", "", $string);
        $string =  str_replace("#", "", $string);
        $string =  str_replace(";", "", $string);
        $string =  str_replace("`", "", $string);
        $string =  str_replace("`", "", $string);
        $string =  str_replace("，", "", $string);
        $string =  str_replace(".", "", $string);
        $string =  str_replace("(", "", $string);
        $string =  str_replace(")", "", $string);
        $string =  str_replace("&", " and ", $string);
        $string =  str_replace(" ", "-", $string);
        $string =  preg_replace('/-{2,}/','-',$string);
        return $string;
}

/*获取邮件配置
*	$tmp 为邮件模板变量
*/

function get_email_config($tmp){
	if(C($tmp) && $tmp)
	{
		$tmp = C($tmp);
		$BaseInfo = include(ROOT_PATH .'Conf/StmpInfo.php');
		return  array("tmp"			=>$tmp,
					   "baseinfo"	=>$BaseInfo					
				);
	}else{
			return array();
	}
}
/**
	 * 邮件发送
	 *
	 * @param: $name[string]        接收人姓名
	 * @param: $email[string]       接收人邮件地址
	 * @param: $subject[string]     邮件标题
	 * @param: $content[string]     邮件内容
	* @param: $notification[bool]  true 要求回执， false 不用回执
	 *
	 * @return boolean
	 */
 
 function get_header($name,$email,$subject,$content,$notification=true){
		$charset = 'utf-8';
		  /* 邮件的头部信息 */
        $content_type = 'Content-Type: text/html; charset= utf-8';
        $content   =  $content;
		$headers = array();
        $headers[] = 'Date: ' . gmdate('D, j M Y H:i:s') . ' +0000';
        $headers[] = 'To: "' . '=?' . $charset . '?B?' . base64_encode($name) . '?=' . '" <' . $email. '>';
        $headers[] = 'From: "' . '=?' . $charset . '?B?' . base64_encode(C('print_head_name')) . '?='.'" <' . C('SendUser') . '>';
        $headers[] = 'Subject: ' . '=?' . $charset . '?B?' . base64_encode($subject) . '?=';
        $headers[] = $content_type . '; format=flowed';
       return $headers;
}

	
	
function get_register($id,$tmp){
	$regUrl = C("SITE_URL").U("Login/ActiveUser",array("regid"	=>$id));
	return str_replace('{$link}',get_href($regUrl),$tmp);
}

function get_href($url){
	return "<a href='".$url."' target='_blank'>".$url."</a>";
}


function send_mail($data){
	$EmailConfig = get_email_config("WarnRegisterTmp");		//邮件配置信息
	import('Admin.ORG.Email');
	$Email = new Email($EmailConfig['baseinfo']);
			$headers =	get_header($data['username'],$data['email'],$data['subject'],$data['content']);
			$sendParm = array('from'		=>C('SendName'),
				 			   'headers'	=>$headers,
							   'recipients'	=>$data['email'],
							   'body'	  	=>$data['content']
							);
			 if ($Email->connect()){
				 $Email->send($sendParm);  //开始发送邮件
          	 }
		
}
function times($args){
	if(!$args)
		return date("Y-m-d",time());
	else
		return date("Y-m-d H:i:s",$args);
}

function get_str_arr($str){
	$len = strlen($str);
	for($i=0;$i<$len;$i++)
	{
		$newStr[] =substr($str,$i,1);
	}
	return $newStr;
}


/*
	转义html
*/

 function htmls($content){
 	
	$content = str_replace("\\",'',stripslashes($content));
	$content = str_replace('&quot;&quot;','',stripslashes($content));
	return $content;
}

function status($arg)
{
	$state = array(
		0=>'未通过',
		1=>'待审核',
		2=>'审核通过'	
	);
	return $state[$arg];
}

function latter_status($arg)
{
	$state = array(
			2=>'未读',
			1=>'已读'
	);
	return $state[$arg];
}

function order_status($arg)
{
	$orderStatus = array(0	=>'等待买家付款',
						1  =>"买家已付款",
						2	=>"卖家已发货",
						3	=>"买家确认收货",
						4	=>"取消订单成功",
						5	=>"退款申请中",
						6	=> "退款申请成功"
					);	
	return $orderStatus[$arg];
}

function payment_status($arg)
{
	$arg = $arg ? $arg : 1;
	$state = array(
			1=>'货到付款',
			2=>'在线付款',
			3=>'货到付款',
			4=>'预付款',
	);
	return $state[$arg];
}

function coupon_status($arg)
{
	$state = array(
			0=>'禁用',
			1=>'未使用',
			2=>'已使用',
			3=>'过期'
	);
	return $state[$arg];
}

function coupon_type($arg)
{
	$state = array(
			1=>'优惠券',
			2=>'红包',
			2=>'积分'
	);
	return $state[$arg];
}


function GetIP() {
    if ($_SERVER["HTTP_X_FORWARDED_FOR"])
        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    else if ($_SERVER["HTTP_CLIENT_IP"])
        $ip = $_SERVER["HTTP_CLIENT_IP"];
    else if ($_SERVER["REMOTE_ADDR"])
        $ip = $_SERVER["REMOTE_ADDR"];
    else if (getenv("HTTP_X_FORWARDED_FOR"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("HTTP_CLIENT_IP"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("REMOTE_ADDR"))
        $ip = getenv("REMOTE_ADDR");
    else
        $ip = "Unknown";
    return $ip;
}

function getIpAddress(){
	$ip = "27.24.158.130";
	if($ip == "Unknown"){
		return false;
	}
	$url='http://www.ip138.com/ips138.asp?ip='.$ip.'&action=2';  //IP138接口
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/14.0.835.202 Safari/535.1");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	 $content = curl_exec($ch);
	//$content=iconv('gb2312', 'UTF-8', $content);
	//print_r($content);
	 $content=iconv('GB2312', 'UTF-8', $content);//content本身是gb3212的文件是utf8的所以要转码
/*
	preg_match('/本站主数据：(?<mess>(.*))市(.*)<\/li><li>/',$content,$arr);//print_r($arr['mess']);exit;*/
	//print_r($arr);


	 preg_match('/本站主数据：(?<mess>(.*))市(.*)<\/li><li>/',$content,$arr);	//print_r($arr['mess']);exit;

	return $arr;
	
}
//新调用挂件方法
	function NW($name='',$config=null,$delegation=null,$isEcho = true){
		
		
		
		$w = A($name, 'Widget');
		
		if(!is_null($delegation)){
			import("Delegation.{$delegation}Delegation",APP_PATH.'admin/');
			$r = new ReflectionClass("{$delegation}Delegation");
			$d = $r->newInstance();
			$w->setCompent($d);
		}
		if($isEcho){
			echo $w->render($config);
			return '';
		}
		return $w->render($config);
	}

 /**
  * 获取当前id的子ID
  * @param array $data 原始数组
  * @param int $id 当前id
  * @param int $layer 当前层级
  */
 function genCate($data, $pid = 0, $level = 0)
 {
     if($level == 10)	 break;
     $l        = str_repeat("&nbsp;&nbsp;&nbsp;", $level);
     $l        = $pid == 0 ? $l : $l.'└';
     static $arrcat    = array();
     $arrcat    = empty($level) ? array() : $arrcat;
	 foreach($data as $k => $row)
     {
	 	 /**
          * 如果父ID为当前传入的id
          */
         if($row['fid'] == $pid)
         {
		 	//如果当前遍历的id不为空
             $row['classify_name']    = $l.$row['classify_name'];
             $row['level']    = $level;
             $arrcat[]    = $row;
			 genCate($data, $row['id'], $level+1);//递归调用
         }
     }
	 return $arrcat;
 }
 
function getTreeSelect($data,$name,$default = 0,$index=true){
	$str.="<select name='".$name."' id='".$name."' class='".$name."'>";
	if($index)
		$str .="<option value='0'>不选择</option>";
	foreach($data as $row)
	{
		 $selected = $default== $row['id'] ? "selected=selected" : "";  //默认选中
		 $str.="<option value={$row['id']} ".$selected.">";
		 $str.= $row['classify_name'];
		 $str.= "</option>";
	 }
	 	$str.= "</select>";
	return $str;
}

	  
function get_in($cat_in,$fix,$arr){
	if(empty($arr)){
		return false;
	}
	$cat_id  = $cat_in ? $cat_in : "id";
	foreach($arr as $key=>$val)
	{
		$catin[] = $val[$fix];
	}
	return " $cat_id in (".join(",",$catin).")";
}
	

function price_format($price,$uit="",$sprint = '￥%s元'){
	$price = number_format($price, 2, '.', '');  //保留一位小数;
	
		return   sprintf($sprint,$price);
}


function totalComentNum($statis_value,$service_value,$fh_value,$status=false)
{
	$total =  $statis_value+$service_value+$fh_value ? $statis_value+$service_value+$fh_value : 15;
		return $status ? get_star(round($total/3),'a') : round($total/3);
}

function ScoreResult($type)
{
	$array = array("","好评","中评","差评");
	return $array[$type];
} 
//合并数组
// $bulidArr   组合到该数组;
//	$byArr     被组合的数组
function bulid_array($bulidArr,$byArr){
	//empty($byArr) && return false;
	foreach($byArr as $key=>$val)
	{
		$bulidArr[$key] = $val;
	}
	return $bulidArr;
}



//付款方式;
function pay_types($type,$order_id){
	$type = $type ? $type : 1;
	if(!$order_id)
			return false;
	switch($type){
		case  1 : 
				$str = "<a href='".U("Seller/order_success",array('order_id'		=>$order_id,'pay_id'	=>$type))."'>付款</a>";
				
		break;
		case "2" :  //支付宝付款
					
				$str = "<a href='".U("Seller/order_success",array('order_id'		=>$order_id,'pay_id'	=>$type))."' target='_blank'>付款</a>";
		break;
		case "3" :  //支付宝付款
				$str = "<a href='".U("Seller/",array('order_id'		=>$order_id,'pay_id'	=>$type))."' target='_blank'>付款</a>";
		break;
	}
	return $str;
}
//3、这里有几个支付处理过程中需要用到的函数，我把这些函数写到了项目的Common/common.php中，这样不用手动调用，即可直接使用这些函数，代码如下：//////////////////////////////////////////////////////
//Orderlist数据表，用于保存用户的购买订单记录；


/*
	支付方式
*/
function payment_statuss($pay = 0){

		
		$array = array(""		=>'货到付款',
						"1"		=>'货到付款',
						'2'		=>'支付宝'
					);

		return $array[$pay];
}



//获取一个随机且唯一的订单号；
function getordcode(){
	$Ord=M('Orderlist');
	$numbers = range (10,99);
	shuffle ($numbers);
	$code=array_slice($numbers,0,4);
	$ordcode=$code[0].$code[1].$code[2].$code[3];
	$oldcode=$Ord->where("ordcode='".$ordcode."'")->getField('ordcode');
	if($oldcode){
		getordcode();
	}else{
		return $ordcode;
	}
}



/**
 * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
 * @param $para 需要拼接的数组
 * return 拼接完成以后的字符串
 */
function createLinkstring($para) {
	$arg  = "";
	while (list ($key, $val) = each ($para)) {
		$arg.=$key."=".$val."&";
	}
	//去掉最后一个&字符
	$arg = substr($arg,0,count($arg)-2);
	
	//如果存在转义字符，那么去掉转义
	if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}
	
	return $arg;
}


/**
 * 签名字符串
 * @param $prestr 需要签名的字符串
 * @param $key 私钥
 * return 签名结果
 */
function md5Sign($prestr, $key) {
	$prestr = $prestr . $key;
	return md5($prestr);
}

/**
 * 验证签名
 * @param $prestr 需要签名的字符串
 * @param $sign 签名结果
 * @param $key 私钥
 * return 签名结果
 */
function md5Verify($prestr, $sign, $key) {
	$prestr = $prestr . $key;
	$mysgin = md5($prestr);

	if($mysgin == $sign) {
		return true;
	}
	else {
		return false;
	}
}


/**
	 * 除去数组中的空值和签名参数
	 * @param $para 签名参数组
	 * return 去掉空值与签名参数后的新签名参数组
 */
function paraFilter($para) {
		$para_filter = array();
		while (list ($key, $val) = each ($para)) {
			if($key == "sign" || $key == "sign_type" || $val == "")	continue;
			else	$para_filter[$key] = $para[$key];
		}
		return $para_filter;
}
/**
	 * 对数组排序
	 * @param $para 排序前的数组
	 * return 排序后的数组
 */
function argSort($para) {
		ksort($para);
		reset($para);
		return $para;
}


/*
	 获取当前是否该显示验证码
	*/
function get_display_verify(){
	$verify = false;
	if(C("FALUT_NUM") && session("fault_num") > C("FALUT_NUM")){
		$verify = true;
	}
	return $verify;
}

/**
 +----------------------------------------------------------
 * 判断id是否在数组arr中，如果在，在数组中新增checked字段，用于编辑复选框
 +----------------------------------------------------------
 * @param  id 含有用逗号链接的字符串（例1,2,3,4）
 * @param  arr 包含id的数组(二维数组)
 +----------------------------------------------------------
 * @access public
 +----------------------------------------------------------
 * @return bool
 +----------------------------------------------------------
 */
function is_checked($id_string,$id_arr){

	$split = explode(',',$id_string);
	$return_arr = array();
	if ( ! empty($split)) {
		foreach ($id_arr as $key=>$val){
			$val['is_checked']= in_array($val['id'],$split)? 'checked': '';
			$return_arr[] = $val;
		}
		return $return_arr;
	} else {
		return $id_arr;
	}
}


/*
  保存html文件
  path  文件夹路径
  source 保存资源
*/
function save_html($source,$path){
	
	if(strpos($path,".")){
		
		$path = substr($path,0,strrpos($path,"/"));
	}
	if(!file_exists($var_dir."/".$path)){
		mkdir($path,'0777');
	}
	mkdir($path,'0777');
	$path.="/".md5($source).random(3).".html";
	$fp = fopen($path,"w");
	fwrite($fp,$source);
	fclose($fp);
	return basename($path,".html");
}


/*
	删除某个目录下所有文件信息
*/
function  rm_dir($path){

	if(!file_exists($path)){
		return false;
	}	
	$dh=opendir($path);
	while($file = readdir($dh)){
	  if($file!="." && $file!="..") {
	  	 $fullpath=$path."/".$file;
		  if(is_dir($fullpath)){
		 	rm_dir($fullpath);
		 }else{
		 	@unlink($fullpath);
		 }
	  }
	}
	closedir($dh);
	rmdir($path);
}




/**
	 +----------------------------------------------------------
	 * 订单商品标题的标识
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
 */
	function order_goods_prifix($prefix =0){
		$prefix = $prefix ? $prefix : 0;
		$array = array("【普通商品】",
					   "【秒杀商品】",
					   " 【团购商品】",
					   '【限时折扣】',
					   '-【积分兑换】',
					   '【优惠活动】',
					   '【满立减商品】'
					  );
		return $array[$prefix];
	}
	
	/**
+----------------------------------------------------------
* 字符串截取，支持中文和其他编码
+----------------------------------------------------------
* @static
* @access public
+----------------------------------------------------------
* @param string $str 需要转换的字符串
* @param string $start 开始位置
* @param string $length 截取长度
* @param string $charset 编码格式
* @param string $suffix 截断显示字符
+----------------------------------------------------------
* @return string
+----------------------------------------------------------
*/
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true)
{
    if(function_exists("mb_substr")){
            if ($suffix && strlen($str)>$length)
                return mb_substr($str, $start, $length, $charset)."...";
        else
                 return mb_substr($str, $start, $length, $charset);
    }
    elseif(function_exists('iconv_substr')) {
            if ($suffix && strlen($str)>$length)
                return iconv_substr($str,$start,$length,$charset)."...";
        else
                return iconv_substr($str,$start,$length,$charset);
    }
    $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("",array_slice($match[0], $start, $length));
    if($suffix) return $slice."…";
    return $slice;
}


	/*
		验证提交不能为空的信息
	*/
	 function check_null($checkParam,$checkData){
		if(empty($checkParam) || empty($checkData)){
			throw new Exception("检测对象或者检测参数不能为空");
		}
		foreach($checkParam as $param =>$msg){
			if(!$checkData[$param]){
				throw new Exception($msg);
			}
		}
		return $checkData;
	}
	
/*
	刪除數組中某個值
	$key   為字符串 或者數組  
*/
	function unsetArray(&$data, $key=''){
		if(!is_array($key) && $key){
			if($data[$key] != ''){
				unset($data[$key]);
			}
		}elseif(is_array($key)){
			foreach($key as $key=>$del_val){
				if($data[$del_val] != ''){
					unsetArray($data,$del_val);
				}
			}
		}else{
			array_walk($data,create_function('&$item,&$key',' $item="";$key="";'));
			$data = array_filter($data);;
			
		}
		return $data;
	}
	

	
	/*
		删除图片信息
	*/
	function unlink_img( $path ){
		if(is_array($path)){
			foreach($path as $p ){
				if(file_exists( $p )){
					@unlink( $p );
				}
			}
		}else{
			if(file_exists( $path )){
					@unlink( $path );
			}
		}
	}
	/**
     +----------------------------------------------------------
     * @function: info  生成用户日志
     +----------------------------------------------------------
   	 * @access public
     +----------------------------------------------------------
	 * $model为操作的模块,  type为操作类型, content为操作信息
	 * $model+index 为模块名称
	 "add"					=>'添加',
	 'del'					=>'删除',
	 'edit'					=>'编辑',
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     **/
	 function admin_log($model = '', $type, $content){
	 	if(!$model){$model = MODULE_NAME;}
		$model_info = DD("Function")->get_current_nav( 'url' , $model."/index");	//查询当前模块信息
		
		$log_content = ($model_info['url_name'] ? $model_info['url_name'] : $model)."->".L($type)."->".$content;
		$log_arr = array("uid"				 =>session('admin_id'),
						 "log_info"			 =>$log_content,
						 'create_datetime'	 =>time(),
						 'login_ip'			 =>get_client_ip()
						);
		return M('admin_log')->add($log_arr);
	 }
	 
	/*
		返回完整的图片路径
		$size = 0 为大图
		$size = 1  为小图
	*/
	  function product_thumb($file,$size = 0){
	 	$sizeArr = array("original",'small');
		if(!$file){
			return  C("WEB_URL").'/images/no_pic.jpg';
		}else{
			if($size) $file = "thumb_".$file;
			return  C("WEB_URL").'/upload/product/'.$sizeArr[$size].'/'.$file;
		}
	 }
	/*
		获取翻页规则
	*/
	 function  get_limit($size = ''){
		$Page  = intval($_REQUEST['p']) ? intval($_REQUEST['p']) : 1;
		$size = $size ? $size : C("DEFAULT_SIZE");
		return ($Page-1)*$size;
	}
	
	function review($status){
		$status =  $status!='' ? $status : 0;
		$review_arr = array('未审核','已审核');
		return $review_arr[$status];
	}	
	
	function api_login(){
		  $faster_login = array("qq" =>array("url"	 =>U("User/api_login",array("prefix"	=>"qq")),
											 "title" =>'QQ登陆'
													),
								"wb"  =>array("url"	  =>U("User/api_login",array("prefix"	=>"sina")),
											  "title" =>'新浪微博登陆'
											),
								"kxw"	=>array("url"   =>U("User/api_login",array("prefix"	=>"kaixin")),
												"title"	=>"开心网登陆"
											),
								"rrw"	=>array("url"	=>U("User/api_login",array("prefix"	=>"renren")),
												"title"	=>'人人网登陆'
											),
								"db"	=>array("url"  =>U("User/api_login",array("prefix"	=>"douban")),
												"title"	=>'豆瓣网登陆'
										 )
								);
		return $faster_login;
	}
	
	
	/**
	 +----------------------------------------------------------
	 * @function: info  获取用户头像Member edit用到
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return boolean
	 +----------------------------------------------------------
	 **/
	function all_dir($path){
		if(!file_exists($path)){
			exit("错误的文件夹路径");
		}
		$dirHandle = opendir($path);
		while (false !== ($fileName = readdir($dirHandle))) {
			$subFile =  $fileName;
			if ($fileName !='.' && $fileName !='..') {
				$fileArr[] = $subFile;
			}
		}
		return $fileArr;
	}
	
	/**
	 +----------------------------------------------------------
	 * @function: info  取订单号
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return boolean
	 +----------------------------------------------------------
	 **/
	function get_order_id(){
		$year_code = array('A','B','C','D','E','F','G','H','I','J');
		$order_sn  = 'TY'.$year_code[intval(date('Y'))-2010]
					.strtoupper(dechex(date('m')))
					.date('d')
					.substr(time(),-5)
					.substr(microtime(),2,5)
					.sprintf('d',rand(0,99));
		return $order_sn;
	}
	
	
	/**
	 +----------------------------------------------------------
	 * @function: info  计算两个时间之差
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return boolean
	 +----------------------------------------------------------
	 **/
	function timediff($starttime,$endtime = '')
	{
		if (empty($endtime)) {
			$endtime = time();
		}
		$timediff 	= $endtime-$starttime;
		$days 		= intval($timediff/86400);
		$remain 	= $timediff%86400;
		$hours 		= intval($remain/3600);
		$remain 	= $remain%3600;
		$mins 		= intval($remain/60);
		$secs 		= $remain%60;
		
		$res 		= array(
				"days"  	=> $days,
				"hours" 	=> $hours,
				"mins"  	=> $mins,
				"seconds"   => $secs
		);
		
		$r = '';
		foreach ($res as $key => $v){
			if ($v) {
				$r =$v.$key.' ago';
				break;
			}
		}
		return $r;
	}
	
	
	/*
	 清楚前台静态文件缓存
	
	*/
	function clear_function_cache($file_name, $replace_dir = array()){
		if(empty($replace_dir)){
			$replace_dir = array("admin", 'home');
		}
		list($replace_from , $replace_to) =  $replace_dir;
		$clear_path = str_replace("src/".$replace_from."", 'src/'.$replace_to.'', DATA_PATH);
		if(file_exists($clear_path)  && is_dir($clear_path)){
			F($file_name, array(),$clear_path);
		}		
		
	}
	
	/**
	 * 将二维数组变成IdName数组 用于下拉框
	 *
	 * @param array $aTwo 二维数组
	 * @return array
	 */
	function f_sql_twoToIdName($aTwo){
		if (empty($aTwo) || !is_array($aTwo)){
			return array();
		}
		$aIdName = array();
		foreach ($aTwo as $aD){
			$aVal = array_values($aD);
			$aOne['id'] = $aVal[0];
			$aOne['name'] = $aVal[1];
	
			if (count($aD) > 2){
				$aD_fix = array_slice($aD, 2);
				$aOne = array_merge($aOne, $aD_fix);
			}
			$aIdName[$aOne['id']] = $aOne;
		}
		return $aIdName;
	}
	
/**
 * 将int时间 转化为 日期 / 当前年不显示， 不显示秒
 *
 * @param int $nTime 时间
 * @param string $sType 类型  默认  ''/Y-m-d H:i, 'ymd'/Y-m-d
 * @return string
 */
function f_time_show($nTime, $sType=''){
	$nTime = (int)$nTime;
	if ($nTime == 0){return '-';}
	
	if ($sType == 'ymd'){
		$sThisDate = date('Y-m-d', $nTime);
		if (date('Y') == substr($sThisDate, 0, 4)){return substr($sThisDate, 5);}return $sThisDate;
	}
	$sThisDate = date('Y-m-d H:i', $nTime);
	if (date('Y-m-d') == substr($sThisDate, 0, 10)){return substr($sThisDate, 11);}
	if (date('Y') == substr($sThisDate, 0, 4)){return substr($sThisDate, 5);}
	return $sThisDate;
}
	
	function clear_cache($key){
		$cache = Cache::getInstance();
		$cache->rm($key);
	}
	
	function verify_mobile($mobile){
		if(preg_match("/^13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/",$mobile_number)){    
     		return false;
        }
		return true;
	}
	
?>