<?php
/**
 * 基础类
 *
 * @author		黑冰(001.black.ice@gmail.com)
 * @copyright	(c) 2012
 * @version		$Id$
 * @package		com.modules
 * @since		v0.1
 */
	define("WORD_COUNT_MASK", "/\p{L}[\p{L}\p{Mn}\p{Pd}'\x{2019}]*/u");

	class MyController extends CController{
    
		// 默认分页条数
		const PAGE_SIZE = 20;

		// 初始化
		public function init(){
			parent::init();
		}

		/**
		 * 不加载布局的视图输出
		 */
		public function display($filename, $data = array())
		{
			$this->renderPartial($filename, $data);
		}


		/**
		 * AJAX输出
		 */
		public static function ajaxDisplay($msg)
		{
			header('Content-Type: text/html; charset=utf-8');
			header('Pragma: no-cache');
			header('Expires: Thu, 19 Nov 1981 08:52:00 GMT');

			die($msg);
		}

		/**
		 * 后台管理中心AJAX输出错误！
		 */
		public static function ajaxError($msg, $error = 1)
		{
			header('Content-Type: text/html; charset=utf-8');
			header('Pragma: no-cache');
			header('Expires: Thu, 19 Nov 1981 08:52:00 GMT');

			$result = array();
			$result['msg'] = $msg;
			$result['error'] = $error;
			die(CJSON::encode($result));
		}

		/**
		 * DWZ框架普通AJAX请求错误消息提示
		 */
		public static function alert_error($msg = '操作失败')
		{
			$data = array();
			$data['message'] = $msg;
			$data['statusCode'] = 300;
			self::ajaxDisplay(CJSON::encode($data));
		}

		/**
		 * DWZ框架普通AJAX请求成功消息提示
		 */
		public static function alert_ok($msg = '操作成功', $data = array())
		{
			//$data['callbackType'] = 'closeCurrent';// 关闭当前标签
			//$['callbackType'] = 'forward';// 刷新当前标签
			//$['navTabId'] = '';// 标签页的ID
			//$['forwardUrl'] = '';// 跳转的URL

			if(!$data)
			{
				$data['callbackType'] = 'forward';//表示刷新
			}
			$data['message'] = $msg;
			$data['statusCode'] = 200;
			self::ajaxDisplay(CJSON::encode($data));
			exit;
		}

		public static function alert_ok2($msg = '操作成功', $is_exit = true)
		{
			echo "<script>alert('{$msg}');</script>";

			if($is_exit)
				exit;
		}

		/**
		 * DWZ框架IFRAME上传文件时的错误消息提示
		 */
		public static function iframe_alert_error($msg = '操作失败')
		{
			echo "<script type='text/javascript'>var statusCode='300';var message='{$msg}';var navTabId='';var forwardUrl='';var callbackType='closeCurrent';var response = {statusCode:statusCode,message:message,navTabId:navTabId,forwardUrl:forwardUrl,callbackType:callbackType};if(window.parent.donecallback) window.parent.donecallback(response);</script>";
			exit;
		}

		/**
		 * DWZ框架IFRAME上传文件时的成功消息提示
		 */
		public static function iframe_alert_ok($msg = '操作成功', $navTabId = '')
		{
			echo "<script type=\"text/javascript\">var statusCode='200';var message='{$msg}';var response = {statusCode:statusCode,message:message,navTabId:'{$navTabId}',forwardUrl:'',callbackType:'forward'};if(window.parent.donecallback) window.parent.donecallback(response);</script>";
			exit;
		}
		
		
		/**
		* 验证码
		*/
		public function actions() {
		return array(
				'captcha'=> array(
				'class' => 'CCaptchaAction',//加载外部的action class
				'backColor' => 0xffffff,//设置验证码图片背景色属性
				'width' => 100,
				'height' => 40,
		'padding' => 3,
		'maxLength' => 2,
		//'transparent' => TRUE,// 是否需要刷新按钮
				'testLimit' => 10,//三次输入错误时就换一张
				'transparent' => TRUE,//显示为透明*/
				),
		);
		}
		
		/**
		 * 注销
		 */
		  public function actionLogout()
		  {
		  Yii::app()->user->logout();
		  }
	
		  /**
		  * 替换为红色
		  */
		  public function replaceToRed($replace_string, $string)
		  {
		  	if(!is_array($replace_string))
		  	{
		  		$replace_string = array($replace_string);
		  	}
		  	foreach ($replace_string as $row)
		  	{
		  		$string = str_replace($row, "<font color=\"red\"><b>{$row}</b></font>", $string);
		  	}
		  	return $string;
		  }
	
		  /**
		   * 静态样式的分页处理
		   */
		  public function page($page_html)
		  {
		  	$page_html = preg_replace('/\.html?\?page=([\d]+)/', "_\$1.html", $page_html);
		  	echo $page_html;
		  }
	
		  /**
		   * 截取字符串
		   */
		  public static function cutStr($str, $lenght) {
		  	if (strlen($str) <= $lenght) {
		  		return($str);
		  	} else {
		  		$not_zh_len = 0;
	
		  		for ($i = 0; $i < $lenght; $i++) {
		  			if (ord(substr($str, $i, 1)) < 128) {
		  				$not_zh_len = $not_zh_len + 1;
		  			}
		  		}
	
		  		if ($lenght % 3 == 0 && $not_zh_len % 3 == 1 ) {
		  			$lenght = $lenght + 2;
		  		}
		  		else if ($lenght % 3 == 0 && $not_zh_len % 3 == 2 ) {
		  			$lenght = $lenght + 2;
		  		}
		  		else if ($lenght % 3 == 1 && $not_zh_len % 3 == 0 ) {
		  			$lenght = $lenght + 2;
		  		}
		  		else if ($lenght % 3 == 1 && $not_zh_len % 3 == 2 ) {
		  			$lenght = $lenght + 1;
		  		}
		  		else if ($lenght % 3 == 1 && $not_zh_len % 3 == 3 ) {
		  			$lenght = $lenght + 1;
		  		}
		  		else if ($lenght % 3 == 2 && $not_zh_len % 3 == 1 ) {
		  			$lenght = $lenght + 2;
		  		}
		  		else if ($lenght % 3 == 2 && $not_zh_len % 3 == 3 ) {
		  			$lenght = $lenght + 2;
		  		}
		  		else if ($lenght % 3 == 2 && $not_zh_len % 3 == 0 ) {
		  			$lenght = $lenght + 1;
		  		}
	
		  		return(substr($str, 0, $lenght) . '…');
		  	}
		  }
	
		  public static function get_url($url,$ispost = FALSE,$post_data=null)
		  {
		  	//启动一个CURL会话
		  	$ch = curl_init();
	
		  	// 要访问的地址
		  	curl_setopt($ch, CURLOPT_URL, $url);
	
		  	// 对认证证书来源的检查，0表示阻止对证书的合法性的检查。
		  	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	
		  	// 从证书中检查SSL加密算法是否存在
		  	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
	
		  	//模拟用户使用的浏览器，在HTTP请求中包含一个”user-agent”头的字符串。
		  	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)");
	
		  	//发送一个常规的POST请求，类型为：application/x-www-form-urlencoded，就像表单提交的一样。
		  	if ($ispost)
		  	{
		  		curl_setopt($ch, CURLOPT_POST, 1);
		  		//要传送的所有数据，如果要传送一个文件，需要一个@开头的文件名
		  		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		  	}
	
	
	
		  	//连接关闭以后，存放cookie信息的文件名称
		  	#curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
	
		  	// 包含cookie信息的文件名称，这个cookie文件可以是Netscape格式或者HTTP风格的header信息。
		  	#curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
	
		  	// 设置curl允许执行的最长秒数
		  	//curl_setopt($ch, CURLOPT_TIMEOUT, 6);
	
		  	// 获取的信息以文件流的形式返回，而不是直接输出。
		  	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	
		  	// 执行操作
		  	$result = curl_exec($ch);
		  	$httpcode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
	
		  	// 关闭CURL会话
		  	curl_close($ch);
		  	return array('code'=>$httpcode,'content'=>$result);
		  }
	
		  public static function hash_password($password)
		  {
		  	return md5(md5($password));
		  }
	
		  /**
		   * 得到用户的IP
		   */
		  public static function getUserHostAddress()
		  {
		  	switch(true)
		  	{
		  		case ($ip=getenv("HTTP_CLIENT_IP")):
		  			break;
		  		case ($ip=getenv("HTTP_X_FORWARDED_FOR")):
		  			break;
		  		default:
		  			$ip=getenv("REMOTE_ADDR") ? getenv("REMOTE_ADDR") : '127.0.0.1';
		  	}
		  	if (strpos($ip, ', ') > 0)
		  	{
		  		$ips = explode(', ', $ip);
		  		$ip = $ips[0];
		  	}
		  	return $ip;
		  }
	
	
		  static function microtime_float()
		  {
		  	list($usec, $sec) = explode(" ", microtime());
		  	return ((float)$usec + (float)$sec);
		  }

	}
?>
