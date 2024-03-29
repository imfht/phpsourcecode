<?php /*a:1:{s:71:"D:\phpstudy_pro\WWW\git\DSMall\app\admin\view\public\dispatch_jump.html";i:1591845922;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo htmlentities(lang('jump_message')); ?></title>
    <style type="text/css">
        *{ padding: 0; margin: 0; }
        body{ background: #fff; font-family: "Microsoft Yahei","Helvetica Neue",Helvetica,Arial,sans-serif; color: #333; font-size: 12px; }
        .system-message{margin:auto;width:400px;border-radius:5px;border:1px solid #D4D4D4;margin-top:100px;}
        .system-message h1{font-weight: normal; line-height:32px;height:32px;font-size:16px;background:#FF9933;color:#fff;padding:5px 0;}
        .system-message h1 img{float:left;margin:0 10px;}
        .system-message .success,.system-message .error{ line-height:32px; font-size:20px;text-align:center;margin:10px 20px;}
        .system-message .detail{ font-size: 12px; line-height: 20px; margin-top: 12px; display: none; }
        .system-message .jump{ padding:15px 0; text-align:center;background:#F9F9F9;font-size:13px;}
        .system-message .jump a{ color: #333; }
    </style>
</head>
<body>
    <div class="system-message">
        <?php switch ($code) {case 1:?>
            <h1><img src="<?php echo htmlentities(ADMIN_SITE_ROOT); ?>/images/dispatch_jump_success.png"/><?php echo htmlentities(lang('ds_common_op_succ')); ?>!</h1>
            <p class="success"><?php echo(strip_tags($msg));?></p>
            <?php break;case 0:?>
            <h1><img src="<?php echo htmlentities(ADMIN_SITE_ROOT); ?>/images/dispatch_jump_error.png"/><?php echo htmlentities(lang('has_error')); ?>!</h1>
            <p class="error"><?php echo(strip_tags($msg));?></p>
            <?php break;} ?>
        <p class="detail"></p>
        <p class="jump"><?php echo htmlentities(lang('page_auto')); ?> <a id="href" href="<?php echo htmlentities($url); ?>"><?php echo htmlentities(lang('jump')); ?></a> <?php echo htmlentities(lang('wait_time')); ?>： <b id="wait"><?php echo htmlentities($wait); ?></b></p>
    </div>
    <script type="text/javascript">
        (function(){
            var wait = document.getElementById('wait'),
                href = document.getElementById('href').href;
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
