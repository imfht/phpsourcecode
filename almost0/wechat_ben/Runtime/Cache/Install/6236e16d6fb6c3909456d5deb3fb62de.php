<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>WeiPHP 安装</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <!-- Le styles -->
        <link href="/jae_wechat_ben/Public/static/bootstrap/css/bootstrap.css?v=<?php echo SITE_VERSION;?>" rel="stylesheet">
        <link href="/jae_wechat_ben/Public/static/bootstrap/css/bootstrap-responsive.css?v=<?php echo SITE_VERSION;?>" rel="stylesheet">
        <link href="/jae_wechat_ben/Public/Install/css/install.css?v=<?php echo SITE_VERSION;?>" rel="stylesheet">

        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
            <script src="assets/js/html5shiv.js?v=<?php echo SITE_VERSION;?>"></script>
        <![endif]-->
        <script src="/jae_wechat_ben/Public/static/jquery-1.10.2.min.js"></script>
        <script src="/jae_wechat_ben/Public/static/bootstrap/js/bootstrap.js?v=<?php echo SITE_VERSION;?>"></script>
    </head>

    <body data-spy="scroll" data-target=".bs-docs-sidebar">
        <!-- Navbar
        ================================================== -->
        <div class="navbar navbar-inverse navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="brand" target="_blank" href="http://www.onethink.cn">OneThink</a>
                    <div class="nav-collapse collapse">
                    	<ul id="step" class="nav">
                    		
    <li class="active"><a href="javascript:;">安装协议</a></li>
    <li class="active"><a href="javascript:;">环境检测</a></li>
    <li class="active"><a href="javascript:;">创建数据库</a></li>
    <li><a href="javascript:;">安装</a></li>
    <li><a href="javascript:;">完成</a></li>

                    	</ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="jumbotron masthead">
            <div class="container">
                
    <?php
 defined('SAE_MYSQL_HOST_M') or define('SAE_MYSQL_HOST_M', '127.0.0.1'); defined('SAE_MYSQL_HOST_M') or define('SAE_MYSQL_PORT', '3306'); ?>
    <h1>创建数据库</h1>
    <form action="/jae_wechat_ben/install.php?m=Install&c=Install&a=step2" method="post" target="_self">
        <div class="create-database">
            <h2>数据库连接信息</h2>
            <div>
                <select name="db[]" style="height:38px; padding:8px;">
                    <option>mysql</option>
                    <option>mysqli</option>
                </select>
                <span>数据库连接类型，服务器支持的情况下建议使用mysqli</span>
            </div>
            <div>
                <input type="text" name="db[]" value="<?php if(defined("SAE_MYSQL_HOST_M")): echo (SAE_MYSQL_HOST_M); else: ?>127.0.0.1<?php endif; ?>">
                <span>数据库服务器，数据库服务器IP，一般为127.0.0.1</span>
            </div>
            <div>
                <input type="text" name="db[]" value="<?php if(defined("SAE_MYSQL_DB")): echo (SAE_MYSQL_DB); endif; ?>">
                <span>数据库名</span>
            </div>
            <div>
                <input type="text" name="db[]" value="<?php if(defined("SAE_MYSQL_USER")): echo (SAE_MYSQL_USER); endif; ?>">
                <span>数据库用户名</span>
            </div>
            <div>
                <input type="password" name="db[]" value="<?php if(defined("SAE_MYSQL_PASS")): echo (SAE_MYSQL_PASS); endif; ?>">
                <span>数据库密码</span>
            </div>

            <div>
                <input type="text" name="db[]" value="<?php if(defined("SAE_MYSQL_PORT")): echo (SAE_MYSQL_PORT); else: ?>3306<?php endif; ?>">
                <span>数据库端口，数据库服务连接端口，一般为3306</span>
            </div>

            <div>
                <input type="text" name="db[]" value="wp_">
                <span>数据表前缀，同一个数据库运行多个系统时请修改为不同的前缀</span>
            </div>
        </div>

        <div class="create-database">
            <h2>创始人帐号信息</h2>
            <div>
                <input type="text" name="admin[]" value="Administrator">
                <span>用户名</span>
            </div>
            <div>
                <input type="password" name="admin[]" value="">
                <span>密码</span>
            </div>
            <div>
                <input type="password" name="admin[]" value="">
                <span>确认密码</span>
            </div>
            <div>
                <input type="text" name="admin[]" value="">
                <span>邮箱，请填写正确的邮箱便于收取提醒邮件</span>
            </div>
        </div>
    </form>

            </div>
        </div>


        <!-- Footer
        ================================================== -->
        <footer class="footer navbar-fixed-bottom">
            <div class="container">
                <div>
                	
    <a class="btn btn-success btn-large" href="<?php echo U('Install/step1');?>">上一步</a>
    <button id="submit" type="button" class="btn btn-primary btn-large" onclick="$('form').submit();return false;">下一步</button>

                </div>
            </div>
        </footer>
    </body>
</html>