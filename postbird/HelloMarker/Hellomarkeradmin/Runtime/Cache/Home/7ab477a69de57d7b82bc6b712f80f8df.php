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
	 <?php if($backFlag == 1): ?><div class="alert alert-danger alert-dismissable">
		  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		  <strong>Error：</strong><?php echo ($backInfo); ?>
		</div><?php endif; ?>
	 <?php if($backFlag == 2): ?><div class="alert alert-success alert-dismissable">
		  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		  <strong>Success：</strong> <?php echo ($backInfo); ?>
		</div><?php endif; ?>
	 <?php if($backFlag == 3): ?><div class="alert alert-warning alert-dismissable">
		  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		  <strong>Warning：</strong> <?php echo ($backInfo); ?>
		</div><?php endif; ?>
		<div class="col-md-12">
			<div class="page-header">
				<h1>用户列表 <small> | 共计：<strong><?php echo ($count); ?></strong> </small></h1>
			</div>
			<div class="">
				<?php if(is_array($userRows)): $i = 0; $__LIST__ = $userRows;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="list-group-item">
					
				    <img src="/hellomarkertest/Public/uploads/user/<?php echo ($vo["userlogo"]); ?>" alt="hellomarker">
					<h4><strong><?php echo ($vo["username"]); ?></strong></h4>
					<h4><small><a href="/hellomarkertest/hellomarkeradmin.php/Home/Index/deleteUser/id/<?php echo ($vo["userid"]); ?>" onclick="javascript:return myNoteDeleteConfirm();"class="btn btn-danger btn-xs pull-right">注销该用户</a></small></h4>
					<h4><small>用户性别：<?php echo ($vo["usersex"]); ?></small></h4>
					<h4><small>注册时间：<?php echo ($vo["userregtime"]); ?></small></h4>
					<h4><small>邮箱：<?php echo ($vo["useremail"]); ?></small></h4>
					<h4><small>兴趣爱好：<?php echo ($vo["userinterest"]); ?></small></h4>
					<h4><small>QQ：<?php echo ($vo["userqq"]); ?></small></h4>
					<h4><small>微信：<?php echo ($vo["userwechat"]); ?></small></h4>
					<h4><small>地址：<?php echo ($vo["useraddress"]); ?></small></h4>
				</div><?php endforeach; endif; else: echo "" ;endif; ?>
				<div class=" text-right">
					<ul class="pagination">
                      <li> <?php echo ($show); ?></li>
                    </ul>
				</div>
			</div>
		</div>
	</div>
</div>