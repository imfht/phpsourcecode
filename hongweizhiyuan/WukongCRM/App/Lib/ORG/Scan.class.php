<?php
//webscan_error();
//引用配置文件
//require_once('webscan_cache.php');
$webscan_switch=1;
//提交方式拦截(1开启拦截,0关闭拦截,post,get,cookie,referre选择需要拦截的方式)
$webscan_post=1;
$webscan_get=1;
$webscan_cookie=1;
$webscan_referre=1;
$webscan_white_directory='admin';
$webscan_white_url = array('index.php' => 'm=admin');

//get拦截规则
$getfilter = "<[^>]*?=[^>]*?&#[^>]*?>|iframe|\\b(alert\\(|confirm\\(|expression\\(|prompt\\()|<[^>]*?\\b(onerror|onmousemove|ondblclick|onmousedown|onmouseup|onmouseout|onscroll|onfocus|onsubmit|onblur|onchange|onload|onclick|onmouseover)\\b[^>]*?>|^\\+\\/v(8|9)|\\b(and|or)\\b\\s*?([\\(\\)'\"\\d]+?=[\\(\\)'\"\\d]+?|[\\(\\)'\"a-zA-Z]+?=[\\(\\)'\"a-zA-Z]+?|>|<|\s+?[\\w]+?\\s+?\\bin\\b\\s*?\(|\\blike\\b\\s+?[\"'])|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
//post拦截规则
$postfilter = "<[^>]*?=[^>]*?&#[^>]*?>|iframe|\\b(alert\\(|confirm\\(|expression\\(|prompt\\()|<[^>]*?\\b(onerror|onmousemove|ondblclick|onmousedown|onmouseup|onmouseout|onscroll|onfocus|onsubmit|onblur|onchange|onload|onclick|onmouseover)\\b[^>]*?>|\\b(and|or)\\b\\s*?([\\(\\)'\"\\d]+?=[\\(\\)'\"\\d]+?|[\\(\\)'\"a-zA-Z]+?=[\\(\\)'\"a-zA-Z]+?|>|<|\s+?[\\w]+?\\s+?\\bin\\b\\s*?\(|\\blike\\b\\s+?[\"'])|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
//cookie拦截规则
$cookiefilter = "\\b(and|or)\\b\\s*?([\\(\\)'\"\\d]+?=[\\(\\)'\"\\d]+?|[\\(\\)'\"a-zA-Z]+?=[\\(\\)'\"a-zA-Z]+?|>|<|\s+?[\\w]+?\\s+?\\bin\\b\\s*?\(|\\blike\\b\\s+?[\"'])|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
//referer获取
$webscan_referer = empty($_SERVER['HTTP_REFERER']) ? array() : array('HTTP_REFERER'=>$_SERVER['HTTP_REFERER']);

/**
 *   关闭用户错误提示
 */
function webscan_error() {
  if (ini_get('display_errors')) {
    ini_set('display_errors', '0');
  }
}

/**
 *  记录日志
 */
function webscan_slog($logs) {
  $string = "\r\n================\r\n".implode("\r\n", $logs);
  file_put_contents(APP_PATH.'/Runtime/Logs/input_error.txt', $string, FILE_APPEND);
}

/**
 *  参数拆分
 */
function webscan_arr_foreach($arr) {
  static $str;
  if (!is_array($arr)) {
    return $arr;
  }
  foreach ($arr as $key => $val ) {
    if (is_array($val)) {
      webscan_arr_foreach($val);
    } else {
      $str[] = $val;
    }
  }
  return implode($str);
}


/**
 *  防护提示页
 */
function webscan_pape(){
  $pape= <<<HTML
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>操作失败！</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="悟空CRM">
    <link href="./Public/css/bootstrap.min.css" rel="stylesheet">
	<link href="./Public/css/bootstrap-responsive.css" rel="stylesheet">
    <link rel="shortcut icon" href="./Public/ico/favicon.png">
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
      }

      .form-signin {
        max-width: 400px;
        padding: 19px 29px 29px;
        margin: 0 auto 20px;
        background-color: #fff;
        border: 1px solid #e5e5e5;
        -webkit-border-radius: 5px;
           -moz-border-radius: 5px;
                border-radius: 5px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
           -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
      }
      .form-signin .form-signin-heading,
      .form-signin .checkbox {
        margin-bottom: 10px;
      }
      .form-signin input[type="text"],
      .form-signin input[type="password"] {
        font-size: 16px;
        height: auto;
        margin-bottom: 15px;
        padding: 7px 9px;
      }
	  .wukong {
	    margin-top:30px;
		padding-top:10px;
		border-top:1px solid #e5e5e5;		
	  }
    </style>
    <script type="text/javascript">
        var browserInfo = {browser:"", version: ""};
        var ua = navigator.userAgent.toLowerCase();
        if (window.ActiveXObject) {
            browserInfo.browser = "IE";
            browserInfo.version = ua.match(/msie ([\d.]+)/)[1];
            if(browserInfo.version <= 7){
                if(confirm("您的ie浏览器版本过低，建议使用chorme浏览器，\n点击【确定】转到下载页面")){}
                location.href = 'http://www.google.cn/intl/zh-CN/chrome/browser/';
            }
        }
    </script>
  </head>
  <body>
    <div class="container">
      <form action="" method="post" class="form-signin">
		<fieldset>
			<legend><h3>操作失败！</h3></legend>		
			
									<div class="alert alert-error">插入了被禁用的标签！</div>			<p class="jump">
                 <a id="href" href="javascript:history.back(-1);">点击返回</a> </p> 
			<div class="row-fluid wukong">
				<div class="span3"><img style="height:50px;" src="./Public/img/logo_img.png" alt="悟空CRM"/></div>
				<div class="span9">
					<p>悟空CRM &copy; 郑州卡卡罗特软件科技有限公司 2013</p>
					<p><small>
						<a href="http://5kcrm.com/index.php?m=doc" target="_blank">使用帮助</a>
						<span class="muted">&middot;</span>
						<a href="http://www.5kcrm.com/" target="_blank">技术支持</a>
						<span class="muted">&middot;</span>
						<a href="http://www.ccds24.com" target="_blank">联系我们</a>
					</small></p>
				</div>
			</div>
      </form>
    </div> <!-- /container -->	
  </body>
</html>
HTML;
 echo $pape;
  
}

/**
 *  攻击检查拦截
 */
function webscan_StopAttack($StrFiltKey,$StrFiltValue,$ArrFiltReq,$method) {
  $StrFiltValue=webscan_arr_foreach($StrFiltValue);
  if (preg_match("/".$ArrFiltReq."/is",$StrFiltValue)==1){
    webscan_slog(array('ip' => $_SERVER["REMOTE_ADDR"],'time'=>strftime("%Y-%m-%d %H:%M:%S"),'page'=>$_SERVER["PHP_SELF"],'method'=>$method,'rkey'=>$StrFiltKey,'rdata'=>$StrFiltValue,'user_agent'=>$_SERVER['HTTP_USER_AGENT'],'request_url'=>$_SERVER["REQUEST_URI"]));
    exit(webscan_pape());
  }
  if (preg_match("/".$ArrFiltReq."/is",$StrFiltKey)==1){
    webscan_slog(array('ip' => $_SERVER["REMOTE_ADDR"],'time'=>strftime("%Y-%m-%d %H:%M:%S"),'page'=>$_SERVER["PHP_SELF"],'method'=>$method,'rkey'=>$StrFiltKey,'rdata'=>$StrFiltKey,'user_agent'=>$_SERVER['HTTP_USER_AGENT'],'request_url'=>$_SERVER["REQUEST_URI"]));
    exit(webscan_pape());
  }
}
/**
 *  拦截目录白名单
 */
function webscan_white($webscan_white_name,$webscan_white_url=array()) {
  $url_path=$_SERVER['PHP_SELF'];
  $url_var=$_SERVER['QUERY_STRING'];
  if (preg_match("/".$webscan_white_name."/is",$url_path)==1) {
    return false;
  }
  foreach ($webscan_white_url as $key => $value) {
    if(!empty($url_var)&&!empty($value)){
      if (stristr($url_path,$key)&&stristr($url_var,$value)) {
        return false;
      }
    }
    elseif (empty($url_var)&&empty($value)) {
      if (stristr($url_path,$key)) {
        return false;
      }
    }
  }
  return true;
}


if ($webscan_switch&&webscan_white($webscan_white_directory,$webscan_white_url)) {
  if ($webscan_get) {
    foreach($_GET as $key=>$value) {
      webscan_StopAttack($key,$value,$getfilter,"GET");
    }
  }
  if ($webscan_post) { 
    foreach($_POST as $key=>$value) {	
      webscan_StopAttack($key,$value,$postfilter,"POST");
    }	
  }
  if ($webscan_cookie) {
    foreach($_COOKIE as $key=>$value) {
      webscan_StopAttack($key,$value,$cookiefilter,"COOKIE");
    }
  }
  if ($webscan_referre) {
    foreach($webscan_referer as $key=>$value) {
      webscan_StopAttack($key,$value,$postfilter,"REFERRER");
    }
  }
}
