<?php
    if(C('LAYOUT_ON')) {
        echo '{__NOLAYOUT__}';
    }
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>错误提示 - TuziCMS</title>
<link rel="stylesheet" type="text/css" href="css/index.css">
<meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no">
<style type="text/css">
html,body,h1,div,p{margin:0;padding:0;}
img{border:none;}
body{background:#f8f8f8;}

.wrapper{width:280px; margin:0 auto;}
.header{text-indent:-9999em; width:221px; height:58px; margin:0 auto; margin-top:50px;}

.content{margin-top:30px; padding-bottom:50px;;font-size:18px;font-family: "Microsoft Yahei","微软雅黑",Tahoma,Arial,Helvetica,STHeiti;}

.segment{height:40px; padding:3px 3px 1px 3px; background:#52C134; line-height:40px; color:#FFFFFF; text-indent:6px;}
.segmentc1{ padding:6px 6px 6px 6px; border:1px solid #CCCCCC;text-indent:6px;}
.segment a{line-height:40px; display:inline-block; height:40px; width:133px; text-align:center; text-decoration:none; font-size:15px; color:#fff;}
.segment-c1 .segment-item1, .segment-c2 .segment-item2{color:#000;}



@media only screen and (-moz-min-device-pixel-ratio: 1.5), only screen and (-o-min-device-pixel-ratio: 3/2), only screen and (-webkit-min-device-pixel-ratio: 1.5), only screen and (min-device-pixel-ratio: 1.5) {
    .header {background:url(https://passport.sina.cn/images/signup/logo2x.png) no-repeat; background-size:221px 58px;}
    .segment, .segment-c1 .segment-item1, .segment-c2 .segment-item2, .input-w, .input-icon, .read-rule span, .large-btn, .toggle-password{background-size:280px 364px;}
}
</style>
</head>
<body>
	<div class="wrapper">
		<div style="height:150px;"></div>
		
		<!-- content begin -->
		<div class="content">
			
			<!-- segment begin -->
			<div class="segment segment-c1" id="segment">系统提醒</div>
			
			<div class="segmentc1">
				<div class="error" style="word-break: break-all; word-wrap:break-word;" align="center">
				<p class="face" >:(</p>
				<?php echo strip_tags($e['message']);?>
				<div class="content">
				<?php if(isset($e['file'])) {?>
				
				<?php }?>
				<?php if(isset($e['trace'])) {?>
					
				<?php }?>
				</div>
				</div>
			</div>
			
			<!-- segment end -->
		</div>
		<!-- content end -->
	</div>
</body>
</html>