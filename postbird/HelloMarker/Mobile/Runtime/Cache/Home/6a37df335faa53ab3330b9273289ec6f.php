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
<div data-role="page" id="pageAll">
  <div data-role="panel" id="sharePanel"> 
      <h4><small><strong>找我所需</strong></small></h4>
      <a class="list-group-item text-center" href="/hellomarkertest/mobile.php/Home/Index/markerShare/sid/all" data-ajax="false">全部</a>
      <div class="list-group-item text-center">
          <h4>城市</h4>
          <a class="list-group-item text-left" href="/hellomarkertest/mobile.php/Home/Index/markerShare/shareType/address/selectName/上海/cid/c1/sid/address" data-ajax="false"><img src="/hellomarkertest/Public/images/city/shanghai.png" alt="上海" class="ui-li-icon"><font class="pull-right-font  pull-right">上海</font></a>
          <a class="list-group-item text-left " href="/hellomarkertest/mobile.php/Home/Index/markerShare/shareType/address/selectName/杭州/cid/c2/sid/address"data-ajax="false"><img src="/hellomarkertest/Public/images/city/hangzhou.jpg" alt="杭州" class="ui-li-icon"><font class=" pull-right-font pull-right">杭州</font></a>
          <a class="list-group-item text-left" href="/hellomarkertest/mobile.php/Home/Index/markerShare/shareType/address/selectName/苏州/cid/c3/sid/address" data-ajax="false"><img src="/hellomarkertest/Public/images/city/suzhou.png" alt="苏州" class="ui-li-icon "><font class=" pull-right-font pull-right">苏州</font></a>
      </div>
      <a class="list-group-item text-center" href="/hellomarkertest/mobile.php/Home/Index/markerShare/sid/search" data-ajax="false">搜索</a>
  </div> 
    <div data-role="header" data-position="fixed" data-tap-toggle="false"data-theme="f">
      <a href="#sharePanel" style="display:inline-block;margin-top:10px;"><i class="fa fa-list"></i></a>
      <h1>吃喝玩乐</h1>
    </div>
   <div data-role="main" class="ui-content">
        <div class="nav nav-pills nav-top">
            <ul id="all-div" class="nav nav-pills nav-bottom ">
                <li id="all" role="presentation"><a>全部</a></li>
                <li class="pull-right"><a>共分享 •  <font color="red"><?php echo ($count); ?></font></a></li> 
            </ul>
              <ul id="address-div" class="nav nav-pills nav-bottom ">
                <li id="c1" role="presentation" class="hidden"><a href="#"
                 data-ajax="false">上海</a></li>
                <li id="c2" role="presentation" class="hidden"><a href="#" data-ajax="false">杭州</a></li>
                <li id="c3" role="presentation" class="hidden"><a href="#" data-ajax="false">苏州</a></li>
                 <li class="pull-right"><a>共分享 •  <font color="red"><?php echo ($count); ?></font></a></li> 
              </ul>
              <ul id="search-div" class="nav nav-pills nav-bottom  ">
                <li class="width100">
                  <form action="/hellomarkertest/mobile.php/Home/Index/markerShare/sid/search/" data-ajax="false"method="get" class="" autocomplete="on" role="search">
                      <div class="ui-field-contain">
                          <input type="search" name="selectName" id="search" placeholder="搜索内容..." class="form-control">
                           <fieldset data-role="controlgroup" data-type="horizontal">
                                <label for="address">地址</label>
                                  <input type="radio" name="shareType" id="address" value="address" checked>
                                <label for="name">名称</label>
                                  <input type="radio" name="shareType" id="name" value="name"> 
                            </fieldset>
                          <button type="submit" class="btn btn-xs width100 color-fff"><i class="fa fa-search"></i>  搜索</button>
                    </div> 
                  </form>
                </li>
              </ul>
        </div>
        <?php if($count > 0): if(is_array($noteShare)): $i = 0; $__LIST__ = $noteShare;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li id="#<?php echo ($vo["noteid"]); ?>" class="list-group-item <?php echo ($vo["noteid"]); ?> " style="width:100%;"onmouseover="shareListHover('.<?php echo ($vo["noteid"]); ?>');" onmouseout="shareListHoverOut('.<?php echo ($vo["noteid"]); ?>');">
                  <div class="media">
                      <div class="media-left">
                          <a href="/hellomarkertest/mobile.php/Home/Index/userHome/user/<?php echo ($vo["usernickname"]); ?>/" data-ajax="false" style="color:#333;" data-ajax="false">
                              <img src="/hellomarkertest/Public/uploads/user/<?php echo ($vo["userlogo"]); ?>" class="img-circle" alt="<?php echo ($vo["usernickname"]); ?>">
                              <font><strong><?php echo ($vo["usernickname"]); ?></strong></font>
                          </a>
                      </div>
                      <hr>
                      <div class="media-body">
                          <a href="/hellomarkertest/mobile.php/Home/Index/noteShareInfo/id/<?php echo ($vo["noteid"]); ?>" data-ajax="false"  class="media-heading  text-center"><font><?php echo ($vo["notename"]); ?></font></a>
                          <h5><small>时间：<?php echo ($vo["sharetime"]); ?></small></h5>
                          <h5><small>讨论：<?php echo ($vo["notediscusscount"]); ?></small></h5>
                          <hr>
                          <img src="/hellomarkertest/Public/uploads/markerimage/<?php echo ($vo["imagesrc"]); ?>" style="width:100%;" alt="<?php echo ($vo["notename"]); ?>">
                      </div>
                  </div>
              </li><?php endforeach; endif; else: echo "" ;endif; ?>
        <?php else: ?>
        <div class="text-center">
            <i class="fa fa-warning fa-2x"></i><font>无内容</font>
        </div><?php endif; ?>
    </div>
<script>
changeNavBottomColor();
</script>
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