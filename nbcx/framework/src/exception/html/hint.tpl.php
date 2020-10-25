<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Hint&Newbie</title>
<style type="text/css">
*{ padding: 0; margin: 0; }
body{ background: #fff; font-family: '微软雅黑'; color: #333; font-size: 16px; }
.system-message{ padding: 24px 48px; }
.system-message h1{ font-size: 100px; font-weight: normal; line-height: 120px; margin-bottom: 12px; }
.system-message .jump{ padding-top: 10px}
.system-message .success{ line-height: 1.8em; font-size: 36px }
.copyright{ padding: 12px 48px; color: #999; }
.copyright a{ color: #000; text-decoration: none; }
</style>
</head>
<body>
<?php 
$hint = isset($hint)?$hint:'PAGE NOT FOUND!';
$message = isset($message)?$message:'Sorry, are working on it. Please check again later.';
?>
<div class="system-message">
<h1>:)</h1>
    <p class="success"><?php echo($hint); ?></p>
    <p class="jump"><?php echo($message); ?></p>
    <?php if(isset($url)){
        $wait = isset($wait)?$wait:3;
        ?>
        <p class="jump">
            页面自动 <a id="href" href="<?=$url?>">跳转</a> 等待时间： <b id="wait"><?php echo($wait); ?></b>
        </p>
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
    <?php }?>
</div>
<div class="copyright">
    <p><a title="官方网站" href="http://nb.cx" target="_blank">NB Framework</a><sup><?=__VER__?></sup> { Fast & Simple OOP PHP Framework } -- [ We Can Do It Just NB Framework ]</p>
</div>
</body>
</html>
