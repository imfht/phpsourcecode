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
      <a href="#sharePanel" style="display:inline-block;margin-top:10px;" ><i class="fa fa-list"></i></a>
      <h1>修改资料</h1>
    </div>
    <div data-role="panel" id="sharePanel"> 
        <h4><small><strong>修改选项</strong></small></h4>
        <a class="list-group-item text-center" href="/hellomarkertest/mobile.php/Home/User/myChange/" data-ajax="false">修改资料<i class="pull-right fa fa-keyboard-o"></i></a>
        <a class="list-group-item text-center" href="/hellomarkertest/mobile.php/Home/User/myChangePassword/" data-ajax="false">修改密码<i class="pull-right fa fa-magnet"></i></a>
        <a class="list-group-item text-center" href="/hellomarkertest/mobile.php/Home/User/myChangeLogo/" data-ajax="false">上传头像<i class="pull-right fa fa-meh-o"></i></a>
        <?php if($myChangeErrorFlag == 1): ?><div class="alert alert-danger"><strong><i class="fa fa-info-circle"></i><?php echo ($myChangeError); ?></strong></div><?php endif; ?>
    </div>
    <div data-role="main" class="ui-content">
         <?php if($userHomeFlag == 1): ?><form action="/hellomarkertest/mobile.php/Home/User/myChangeLogoWork/" method="post" data-ajax="false" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="panel panel-default user-home user-login">
                    <div class="panel-heading">
                        用户头像
                    </div>
                     <div class="panel-body">
                         <div class="">
                             <input type="file" name="userlogo">
                             <input type="hidden" name="public" value="/hellomarkertest/Public">
                             <div class="user-logo">
                                 <img src="/hellomarkertest/Public/uploads/user/<?php echo ($userHomeRow[0]['userlogo']); ?>" alt="<?php echo ($userHomeRow[0]['username']); ?>" width="50px">
                             </div>
                             <div>
                             <h4><small><i class="fa fa-info"></i>  建议图片为方形！支持尺寸： 50 * 50 | 60 * 60 | 70 * 70 | 80 * 80 | 90 * 90 | 100 * 100 | </small></h4>
                             <h4><small><i class="fa fa-info"></i>  支持图片类型:png | jpg | jpeg | gif | bmp |</small></h4>
                             <h4><small><i class="fa fa-info"></i>  图片大小不超过2M</small></h4>
                             </div>
                         </div>
                     </div>
                    <div class="panel-footer">
                        <div class="form-group">
                        <button type="submit" id="userloginbutton" class="btn btn-primary"><i class="fa fa-upload"></i> 上传头像</button>
                        </div>
                    </div>
                </div>
             </form>

            <?php else: ?>
                <div class="col-md-12">
                    <div class="jumbotron">
                        <h2 class="text-center"><i class="fa fa-warning"></i></h2>
                      <h2 class="text-center">用户不存在</h2>
                    </div>
                </div><?php endif; ?>
    </div><!--main- content end-->

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