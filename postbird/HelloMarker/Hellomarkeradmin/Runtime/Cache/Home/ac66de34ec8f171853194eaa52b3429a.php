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
				<h1>修改文章Banner <small> </small></h1>
			</div>
			<div class="">
			<div class="col-md-12 ">
			    <li class="list-group-item"><?php echo ($articleRow[0]['articlename']); ?></li>
			    <h4>当前Banner：</h4>
			    <div class="col-md-12">
				   <img src="/hellomarkertest/Public/images/article/banner/<?php echo ($articleRow[0]['articlebanner']); ?>" class="center-block"alt="hellomarker" width="100%">
			    </div>
			</div>
			
               <form action="/hellomarkertest/hellomarkeradmin.php/Home/Index/changeArticleBannerWork" method="post" accept-charset="utf-8" class="form" enctype="multipart/form-data">
                    <div class="form-group">
                    <label>上传Banner图片：</label><br><br>
                    	<input type="file" name="articleimage" required>
                    </div>
               	    <hr>
               	    <div class="form-group">
                           <input type="hidden" name="articleid" value="<?php echo ($articleRow[0]['articleid']); ?>">
                           <input type="submit" name="" value="提交修改" class="btn btn-success btn-primary">
               	    </div>
               </form>
			</div>
		</div>
	</div>
</div>