<?php if(!defined('HDPHP_PATH'))exit;C('SHOW_NOTICE',FALSE);?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>网站暂时关闭</title>
    <style type="text/css">
        div{
            font-family: '微软雅黑', Microsoft Yahei, Hiragino Sans GB, WenQuanYi Micro Hei, sans-serif;
            font-size:58px;
            color:#419641;
            padding: 20px;
        }
    </style>
</head>
<body>
<div>
    <?php echo $hd['config']['WEB_CLOSE_MESSAGE'];?>
</div>
</body>
</html>