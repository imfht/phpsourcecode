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
    <div data-role="header" data-position="fixed" data-tap-toggle="false"data-theme="f">
      <h1>用户信息</h1>
    </div>
	<div data-role="main" class="ui-content">
		<?php if($userLoginFlag == 1): ?><div class="panel text-center" style="padding:20px 20px;">
			<img src="/hellomarkertest/Public/uploads/user/<?php echo ($userHomeRow[0]['userlogo']); ?>" alt="<?php echo ($userHomeRow[0]['username']); ?>"></img>
			<font><strong>用户：<?php echo ($userHomeRow[0]['username']); ?></strong> </font>
			<button type=""><a href="/hellomarkertest/mobile.php/Home/User/myChangeLogo/" data-ajax="false" class="btn btn-primary btn-xs width100"><font class="color-fff">更换头像</font></a></button>
			<button type=""><a href="/hellomarkertest/mobile.php/Home/User/userLogout/" data-ajax="false" class="btn btn-danger btn-xs width100"><font class="color-fff">退出登录</font></a></button>
		</div>
		  <a href="/hellomarkertest/mobile.php/Home/User/myChange/" data-ajax="false" class="list-group-item">修改资料 <font class="pull-right"><i class="fa fa-angle-right"></i></font></a>
		  <a href="/hellomarkertest/mobile.php/Home/Index/myHome/user/<?php echo ($userHomeRow[0]['username']); ?>/" class="list-group-item">我的主页 <font class="pull-right"><i class="fa fa-angle-right"></i></font></a><br>
		  <a href="/hellomarkertest/mobile.php/Home/Index/myShare/" class="list-group-item">我的分享 <font class="pull-right"><i class="fa fa-angle-right"></i></font></a>
		  <a href="/hellomarkertest/mobile.php/Home/Index/myCollect/" class="list-group-item">我的收藏 <font class="pull-right"><i class="fa fa-angle-right"></i></font></a><br>
		  <a href="/hellomarkertest/mobile.php/Home/Work/" class="list-group-item" data-ajax="false">我的记事 <font class="pull-right"><i class="fa fa-angle-right"></i> </font></a><br>
		  <a href="/hellomarkertest/mobile.php/Home/Account/" class="list-group-item" data-ajax="false">我的记账 <font class="pull-right"><i class="fa fa-angle-right"></i></font></a><br>
		  <a href="/hellomarkertest/mobile.php/Home/Weather/" class="list-group-item" data-ajax="false">我的天气 <font class="pull-right"><i class="fa fa-angle-right"></i></font></a><br>
		<?php else: ?>
		    <div class="panel-body">
		    	<div class="jumbotron">
		    		<h4>用户未登录</h4>
		    		<button type="button"><a class="btn btn-warning btn-sm width100" href="/hellomarkertest/mobile.php/Home/User/index/" data-ajax="false"><font class="color-fff">点击登录</font></a></button>
		    	</div>
		    </div>
		    <div class="panel-body">
		    	<div class="jumbotron">
		    		<h4>加入Marker</h4>
		    		<button type="button"><a class="btn btn-danger btn-sm width100" href="/hellomarkertest/mobile.php/Home/User/userSign/" data-ajax="false"><font class="color-fff">点击注册</font></a></button>
		    	</div>
		    </div><?php endif; ?>
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