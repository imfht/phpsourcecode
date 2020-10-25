<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="Keywords" content="代码工厂">
        <meta name="Description" content="每天自学一点点，每天进步一点点">
        <meta name="author" content="Clmao">
        <meta http-equiv="Cache-Control" content="no-transform" />
        <meta http-equiv="Cache-Control" content="no-siteapp"/>
        <title>ClmaoBlog V1.0 安装</title>
        <link rel="apple-touch-icon" href="/Public/appicon.png">
        <link rel="shortcut icon" href="/Public/appicon.png">
        <link href="/Public/zui/zui.min.css" rel="stylesheet">
        <script src="/Public/js/jquery.min.js"></script>
        <script src="/Public/zui/zui.min.js"></script>
        <style>
            .panel{margin: 0 auto;max-width: 80%;}
            .btn{margin-right: 20px;}
            .input-group{margin-bottom: 10px;max-width: 80%;}
        </style>
    </head>
    
   
 <body>
        <nav class="navbar navbar-default" role="navigation">
            <ul class="nav navbar-nav nav-justified">
                <li class="cat-item nav-system-home active"><a href="/">安装协议</a></li>
                <li class="cat-item"><a href="javascript:;">环境检测</a></li>
                <li class="cat-item"><a href="javascript:;">创建数据库</a></li>
                <li class="cat-item"><a href="javascript:;">安装</a></li>
                <li class="cat-item"><a href="javascript:;">完成</a></li>
            </ul>
        </nav>
        <div class="panel panel-success">
            <div class="panel-heading">ClmaoBlog安装协议</div>
            <div class="panel-body">
                 <p>ClmaoBlog遵循Apache Licence2开源协议，并且免费使用（但不包括其衍生产品、插件或者服务）。Apache Licence是著名的非盈利开源组织Apache采用的协议。该协议和BSD类似，鼓励代码共享和尊重原作者的著作权，允许代码修改，再作为开源或商业软件发布。需要满足的条件：</p>
                <p>1． 需要给用户一份Apache Licence ；</p>
                <p>2． 如果你修改了代码，需要在被修改的文件中说明；</p>
                <p>3． 在延伸的代码中（修改和有源代码衍生的代码中）需要带有原来代码中的协议，商标，专利声明和其他原来作者规定需要包含的说明；</p>
                <p>4． 如果再发布的产品中包含一个Notice文件，则在Notice文件中需要带有本协议内容。你可以在Notice中增加自己的许可，但不可以表现为对Apache Licence构成更改。</p>
                <p><a href="<?php echo U('Install/step1');?>" class="btn btn-primary">同意安装协议</a><a href="http://blog.clmao.com" class="btn btn-default">不同意</a></p>
            </div>
        </div>
        
    </body>
</html>