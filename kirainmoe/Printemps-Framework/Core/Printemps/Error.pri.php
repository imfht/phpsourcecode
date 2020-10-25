<?php
/**
 * Printemps Framework 错误处理类
 * (c)2015 Printemps Framework DevTeam All rights Reserved.
 */

function Printemps_Error($errno , $errorInfo , $errfile ='', $errline = '' ,$exit = true, $setHeader = true){

	$error = array(
		200=>"OK/请求已成功，请求所希望的响应头或数据体将随此响应返回。",
		201=>"Created/请求已经被实现，而且有一个新的资源已经依据请求的需要而建立。",
		202=>"Accepted/服务器已接受请求，但尚未处理。",
		203=>"Non-Authoritative Information/服务器已成功处理了请求，但返回的实体头部元信息不是在原始服务器上有效的确定集合。",
		204=>"No Content/服务器成功处理了请求，但不需要返回任何实体内容，并且希望返回更新了的元信息。",
		205=>"Reset Content/服务器成功处理了请求，且没有返回任何内容。",
		206=>"Partial Content/服务器已经成功处理了部分 GET 请求。",
		300 => 'Multiple Choices/被请求的资源有一系列可供选择的回馈信息，每个都有自己特定的地址和浏览器驱动的商议信息。用户或浏览器能够自行选择一个首选的地址进行重定向。 ',
		301 => 'Moved Permanently/被请求的资源已永久移动到新位置，并且将来任何对此资源的引用都应该使用本响应返回的若干个 URI 之一。',
		302 => 'Found/临时重定向：请求的资源现在临时从不同的 URI 响应请求。',
		303 => 'See Other/对应当前请求的响应可以在另一个 URI 上被找到，而且您应当采用 GET 的方式访问那个资源。',
		304 => 'Not Modified/如果客户端发送了一个带条件的 GET 请求且该请求已被允许，而文档的内容（自上次访问以来或者根据请求的条件）并没有改变，则服务器应当返回这个状态码。',
		305 => 'Use Proxy/被请求的资源必须通过指定的代理才能被访问。L',
		307 => 'Temporary Redirect/临时重定向：请求的资源现在临时从不同的URI 响应请求。',
		400 => 'Bad Request/服务器娘无法理解您的请求哦……',
		401 => 'Unauthorized/需要执行身份验证=A=',
		403 => 'Forbidden/哼，不许碰，这里不许碰！',
		404 => 'Not Found/啊嘞，你要找的东西或许被吃掉啦……NicoNicoNi~',
		405 => 'Method Not Allowed/请求行中指定的请求方法不能被用于请求相应的资源。',
		406 => 'Not Acceptable/请求的资源的内容特性无法满足请求头中的条件，因而无法生成响应实体。 ',
		407 => 'Proxy Authentication Required/需要身份验证，并且你必须在代理服务器上进行身份验证。',
		408 => 'Request Timeout/请求超时辣…… : (',
		409 => 'Conflict/由于和被请求的资源的当前状态之间存在冲突，请求无法完成。',
		410 => 'Gone/被请求的资源在服务器上已经不再可用，而且没有任何已知的转发地址。这样的状况应当被认为是永久性的。',
		411 => 'Length Required/服务器拒绝在没有定义 Content-Length 头的情况下接受请求。你可以在添加了表明请求消息体长度的有效 Content-Length 头之后再次提交该请求。',
		412 => 'Precondition Failed/服务器在验证在请求的头字段中给出先决条件时，没能满足其中的一个或多个。',
		413 => 'Request Entity Too Large/服务器拒绝处理当前请求，因为该请求提交的实体数据大小超过了服务器愿意或者能够处理的范围。',
		414 => 'Request-URI Too Long/请求的URI 长度超过了服务器能够解释的长度，因此服务器拒绝对该请求提供服务。',
		415 => 'Unsupported Media Type/对于当前请求的方法和所请求的资源，请求中提交的实体并不是服务器中所支持的格式，因此请求被拒绝。',
		416 => 'Requested Range Not Satisfiable/请求中包含了 Range 请求头，并且 Range 中指定的任何数据范围都与当前资源的可用范围不重合，同时请求中又没有定义 If-Range 请求头。 ',
		417 => 'Expectation Failed/在请求头 Expect 中指定的预期内容无法被服务器满足，或者这个服务器是一个代理服务器，它有明显的证据证明在当前路由的下一个节点上，Expect 的内容无法被满足。',
		422 => 'Unprocessable Entity/从当前客户端所在的IP地址到服务器的连接数超过了服务器许可的最大范围。',
		500 => 'Internal Server Error/啊哦，服务器炸了（wu）。遇到了一个窝萌从来没见过的错误呐。',
		501 => 'Not Implemented/服务器不支持当前请求所需要的某个功能。当服务器无法识别请求的方法，并且无法支持其对任何资源的请求。',
		502 => 'Bad Gateway/解析请求的网关炸了（弥天大雾）',
		503 => 'Service Unavailable/当前服务器出现了异常，无法返回处理的结果……',
		504 => 'Gateway Timeout/请求超时 :(',
		505 => 'HTTP Version Not Supported/服务器不支持，或者拒绝支持在请求中使用的 HTTP 版本。'
		);
	$http = array ( 
		100 => "HTTP/1.1 100 Continue", 
		101 => "HTTP/1.1 101 Switching Protocols", 
		200 => "HTTP/1.1 200 OK", 
		201 => "HTTP/1.1 201 Created", 
		202 => "HTTP/1.1 202 Accepted", 
		203 => "HTTP/1.1 203 Non-Authoritative Information", 
		204 => "HTTP/1.1 204 No Content", 
		205 => "HTTP/1.1 205 Reset Content", 
		206 => "HTTP/1.1 206 Partial Content", 
		300 => "HTTP/1.1 300 Multiple Choices", 
		301 => "HTTP/1.1 301 Moved Permanently", 
		302 => "HTTP/1.1 302 Found", 
		303 => "HTTP/1.1 303 See Other", 
		304 => "HTTP/1.1 304 Not Modified", 
		305 => "HTTP/1.1 305 Use Proxy", 
		307 => "HTTP/1.1 307 Temporary Redirect", 
		400 => "HTTP/1.1 400 Bad Request", 
		401 => "HTTP/1.1 401 Unauthorized", 
		402 => "HTTP/1.1 402 Payment Required", 
		403 => "HTTP/1.1 403 Forbidden", 
		404 => "HTTP/1.1 404 Not Found", 
		405 => "HTTP/1.1 405 Method Not Allowed", 
		406 => "HTTP/1.1 406 Not Acceptable", 
		407 => "HTTP/1.1 407 Proxy Authentication Required", 
		408 => "HTTP/1.1 408 Request Time-out", 
		409 => "HTTP/1.1 409 Conflict", 
		410 => "HTTP/1.1 410 Gone", 
		411 => "HTTP/1.1 411 Length Required", 
		412 => "HTTP/1.1 412 Precondition Failed", 
		413 => "HTTP/1.1 413 Request Entity Too Large", 
		414 => "HTTP/1.1 414 Request-URI Too Large", 
		415 => "HTTP/1.1 415 Unsupported Media Type", 
		416 => "HTTP/1.1 416 Requested range not satisfiable", 
		417 => "HTTP/1.1 417 Expectation Failed", 
		500 => "HTTP/1.1 500 Internal Server Error", 
		501 => "HTTP/1.1 501 Not Implemented", 
		502 => "HTTP/1.1 502 Bad Gateway", 
		503 => "HTTP/1.1 503 Service Unavailable", 
		504 => "HTTP/1.1 504 Gateway Time-out"  
		); 

	isset($error[$errno]) ? $detailInfo = explode("/",$error[$errno]) : $detailInfo = array();
	isset($http[$errno]) ? $status = $http[$errno] : $status = 'HTTP/1.1 500 Internal Server Error';
	if($setHeader)
		Printemps_Error::setHeader($status);
	?>
	<!DOCTYPE HTML PUBLIC>
	<html>
	<head>
		<meta charset="UTF-8">
		<title><?php if(isset($error[$errno])) echo $errno; else echo 500;?> <?php echo APP_NAME; ?> - 抛出异常</title>
		<style type="text/css">
			body{
				margin:0px;
				font-family: "Segoe Print","Microsoft Yahei";
				background-color: rgb(97,206,51);
				color:#fff;
				z-index:2333;
			}
			h3{
				text-align: center;
				margin-top:4%;
				font-size:36px;
			}
			.printemps-error-description, .printemps-error-detailInfo{
				text-align: center;
				font-size:18px;
				margin:0px;
			}
			.error-detail{
				background-color:#fff;
				width: 60%;
				margin:0 auto;
				padding:20 15px;
				color:#000;
				box-shadow: 0 0 50px rgb(97,206,51);
			}
			.error-line{
				background-color:#13EF0B;
				width:60%;
				padding:10 15px;
				margin:0 auto;
			}
			p{
				margin: 0;
			}
			.error-container{
				width: 100%;
				background-color: rgb(97,206,51);
				z-index: 999;
				position: fixed;
				top:0;
				height: 100%;
			}
		</style>
	</head>
	<body>
	<div class="error-container">
		<h3><?php if(isset($error[$errno])) echo $errno; else echo '500'.' Internal Server Error ';?> <?php if(!empty($detailInfo)) echo $detailInfo[0];?></h3>
		<p class="printemps-error-description"><?php if(isset($detailInfo[1])) echo $detailInfo[1];?></p>
		<br/>
		<div class="error-detail">
			<p class="printemps-error-detailInfo">详细描述：<?php echo $errorInfo;?></p>
		</div>
		<?php if(!empty($errline)): ?>
			<div class="error-line">
				异常详情 [Error : <?php echo $errno;?>]:
				<p><?php echo "位于 $errfile 文件的 $errline 行";?></p>
			</div>
			<br/>
		<?php endif;?>

		<p style="text-align: center;position:absolute;bottom: 10px;width: 100%;">当您看到这个页面，说明 Printemps Framework 的工作中出现了致命的异常。</p>
	</body>
	</div>
	</html>
	<?php
	if($exit)
		die();
}

function Printemps_Notice($errno , $errorInfo , $errfile ='', $errline = '' ){
	?>
	<div class="printemps-notice-container" style="width: auto;padding:1 20px;margin:10 0px;color:rgb(60,118,61);border-radius: 6px;background-color:rgb(223,240,216);font-family: 'Segoe Print','Microsoft';">
		<p>注意：@ <?php echo "[ $errno ]: $errorInfo 位于 $errfile 文件的 $errline 行";?></p>
	</div>
	<?php
}

class Printemps_Error{
	/**
	 * setHeader 设置错误的HTTP信息
	 * @param string $header 设置的header信息
	 * @return  none
	 */
	static function setHeader($header){
		header($header);
	}
}
