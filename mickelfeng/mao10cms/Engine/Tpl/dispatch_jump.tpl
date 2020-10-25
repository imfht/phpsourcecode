<?php
    if(C('LAYOUT_ON')) {
        echo '{__NOLAYOUT__}';
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>跳转提示</title>
<style type="text/css">
*{ padding: 0; margin: 0; }
body{ background: #fff; font-family: "Helvetica Neue", Helvetica, Microsoft Yahei, Hiragino Sans GB, WenQuanYi Micro Hei, sans-serif; color: #483b4f; font-size: 16px; }
.system-message{ text-align: center; }
.system-message h1{ font-size: 100px; font-weight: normal; line-height: 120px; margin-bottom: 12px; }
.system-message .jump{ padding-top: 10px}
.system-message .jump a{ color: #fff; text-decoration: none; font-size: 26px; background-color: #ff4a00; display: inline-block; width: 60px; line-height: 60px; height: 60px; border-radius: 50%;}
.system-message .success,.system-message .error{ line-height: 1.8em; font-size: 40px; margin-bottom: 40px; font-weight: 400; letter-spacing:5px;}
.system-message .detail{ font-size: 12px; line-height: 20px; margin-top: 12px; display:none}

@media (min-width: 1200px) {
	.system-message{ padding: 330px 0;}
}
@media (min-width: 992px) and (max-width: 1199px) {
	.system-message{ padding: 250px 0;}
}
@media (min-width: 769px) and (max-width: 991px) {
	.system-message{ padding: 180px 0;}
}
@media (max-width: 768px) {
	.system-message{ padding: 120px 0;}
}
@media (max-width: 480px) {
	.system-message{ padding: 80px 0;}
}
</style>
</head>
<body>
<div class="system-message">
<present name="message">
<p class="success"><?php echo($message); ?></p>
<else/>
<p class="error"><?php echo($error); ?></p>
</present>
<p class="detail"></p>
<p class="jump">
<a id="href" href="<?php echo($jumpUrl); ?>"><b id="wait"><?php echo($waitSecond); ?></b></a>
</p>
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
</body>
</html>
