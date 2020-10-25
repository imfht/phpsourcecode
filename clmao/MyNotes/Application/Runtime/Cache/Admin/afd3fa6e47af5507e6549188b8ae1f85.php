<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
    <head>
        <title>页面提示</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
       <link rel="apple-touch-icon" href="/Public/appicon.png">
        <link rel="shortcut icon" href="/Public/appicon.png">
        <link href="/Public/zui/zui.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="panel panel-success" style="max-width: 800px;margin: 0 auto;margin-top: 20px;">
            <div class="panel-heading">系统提示</div>
            <div class="panel-body">
                <?php if(isset($message)): ?><p class="lead"><?php echo($message?$message:'此处显示提示信息!!!'); ?></p>
                    <?php else: ?>
                    <p class="lead"><?php echo($error?$error:'此处显示提示信息!!!'); ?></p><?php endif; ?>
            <p class="text-warning">
                    系统会在 <span class="label label-important" id="wait"><?php echo($waitSecond?$waitSecond:5); ?></span> 秒内自动跳转，如果没有自动跳转，请<a id="href" href="<?php echo($jumpUrl); ?>">点击这里</a>进行跳转，或<a href="<?php echo U('/');?>">点击这里</a>返回首页。
                </p>
            </div>
          </div>
        
        
        
        <script>
            (function(){
                var wait = document.getElementById('wait'),href = document.getElementById('href').href;
                var interval = setInterval(function(){
                    var time = --wait.innerHTML;
                    if(time == 0) {
                        location.href = href;
                        clearInterval(interval);
                    };
                }, 1000);
            })();
        </script>
    </body>
</html>