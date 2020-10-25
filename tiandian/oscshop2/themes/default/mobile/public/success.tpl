{__NOLAYOUT__}<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <link href="__PUBLIC__/jquery-weui/dist/lib/weui.min.css" type="text/css" rel="Stylesheet" />
    <title>跳转提示</title>
</head>
<body>
	<div class="weui_msg">
	    <div class="weui_icon_area"><i class="weui_icon_success weui_icon_msg"></i></div>
	    <div class="weui_text_area">
	        <h2 class="weui_msg_title"><?php echo(strip_tags($msg));?></h2>	      
	    </div>
	    <div class="weui_opr_area">
	        <p class="weui_btn_area">
	            <a href="<?php echo($url);?>" class="weui_btn weui_btn_primary">返回</a>
	        </p>
	    </div>
	   
	</div>
    
    <script type="text/javascript">     
        	setTimeout(function(){
				location.href = '<?php echo($url);?>';
			},3000);          
    </script>
</body>
</html>
