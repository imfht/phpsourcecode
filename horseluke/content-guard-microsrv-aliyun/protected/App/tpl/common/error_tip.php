<?php
use SCH60\Kernel\StrHelper;
?>
<!DOCTYPE html>
<html manifest="N404.manifest">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, height=device-height" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<title>错误提示</title>
<style type="text/css">
body {
	margin: 0 auto;
	padding: 0;
	background-color: #eee;
	font-size: 62.5%;
}

@media screen and (max-width:639px) {
	body {
		width: 100%;
		margin: 0 auto;
	}
}

@media screen and (min-width: 640px) {
	body {
		width: 640px;
	}
}

.container {
	margin: 0 auto;
}

.header img {
	width: 100%;
	text-align: center;
	vertical-align: middle;
	border: none;
}

.textblock {
	margin: 1.5em 1em;
}

.heading {
	margin-top: 2.1em;
	width: 34%;
}

.heading img {
	width: 100%;
	min-width: 109px;
}

.font01 {
	font-size: 2.2em;
	font-family: "微软雅黑", "黑体";
	color: black;
	line-height: 1.5em;
}

.font01 table{
    font-size: 0.8em;
}

.font01 p {
	margin: 0.5em 0;
}

.footer {text-align: right;color:black;font-size: 1.5em;margin: 1.5em 1em;}
.footer a{color:black;}
.footer p{padding:  0.5em 0;}

</style>

</head>

<body>
	<div class="container">
	    <div style="height:100px;"></div>
	    
		<div class="font01 textblock">
		    <p id="tipstr">错误提示</p>
			<p id="tipstr"><?=$isHtml ? $tip : htmlspecialchars($tip);?></p>
		</div>

		<div class="font01 textblock">
		</div>

     <div class="footer">
         <p>
                       <?php
                       $tipLen = mb_strlen($tip);
                       $waitMilsec = 3000 + intval($tipLen / 30) * 1000;
                       ?>
		           <a href="<?php if(!empty($redirectUrl)):?><?=$redirectUrl?><?php else: ?>javascript:history.back();<?php endif; ?>"><span id="tip-x-countdown"><?=$waitMilsec/1000;?></span>秒后自动跳转，没有反应请点击此。</a>
                       <script>
                                setTimeout(function(){
                                	<?php if(!empty($redirectUrl)):?>
                                	     window.location.href = "<?=$redirectUrl?>";
                                	<?php else: ?>
                                	    window.history.back();
                                	<?php endif; ?>
                                }, <?=$waitMilsec;?>);
                                
                                var createCountDown = function(){
                                    var dom = document.getElementById("tip-x-countdown");
                                    var sec = parseInt(dom.innerHTML);
                                    if(isNaN(sec) || sec <= 1){
                                        return ;
                                    }
                                    sec = sec - 1;
                                    dom.innerHTML = sec;
                                    setTimeout(createCountDown, 1000);
                                };

                                setTimeout(createCountDown, 1000);
                       </script>
         </p>
         
        <p>
            <a href="javascript:window.close();">关闭窗口</a>
            &nbsp;|&nbsp;<a href="<?=StrHelper::url()?>">返回首页</a>
        </p>
    </div>
	
    <div class="footer">
        <p></p>
    </div>

	</div>
</body>
</html>
