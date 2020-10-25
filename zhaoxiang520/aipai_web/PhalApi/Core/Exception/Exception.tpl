<!doctype html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>Sevens - 系统发生错误</title>
    <link href="http://cdn.bootcss.com/semantic-ui/2.2.4/semantic.min.css" rel="stylesheet">
</head>
<body>
<div class="ui very padded piled red text container segment" style="margin-top:3%; max-width: none !important;">
    <h1 class="ui orange header huge"><i class="warning sign icon"></i></h1>
    <h2 class="ui header huge"><?php echo strip_tags($e['message']);?></h2>
    <?php if(isset($e['file'])) {?>
    <h4>错误位置</h4>
    <p>FILE: <?php echo $e['file'] ;?> &#12288;LINE: <?php echo $e['line'];?></p>
    <?php }?>
    <?php if(isset($e['trace'])) {?>
    <h4>TRACE</h4>
    <p><?php echo nl2br($e['trace']);?></p>
    <?php }?>
    <div class="ui clearing divider"></div>
    <div class="ui right aligned container">
        <p><a href="http://www.aixiuaipai.com/">Sevens</a><sup><?php echo APP_VERSION; ?></sup> { Fast & Simple API Framework }</p>
    </div>
</div>
</body>
</html>