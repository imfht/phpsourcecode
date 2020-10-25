<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>HelloMarker | 一本正经地吃喝玩乐</title>
    <link href="//cdn.bootcss.com/bootstrap/3.0.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/hellomarkertest/Public/css/font-awesome.min.css">
    <!-- <link href="//cdn.bootcss.com/font-awesome/4.5.0/css/font-awesome.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="/hellomarkertest/Mobile/Home/View/Public/css/marker.css">
    <link rel="stylesheet" href="/hellomarkertest/Public/css/jquery.mobile-1.4.5.min.css">
    <!-- <link rel="stylesheet" href="//cdn.bootcss.com/jquery-mobile/1.4.5/jquery.mobile.min.css"> -->
    <script src="/hellomarkertest/Public/js/jquery-1.10.2.js"></script>
    <script src="/hellomarkertest/Public/js/jquery.mobile-1.4.5.min.js"></script>
    <script src="/hellomarkertest/Public/js/bootstrap.min.js"></script>
    <script src="/hellomarkertest/Mobile/Home/View/Public/js/marker.js"></script>
    <script>
      var appUrl="/hellomarkertest/mobile.php";
    </script>
</head>
<div data-role="page" >
    <div data-role="header" data-position="fixed"data-tap-toggle="false" data-theme="f">
      <h1>用户登录</h1>
    </div>
    <div data-role="main" class="content">
        
        <form action="/hellomarkertest/mobile.php/Home/User/userLogin/" method="post" data-ajax="false"accept-charset="utf-8" class="form">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="form-group">
                        <label for="username" title="必填项"><i class="fa fa-dot-circle-o"></i> 用户名</label>
                        <input type="text" name="username" id="username" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="userpassword" title="必填项"><i class="fa fa-dot-circle-o"></i> 密码</label>
                        <input type="password" name="userpassword" id="userpassword" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="loginverify" title="必填项"><i class="fa fa-dot-circle-o"></i> 验证码</label><br>
                        <input id="loginverify" type="text" name="loginverify" class="form-control input-verify">
                        <img src="/hellomarkertest/mobile.php/Home/User/verify" alt="验证码显示" onclick="this.src='/hellomarkertest/mobile.php/Home/User/verify/rand='+Math.random()">
                    </div>
                    <div class="form-group">
                        <label for="userremember">
                           <input id="userremember" type="checkbox" name="userremember" value="1" >   记住我</label>
                    </div>    
                </div>
                <div class="panel-footer">
                    <div class="form-group">
                        <button type="submit" id="userloginbutton" class="btn btn-primary">登录</button>
                        
                    </div>
                </div>
            </div>
        </form>
        <div class="col-md-3 login-error">
            <?php if($loginErrorFlag == 1): ?><div class="alert alert-danger .alert-dismissible">
                 <a href="" class="pull-right"><i data-dismiss="alert" aria-label="Close" aria-hidden="true" class="fa fa-close "></i></a>
                 <i class="fa fa-exclamation-circle"></i> &nbsp;&nbsp; <strong><?php echo ($loginErrorInfo); ?></strong>
                 </div><?php endif; ?>
        </div>
     </div>
<div data-role="footer"data-position="fixed" data-tap-toggle="false">
	<div data-role="navbar" data-iconpos="left" >
      <ul >
	      <li  class="footer-icon ui-block-f"><a href="/hellomarkertest/mobile.php/Home/" data-ajax="false"><i class="fa fa-home "></i><br><font><small>首页</small></font></a></li>
	      <li class="footer-icon"><a href="/hellomarkertest/mobile.php/Home/Article/" data-ajax="false"><i class="fa fa-newspaper-o"></i><br><font><small>咨询</small></font></a></li>
	      <li class="footer-icon"><a href="/hellomarkertest/mobile.php/Home/Index/markerShare/sid/all/" data-ajax="false"><i class="fa fa-eye "></i><br><font><small>发现</small></font></a></li>
	      <li class="footer-icon"><a href="/hellomarkertest/mobile.php/Home/Work/" data-ajax="false"><i class="fa fa-cube"></i><br><font><small>应用</small></font></a></li>
	      <li class="footer-icon"><a href="/hellomarkertest/mobile.php/Home/User/userHome/" data-ajax="false"><i class="fa fa-user-secret"></i><br><font><small>我的</small></font></a></li>
      </ul>
    </div>
</div>
</div><!--page end-->

</body>
</html>