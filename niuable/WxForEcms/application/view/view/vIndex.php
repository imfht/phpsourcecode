<?php defined('APP_PATH') OR exit('No direct script access allowed');?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<title>{$title??''}</title>
	<script src="{$public}../jquery-2.1.4/jquery.min.js"></script>
	<script src="{$public}../js/main.js"></script>
	<link href="{$public}../css/bootstrap.min.css" rel="stylesheet">
	<script src="{$public}../js/bootstrap.js"></script>
	<script type="text/javascript" src="{$public}../editor/ueditor.parse.js"></script>

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    	<script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    	<script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style type="text/css">
    	html,body{
    	   width:100%;
    		max-width:100%;
    		margin: 0 auto;
     		padding:0;
     		word-break:break-word;
     		
    	}
    	.row{
    	   padding:0px;
    	   margin:0;
    	}
    	img,video{
    		max-width:100%;
    	}
    	.ueditor_baidumap div{
    		min-width:98%;
    	}
    </style>
</head>
<body>
<div class="row">
	<div class="col-md-12" id="header">
		<h3>{$title??''}</h3>
		<h4>
			<?php if(isset($author) && !empty($author)){?>
				<small><?php echo '作者：'.$author?></small>
			<?php }?>
			<span class="text-success" title="公众号代号" >{$wx['name']??''}</span>
		</h4>
	</div>
	<div class="col-md-12" id="content">
		<?php if($is_link_img){?>
			<img class="img-responsive" src="{$title_img??''}" style="width: 99% ;margin:0 auto">
		<?php }?>
		<?php if($is_open_outside==1){  ?> 
			<iframe id="mainFrame" name="mainFrame" width="100%" scrolling="no" onload="changeFrameHeight()" frameborder="0" src="{$outside_url??''}"></iframe>
		<?php }else{ ?>
			<?php echo $content;?>
		<?php }?>
	</div>
	<div class="col-md-12" id="footer">
		<?php if($url){?>
			<h4><a href="<?=$url?>" target="_blank">阅读原文</a></h4>
		<?php }?>
	</div>
</div>
<script type="text/javascript">

uParse('#content', {
    rootPath: '{$public}../editor/'
});

<?php if ($is_open_outside) {?>
	startInit('mainFrame', 560);
	var browserVersion = window.navigator.userAgent.toUpperCase();
	var isOpera = browserVersion.indexOf("OPERA") > -1 ? true : false;
	var isFireFox = browserVersion.indexOf("FIREFOX") > -1 ? true : false;
	var isChrome = browserVersion.indexOf("CHROME") > -1 ? true : false;
	var isSafari = browserVersion.indexOf("SAFARI") > -1 ? true : false;
	var isIE = (!!window.ActiveXObject || "ActiveXObject" in window);
	var isIE9More = (! -[1, ] == false);
	function reinitIframe(iframeId, minHeight) {
	    try {
	        var iframe = document.getElementById(iframeId);
	        var bHeight = 0;
	        if (isChrome == false && isSafari == false)
	            bHeight = iframe.contentWindow.document.body.scrollHeight;

	        var dHeight = 0;
	        if (isFireFox == true)
	            dHeight = iframe.contentWindow.document.documentElement.offsetHeight + 2;
	        else if (isIE == false && isOpera == false)
	            dHeight = iframe.contentWindow.document.documentElement.scrollHeight;
	        else if (isIE == true && isIE9More) {//ie9+
	            var heightDeviation = bHeight - eval("window.IE9MoreRealHeight" + iframeId);
	            if (heightDeviation == 0) {
	                bHeight += 3;
	            } else if (heightDeviation != 3) {
	                eval("window.IE9MoreRealHeight" + iframeId + "=" + bHeight);
	                bHeight += 3;
	            }
	        }
	        else//ie[6-8]、OPERA
	            bHeight += 3;

	        var height = Math.max(bHeight, dHeight);
	        if (height < minHeight) height = minHeight;
	        iframe.style.height = height + "px";
	    } catch (ex) { }
	}
	function startInit(iframeId, minHeight) {
	    eval("window.IE9MoreRealHeight" + iframeId + "=0");
	    window.setInterval("reinitIframe('" + iframeId + "'," + minHeight + ")", 100);
	} 
<?php }?>

</script>
</body>
</html>
