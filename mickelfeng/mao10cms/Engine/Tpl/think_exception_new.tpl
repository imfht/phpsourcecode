<?php
    if(C('LAYOUT_ON')) {
        echo '{__NOLAYOUT__}';
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<title>系统发生错误</title>
<style type="text/css">
*{ padding: 0; margin: 0; }
html {text-rendering: optimizeLegibility;-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale;}
body {font-size: 16px;-ms-word-break: break-all;word-break: break-all;word-break: break-word;word-wrap: break-word;overflow-wrap: break-word;background: #fff;color: #747f8c;text-align: center; padding-top: 150px; line-height: 1.3;}
a {color: #ff4a00; text-decoration: none;}
a:hover {text-decoration: none; color: #ee330a;}
h1,h3,.info {margin: 10px 0;}
#infobox {padding: 15px; background: #ff4a00; color: #fff; margin: 10px 0; font-size: 12px;}
#infobox h4 {font-size: 14px;}
#infobox a {color: #fff;}
#wait {font-size: 40px; font-weight: bold; background: #fff; color: #ff4a00; display: block; width: 100px; border-radius: 50px; margin: 20px auto; text-align: center; height: 100px; line-height: 100px;}
</style>
</head>
<body>
<?php 
$site_url = mc_option('site_url');
?>
<div class="error">
<a id="href" title="官方网站" href="<?php echo $site_url; ?>"><img src="<?php echo $site_url; ?>/Engine/Tpl/logo.png"></a>
<h1>错误404</h1>
<h3>您要访问的页面没有找到，正在返回首页！</h3>
<div id="infobox">
<div class="info">
	<span id="wait">10</span>
</div>
<script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
var interval = setInterval(function(){
	var time = --wait.innerHTML;
	if(time <= 0) {
		location.href = href;
		clearInterval(interval);
	};
}, 1000);
})();
</script>
</div>
</div>
<div class="copyright">
<p><a title="官方网站" href="http://www.mao10.com/">Mao10CMS</a> 新概念社交网络商城建站系统</p>
</div>
</body>
</html>
