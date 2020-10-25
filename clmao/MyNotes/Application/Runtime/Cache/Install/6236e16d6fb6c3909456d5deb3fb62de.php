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
     <?php
 defined('SAE_MYSQL_HOST_M') or define('SAE_MYSQL_HOST_M', '127.0.0.1'); defined('SAE_MYSQL_HOST_M') or define('SAE_MYSQL_PORT', '3306'); ?>
        <nav class="navbar navbar-default" role="navigation">
            <ul class="nav navbar-nav nav-justified">
                <li class="cat-item nav-system-home active"><a href="/">安装协议</a></li>
                <li class="cat-item active"><a href="javascript:;">环境检测</a></li>
                <li class="cat-item active"><a href="javascript:;">创建数据库</a></li>
                <li class="cat-item"><a href="javascript:;">安装</a></li>
                <li class="cat-item"><a href="javascript:;">完成</a></li>
            </ul>
        </nav>
     <form action="/install.php/Install/Install/step2.html" method="post" target="_self">
<div class="panel panel-success">
            <div class="panel-heading">ClmaoBlog创建数据库</div>
            <div class="panel-body">
                <h4 class="text-primary">数据库连接信息</h4>
                  <div class="input-group">
                    <span class="input-group-addon">数据库类型</span>
                    <input type="text"  name="db[]" value="mysql" readonly="readonly" class="form-control" placeholder="数据库类型">
                    <span disabled="true"  class="input-group-addon fix-border fix-padding"></span>
                  </div>
                
                  <div class="input-group">
                    <span class="input-group-addon">数据库服务器IP</span>
                    <input type="text"  name="db[]" value="<?php if(defined("SAE_MYSQL_HOST_M")): echo (SAE_MYSQL_HOST_M); else: ?>127.0.0.1<?php endif; ?>" class="form-control" placeholder="数据库服务器IP">
                    <span class="input-group-addon fix-border fix-padding"></span>
                  </div>
                    
                  <div class="input-group">
                    <span class="input-group-addon">数据库名</span>
                    <input type="text"  name="db[]" value="<?php if(defined("SAE_MYSQL_DB")): echo (SAE_MYSQL_DB); endif; ?>" class="form-control" placeholder="数据库名">
                    <span class="input-group-addon fix-border fix-padding"></span>
                  </div>
                    
                  <div class="input-group">
                    <span class="input-group-addon">数据库用户名</span>
                    <input type="text"  name="db[]" class="form-control" value="<?php if(defined("SAE_MYSQL_USER")): echo (SAE_MYSQL_USER); endif; ?>"placeholder="数据库用户名">
                    <span class="input-group-addon fix-border fix-padding"></span>
                  </div>
                     
                  <div class="input-group">
                    <span class="input-group-addon">数据库密码</span>
                    <input type="password" name="db[]"value="<?php if(defined("SAE_MYSQL_PASS")): echo (SAE_MYSQL_PASS); endif; ?>" class="form-control" placeholder="数据库密码">
                    <span class="input-group-addon fix-border fix-padding"></span>
                  </div>
                     
                  <div class="input-group">
                    <span class="input-group-addon">数据库端口</span>
                    <input type="text"  name="db[]" value="<?php if(defined("SAE_MYSQL_PORT")): echo (SAE_MYSQL_PORT); else: ?>3306<?php endif; ?>"class="form-control" placeholder="数据库端口">
                    <span class="input-group-addon fix-border fix-padding"></span>
                </div>
                     
                  <div class="input-group">
                    <span class="input-group-addon">数据表前缀</span>
                    <input type="text"  name="db[]" value="clmao_" class="form-control" placeholder="数据表前缀">
                    <span class="input-group-addon fix-border fix-padding"></span>
                  </div>
                <hr/>
                <h4 class="text-primary">管理员信息</h4>
                <div class="input-group">
                    <span class="input-group-addon">用户名</span>
                    <input type="text" name="admin[]" class="form-control" placeholder="用户名">
                    <span class="input-group-addon fix-border fix-padding"></span>
                  </div>
                <div class="input-group">
                    <span class="input-group-addon">密码</span>
                    <input type="password" name="admin[]" class="form-control" placeholder="密码">
                    <span class="input-group-addon fix-border fix-padding"></span>
                  </div>
                <br/>
                <p><a href="<?php echo U('Install/step1');?>" class="btn btn-default">上一步</a><a href=""onclick="$('form').submit();return false;" class="btn btn-primary">下一步</a></p>
            </div>
        </div>
     </form>
        
    </body>
</html>