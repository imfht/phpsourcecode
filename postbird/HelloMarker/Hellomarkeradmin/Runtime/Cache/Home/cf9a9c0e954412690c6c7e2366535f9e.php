<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>HelloMarker | 一本正经地吃喝玩乐</title>
    <link rel="stylesheet" href="/hellomarkertest/Public/css/bootstrap.min.css">
    <link rel="stylesheet" href="/hellomarkertest/Public/css/font-awesome.min.css">
    <link rel="stylesheet" href="/hellomarkertest/Hellomarkeradmin/Home/View/Public/css/index.css">
    <link rel="stylesheet" href="/hellomarkertest/Marker/Home/View/Public/css/marker.css">
    <link rel="stylesheet" href="/hellomarkertest/Hellomarkeradmin/Home/View/Public/css/jquery-clock.css">
    <link rel="stylesheet" href="/hellomarkertest/Hellomarkeradmin/Home/View/Public/css/bootstrap-datepicker.css">
    <script src="/hellomarkertest/Public/js/jquery-1.10.2.js"></script>
    <script src="/hellomarkertest/Public/js/bootstrap.min.js"></script>
    <script>
      var appUrl="/hellomarkertest/hellomarkeradmin.php";
    </script>
    <script src="/hellomarkertest/Hellomarkeradmin/Home/View/Public/js/index.js"></script>
    <script src="/hellomarkertest/Public/js/kindeditor/kindeditor-min.js"></script>
</head>
<body style="background-color:#F4F8FA">
<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="container">
    <div class="row">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="/hellomarkertest/hellomarkeradmin.php/Home/Index/indexShow/">HelloMarker</a>
    </div>
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li><a href="/hellomarkertest/hellomarkeradmin.php/Home/Index/indexShow/">后台首页 </a></li>
        <li><a href="/hellomarkertest/hellomarkeradmin.php/Home/Index/shareShow/">分享内容</a></li>
        <li><a href="/hellomarkertest/hellomarkeradmin.php/Home/Index/discussShow/">评论列表</a></li>
        <li><a href="/hellomarkertest/hellomarkeradmin.php/Home/Index/userShow/">用户管理</a></li>
        <li><a href="/hellomarkertest/hellomarkeradmin.php/Home/Index/articleShow/">文章列表</a></li>
        <li><a href="/hellomarkertest/hellomarkeradmin.php/Home/Index/articleAdd/">文章发布</a></li>
        <li><a href="/hellomarkertest/hellomarkeradmin.php/Home/Index/bugShow/">用户反馈</a></li>
        <li class="btn btn-primary btn-xs" ><a href="/hellomarkertest/marker.php/" target="_blank">前往前台主页</a></li>
        <li><a href="/hellomarkertest/hellomarkeradmin.php/Home/Admin/logout/" x><i class="fa fa-user-secret"></i> <?php echo ($adminsessionname); ?> | <font color="#000">退出</font></a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
        </div><!--row end-->
    </div><!--container end-->
  </div><!-- /.container-fluid -->
</nav>
<script>
    function myNoteDeleteConfirm(){
      var msg = "请确认操作呦，不然会引起误会哦？"; 
      if (confirm(msg)==true){ 
        return true; 
      }else{ 
        return false; 
      } 
    }

</script>

<div class="container">
	<div class="row">
		<div class="jumbotron">
			<h1><small>欢迎管理员，</small><small><?php echo ($adminsessionname); ?></small></h1>
		</div>
	</div>
</div>
    <div class="container marketing">
      <div class="row">
        <div class="col-lg-4">
          <img class="img-circle" src="/hellomarkertest/Hellomarkeradmin/Home/View/Public/images/b1.jpg"  width="140" height="140">
          <h2><small>祝愿</small></h2>
          <h2><small>辛苦的你每天阳光明媚！</small></h2>
        </div><!-- /.col-lg-4 -->
        <div class="col-lg-4">
          <img class="img-circle" src="/hellomarkertest/Hellomarkeradmin/Home/View/Public/images/b2.jpg" alt="Generic placeholder image" width="140" height="140">
          <h2><small>祈祷</small></h2>
          <h2><small>你的生活会有我的精彩！</small></h2>
        </div><!-- /.col-lg-4 -->
        <div class="col-lg-4">
          <img class="img-circle" src="/hellomarkertest/Hellomarkeradmin/Home/View/Public/images/b3.jpg" alt="Generic placeholder image" width="140" height="140">
         <h2><small>叮嘱</small></h2>
          <h2><small>忙碌中的小憩必不可少！</small></h2>
        </div><!-- /.col-lg-4 -->
      </div><!-- /.row -->

      <!-- START THE FEATURETTES -->
      <hr class="featurette-divider">

      <div class="row featurette">
        <div class="col-md-7">
          <h2 class="featurette-heading">活力四射， <span class="text-muted">因你的活泼</span></h2>
          <h2><small>不良的记录毅然决然地扔它到火星</small></h2>
          <h2><small>鄙俗的评论大义凛然地驱逐出地球</small></h2>
        </div>
        <div class="col-md-5">
          <img class="featurette-image img-responsive center-block" src="/hellomarkertest/Hellomarkeradmin/Home/View/Public/images/c1.jpg" >
        </div>
      </div>

      <hr class="featurette-divider">

      <div class="row featurette">
        <div class="col-md-7 col-md-push-5">
          <h2 class="featurette-heading">井然有序， <span class="text-muted">有你的辛劳</span></h2>
          <h2><small>营养的反馈细细品读其个中滋味</small></h2>
          <h2><small>一大波的高营养文章正在翘首以待地等你翻牌子哟</small></h2>
        </div>
        <div class="col-md-5 col-md-pull-7">
          <img class="featurette-image img-responsive center-block" src="/hellomarkertest/Hellomarkeradmin/Home/View/Public/images/c2.jpg">
        </div>
      </div>

      <hr class="featurette-divider">
      <!-- /END THE FEATURETTES -->
  </div>