<?php
class Common {
	//获得客服端的IP
	public function get_client_ip(){
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
	   return($ip);
	}
	
	//输出成功信息
	function ajax_json_success($message,$callbackType="",$navTabId="",$forwardUrl=""){
		//$callbackType=!empty($callbackType)?"forward":"";
		switch($callbackType){
			case 1:
				$callbackTypeStr="forward";
				break;
			case 2:
				$callbackTypeStr="closeCurrent";
				break;
			default :
				$callbackTypeStr="";
				break;
		}
		$forwardUrl  =!empty($forwardUrl)?ACT.$forwardUrl:"";
		$menu=array(
				  "statusCode"=>"200", 
				  "message"=>$message, 
				  "navTabId"=>$navTabId, 
				  "rel"=>"1", 
				  "callbackType"=>$callbackTypeStr,
				  "forwardUrl"=> $forwardUrl
		 );
		 echo json_encode($menu);		
	}	
	/*错误提示*/
	function ajax_json_error($message){
		$menu=array(
				  "statusCode"=>"300", 
				  "message"=>$message
		 );
		 echo json_encode($menu);		
	}
	
	/*超时提示*/
	function ajax_json_timeout($message){
		$menu=array(
				  "statusCode"=>"301", 
				  "message"=>$message
		 );
		 echo json_encode($menu);		
	}		
	//标签替换
	public function replace_tags($tagsArr,$string){
		foreach($tagsArr as $key=>$value)
		{
			 $string=str_replace("{".$key."}",$value,$string);
		
		}
		return  $string;			
	}
	//curl 请求函数
	public function open_curl($url,$post_data){
		//$post_data = http_build_query($post_data);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);
 		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:')); //设置header
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

		curl_setopt($ch, CURLOPT_TIMEOUT,30);
		$response  = curl_exec($ch);
		
		$errno = curl_errno( $ch );
		$info  = curl_getinfo( $ch );
		$error = curl_error($ch);
//		var_dump($response);
//		var_dump($error);
		curl_close($ch);//关闭	
		return $response;
	}
	
	//跳转到一下页的JS
	public function gotojs(){
		$gotojs = "function GotoNextPage(){
			document.gonext."."submit();
		}"."\r\nset"."Timeout('GotoNextPage()',500);";
		
		return "<script language='javascript'>$gotojs</script>";
	}
	
	//打印输出提示信息
	function put_info($msg1,$msg2){
		$msginfo = "<html>\n<head>
			<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
			<title>提示信息</title>
			<base target='_self'/>\n</head>\n<body leftmargin='0' topmargin='0'>\n<center>
			<br/>
			<div style='width:600px;padding-top:4px;height:24;font-size:10pt;border-left:1px solid #cccccc;border-top:1px solid #cccccc;border-right:1px solid #cccccc;background-color:#DBEEBD;'>提示信息！</div>
			<div style='width:580px;font-size:10pt;border:1px solid #cccccc;background-color:#F4FAEB;text-align:left;padding-left:20px;'>
			<span style='line-height:160%'><br/>{$msg1}</span>
			<br/><br/></div>\r\n{$msg2}</center>\n</body>\n</html>";
		$msginfo .='<script type="text/JavaScript">parent.document.getElementById("lastmove").value=Math.round(new Date().getTime()/1000);</script>';
		echo $msginfo;
	}
	//时间计算
	function date_range($type,$datetime){
		if($type=='-1'){
			switch($datetime){
				case '3d' :
					$date_range=date('Y-m-d',strtotime("-3 day",time()));
					break;
				case '7d' :
					$date_range=date('Y-m-d',strtotime("-7 day",time()));
					break;
				case '15d' :
					$date_range=date('Y-m-d',strtotime("-15 day",time()));	
					break;
				case '1m' :
					$date_range=date('Y-m-d',strtotime("-1 month",time()));	
					break;
				case '3m' :
					$date_range=date('Y-m-d',strtotime("-3 month",time()));	
					break;
				case '6m' :
					$date_range=date('Y-m-d',strtotime("-6 month",time()));	
					break;
				case '12m' :
					$date_range=date('Y-m-d',strtotime("-12 month",time()));	
					break;
			}			
		}else if($type=='1'){
			switch($datetime){
				case '3d' :
					$date_range=date('Y-m-d',strtotime("+3 day",time()));
					break;
				case '7d' :
					$date_range=date('Y-m-d',strtotime("+7 day",time()));
					break;
				case '15d' :
					$date_range=date('Y-m-d',strtotime("+15 day",time()));	
					break;
				case '1m' :
					$date_range=date('Y-m-d',strtotime("+1 month",time()));	
					break;
				case '3m' :
					$date_range=date('Y-m-d',strtotime("+3 month",time()));	
					break;
				case '6m' :
					$date_range=date('Y-m-d',strtotime("+6 month",time()));	
					break;
				case '12m' :
					$date_range=date('Y-m-d',strtotime("+12 month",time()));	
					break;	
			}			
		}
		return $date_range;
	}
	
}//end class
?>
