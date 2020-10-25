<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="Keywords" content="<?php @autoKey($actionName,$categoryName); ?>">
        <meta name="Description" content="<?php @autoDec($actionName,$categoryName,$contentName,$contentDec); ?>">
        <meta name="author" content="Clmao">
        <meta http-equiv="Cache-Control" content="no-transform" />
        <meta http-equiv="Cache-Control" content="no-siteapp"/>
        <title><?php @autoTitle($actionName,$categoryName,$contentName); ?></title>
        <link rel="apple-touch-icon" href="/Public/appicon.png">
        <link rel="shortcut icon" href="/Public/appicon.png">
        <link href="/Public/zui/zui.min.css" rel="stylesheet">
        <link href="/Public/zui/example.css" rel="stylesheet">
        <script src="/Public/js/jquery.min.js"></script>
        <script src="/Public/zui/zui.min.js"></script>
    <?php if($actionName == 'content' ): ?><link rel="stylesheet" href="/Public/ueditor/third-party/SyntaxHighlighter/shCoreDefault.css" />
        <?php else: endif; ?>
    <?php echo getSiteOption('headerPlus'); ?>
</head>

        <script type="text/javascript">
            verifyURL = '<?php echo U("/Home/Index/verify",'','');?>';
            function change_code(obj) {
                $("#code").attr("src", verifyURL + '/' + Math.random());
                return false;
            }
        </script>
        <style>
            .panel{ max-width: 500px;margin: 0 auto;margin-top: 10px;}
            .input-group{ max-width: 95%;margin: 0 auto;margin-top: 10px;}
            .code{margin-left: 20px;margin-top: 10px;margin-bottom: 10px;}
        </style>
    </head>
    <body>
        <div class="panel">
            <form role="form" action='<?php echo U("Home/Index/checkLogin");?>' method="post">
             <div class='panel-heading'>用户登陆</div>
            <section id='input-groups' class="page-section">

                <div class="input-group">
                  <span class="input-group-addon"><i class='icon-user'></i></span>
                    <input type="text"  name="user" class="form-control" required='required' placeholder="请输入用户名">
                  <span class="glyphicon glyphicon-star"></span>
                </div>
                
                <div class="input-group">
                  <span class="input-group-addon"><i class="icon-key"></i></span>
                    <input type="password"  name="pwd" class="form-control"required='required' placeholder="请输入密码">
                  <span class="glyphicon glyphicon-star"></span>
                </div>
                
                <div class="input-group">
                  <span class="input-group-addon"><i class="icon-chevron-down"></i></span>
                    <input type="text"  name="code" class="form-control"required='required' placeholder="请输入验证码">
                  <span class="glyphicon glyphicon-star"></span>
                </div>
                 <div class="code"> 
                    <img src="<?php echo U('Home/Index/verify');?>"onClick="change_code(this)" id="code"/>
                </div>
            
                
                <div class="code">
                    <input type="submit" value='登陆' class="btn btn-primary" />
                    &nbsp;&nbsp;&nbsp;&nbsp;<input type="reset" value='清空' class="btn btn-warning" />
                </div>
            </section>
               
            </form>
        </div>
    

    </body>
</html>