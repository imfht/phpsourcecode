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
      <a href="#sharePanel" class="btn btn-primary btn-sm no-style-btn" ><i class="fa fa-list"></i></a>
      <h1>用户详情</h1>
    </div>
    <div data-role="panel" id="sharePanel"> 
      <h4><small><strong>信息列表</strong></small></h4>
        <a class="list-group-item text-center" href="/hellomarkertest/mobile.php/Home/Index/userHome/user/<?php echo ($userHomeRow[0]['username']); ?>/" data-ajax="false">基本信息<i class="pull-right fa fa-user"></i></a>
        <?php if($myFlag == 1): ?><a class="list-group-item text-center" href="/hellomarkertest/mobile.php/Home/Index/userHomeNote/user/<?php echo ($userHomeRow[0]['username']); ?>/" data-ajax="false">我的记录<i class="pull-right fa fa-list"></i></a><?php endif; ?>
        <a class="list-group-item text-center" href="/hellomarkertest/mobile.php/Home/Index/userHomeShare/user/<?php echo ($userHomeRow[0]['username']); ?>/" data-ajax="false">所有分享<i class="pull-right fa fa-share-alt"></i></a>
        <a class="list-group-item text-center" href="/hellomarkertest/mobile.php/Home/Index/userHomeCollect/user/<?php echo ($userHomeRow[0]['username']); ?>/" data-ajax="false">所有收藏<i class="pull-right fa fa-heart"></i></a>
    </div>
<div data-role="main" class="ui-content">
    <?php if($userHomeFlag == 1): ?><div class="panel panel-default user-home">
                <div class="panel-heading">
                    <?php echo ($userHomeRow[0]['username']); ?> 的个人主页
                    <?php if($myFlag == 1): ?><a href="/hellomarkertest/mobile.php/Home/User/myChange/" data-ajax="false" class="btn btn-warning btn-xs pull-right"><font class="color-fff">编辑个人资料</font></a><?php endif; ?>
                </div>
                    <div class="list-group">
                        <li class="list-group-item">
                            <label>头像 </label>
                            <img src="/hellomarkertest/Public/uploads/user/<?php echo ($userHomeRow[0]['userlogo']); ?>" alt="<?php echo ($userHomeRow[0]['username']); ?>" height="50px"/>
                        </li>
                        <li class="list-group-item"><label><small class="color-888"><strong>用户名</strong></small></label><?php echo ($userHomeRow[0]['username']); ?></li>
                        <li class="list-group-item"><label><small class="color-888"><strong>性别</strong></small></label><?php echo ($userHomeRow[0]['usersex']); ?></li>
                        <li class="list-group-item"><label><small class="color-888"><strong>注册时间</strong></small></label><?php echo ($userHomeRow[0]['userregtime']); ?></li>
                        <li class="list-group-item"><label><small class="color-888"><strong>QQ</strong></small></label><?php echo ($userHomeRow[0]['userqq']); ?></li>
                        <li class="list-group-item"><label><small class="color-888"><strong>微信</strong></small></label><?php echo ($userHomeRow[0]['userwechat']); ?></li>
                        <li class="list-group-item"><label><small class="color-888"><strong>所在地</strong></small></label><?php echo ($userHomeRow[0]['useraddress']); ?></li>
                        <li class="list-group-item" title="<?php echo ($userHomeRow[0]['userinterest']); ?>"><label><small class="color-888"><strong>兴趣</strong></small></label><?php echo ($userHomeRow[0]['userinterest']); ?></li>
                    </div>
                </div>
            
    <?php else: ?>
      <div class="col-md-12">
        <div class="jumbotron">
            <h2 class="text-center"><i class="fa fa-warning"></i></h2>
          <h2 class="text-center">用户不存在</h2>
        </div>
      </div><?php endif; ?>
</div><!--content end-->


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