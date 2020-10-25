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
    <div data-role="header" data-position="fixed" data-tap-toggle="false"data-theme="l">
      <h1>吾说八道</h1>
    </div>
    <div data-role="navbar" class="text-left">
        <ul>
            <li><a class="article-nav-header"><h4 class=" pull-left">置顶 · <small>高冷逗比范儿</small></h4></a></li>
        </ul>
    </div>
    <div data-role="main" class="content">
        <a class="list-group-item text-center" href="/hellomarkertest/mobile.php/Home/Article/article/id/<?php echo ($articleRows[0]['articleid']); ?>"data-ajax="false">
            <span><?php echo ($articleRows[0]['articlename']); ?></span>
        </a>
        <img class="list-group-item"src="/hellomarkertest/Public/images/article/<?php echo ($articleRows[0]['articleimg']); ?>" alt="<?php echo ($articleRows[0]['articlename']); ?>" width="100%">
  </div><!--main-content-->
  <div data-role="navbar"class="text-left">
    <ul>
        <li><a class="article-nav-header"><h4 class=" pull-left"> 日常· <small>满足你的小欲望</small></h4></a></li>
    </ul>
  </div>
  <div data-role="main" class="content">
    <?php if(is_array($articleRows)): $k = 0; $__LIST__ = $articleRows;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k; if($k == 1): else: ?>
            <a class="list-group-item text-center" href="/hellomarkertest/mobile.php/Home/Article/article/id/<?php echo ($vo["articleid"]); ?>"data-ajax="false">
                <span><?php echo ($vo["articlename"]); ?></span>
            </a>
            <img class="list-group-item"src="/hellomarkertest/Public/images/article/<?php echo ($vo["articleimg"]); ?>" alt="<?php echo ($vo["articlename"]); ?>>" width="100%"><?php endif; endforeach; endif; else: echo "" ;endif; ?>
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