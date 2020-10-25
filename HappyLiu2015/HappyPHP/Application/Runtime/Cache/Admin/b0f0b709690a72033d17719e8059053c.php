<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html lang='zh-cn'>
<head>
    <meta charset="utf-8" />
    <title>后台管理系统</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!--//老样式-->
    <link href="/test/Public/static/assets/css/dpl-min.css" rel="stylesheet" type="text/css" />
    <link href="/test/Public/static/assets/css/bui-min.css" rel="stylesheet" type="text/css" />
    <!--//新样式
    <link href="http://g.alicdn.com/bui/bui/1.1.21/css/bs3/dpl.css" rel="stylesheet">
    <link href="http://g.alicdn.com/bui/bui/1.1.21/css/bs3/bui.css" rel="stylesheet">
    <link href="/test/Public/static/assets/css/dpl-new.css" rel="stylesheet" type="text/css" />
    <link href="/test/Public/static/assets/css/bui-new.css" rel="stylesheet" type="text/css" />-->
    <link href="/test/Public/Admin/css/page.css" rel="stylesheet" type="text/css" />
    
</head>
<body>
<div class="container">

<!--服务器相关参数-->
<table class="table table-bordered">
    <tr class="success"><th colspan="2">服务器参数</th></tr>
    <tr>
        <td width="15%">服务器域名/IP地址</td>
        <td><?php echo @get_current_user();?> - <?php echo $_SERVER['SERVER_NAME'];?>(<?php if('/'==DIRECTORY_SEPARATOR){echo $_SERVER['SERVER_ADDR'];}else{echo @gethostbyname($_SERVER['SERVER_NAME']);} ?>)&nbsp;&nbsp;你的IP地址是：<?php echo @$_SERVER['REMOTE_ADDR'];?></td>
    </tr>
    <tr class="active">
        <td>服务器操作系统</td>
        <td><?php $os = explode(" ", php_uname()); echo $os[0];?> &nbsp;内核版本：<?php if('/'==DIRECTORY_SEPARATOR){echo $os[2];}else{echo $os[1];} ?></td>
        </tr>
    <tr>
        <td>服务器主机名</td>
        <td><?php if('/'==DIRECTORY_SEPARATOR ){echo $os[1];}else{echo $os[2];} ?></td>
    </tr>
    <tr class="active">
        <td>服务器解译引擎</td>
        <td><?php echo $_SERVER['SERVER_SOFTWARE'];?></td>
    </tr>
    <tr>
        <td>mysql版本</td>
        <td><?php echo ($mysql_version); ?></td>
    </tr>
    <tr class="active">
        <td>服务器端口</td>
        <td><?php echo $_SERVER['SERVER_PORT'];?></td>
    </tr>
    <tr>
        <td>绝对路径</td>
        <td><?php echo $_SERVER['DOCUMENT_ROOT']?str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']):str_replace('\\','/',dirname(__FILE__));?></td>
    </tr>
</table>
<!-- 产品信息 -->
<table class="table table-bordered">
    <tr class="danger"><th colspan="2">产品信息</th></tr>
    <tr>
        <td width="15%">产品名称</td>
        <td>THinkPHP+Bui+Bootstrap后台集成框架</td>
    </tr>
    <tr>
        <td>版本：</td>
        <td>Version 1.0</td>
    </tr>
    <tr class="active">
        <td>框架作者：</td>
        <td>HappyLiu 网址：<a href="http://91happy.wang" target="_blank">http://91happy.wang</a></td>
    </tr>
    <tr class="active">
        <td>产品设计与研发团队</td>
        <td>HappyPHP开发团队</td>
    </tr>
    <tr>
        <td>版权所有</td>
        <td>框架免费开源，欢迎有兴趣的朋友提供意见或自行改进后分享。</td>
    </tr>
</table>


</div>
</body>
<!-- /内容区 -->
<!--<script type="text/javascript" src="/test/Public/static/assets/js/jquery-1.8.1.min.js"></script>-->
<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/test/Public/static/assets/js/bui.js"></script>
<script type="text/javascript" src="/test/Public/static/assets/js/config.js"></script>
<script type="text/javascript">
    (function () {
        var ThinkPHP = window.Think = {
            "ROOT": "/test", //当前网站地址
            "APP": "/test/index.php", //当前项目地址
            "PUBLIC": "/test/Public", //项目公共目录地址
            "DEEP": "<?php echo C('URL_PATHINFO_DEPR');?>", //PATHINFO分割符
            "MODEL": ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],
            "VAR": ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"]
        }
    })();
</script>

</html>