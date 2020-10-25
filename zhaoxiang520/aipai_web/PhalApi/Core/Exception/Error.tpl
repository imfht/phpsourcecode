<!doctype html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>Sevens - 系统发生错误</title>
    <link href="http://cdn.bootcss.com/semantic-ui/2.2.4/semantic.min.css" rel="stylesheet">
</head>
<body>
<div class="ui very padded piled red text container segment" style="margin-top:3%; max-width: none !important;">
    <h1 class="ui orange header huge"><i class="red remove circle icon"></i></h1>
    <h2 class="ui header huge"><?php echo strip_tags($e['message']);?></h2>
    <div class="ui clearing divider"></div>
    <div class="ui right aligned container">
        <p><a href="http://www.aixiuaipai.com/">Sevens</a><sup><?php echo APP_VERSION; ?></sup> { Fast & Simple API Framework }</p>
    </div>
</div>
</body>
</html>