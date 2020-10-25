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
<link rel="stylesheet" href="/hellomarkertest/Public/css/app-owl.carousel.css">
<script src="/hellomarkertest/Public/js/app-owl.carousel.js"></script>

<div data-role="page" >
    <div data-role="header" data-position="fixed"  data-tap-toggle="false"data-theme="f">
      <h1>HelloMarker</h1>
    </div>
        <script>
        $(document).ready(function() {
          $("#owl-demo1").owlCarousel({
            items : 1,
            lazyLoad : true,
            autoPlay : true,
            navigation : false,
            navigationText :  false,
            pagination : false,
          });
        });
        </script>
    <div id="owl-demo1" class="owl-carousel">
        <div class="item-bottom">
            <div class="item-right">
                <img src="/hellomarkertest/Public/images/article/banner/<?php echo ($articleRows[0]['articlebanner']); ?>" alt="" width="100%;"height="110px;">
                <p class="caption"><?php echo ($articleRows[0]['articlename']); ?></p>
            </div>
        </div>
        <div class="item-bottom">
            <div class="item-right">
                <img src="/hellomarkertest/Public/images/article/banner/<?php echo ($articleRows[1]['articlebanner']); ?>" alt="" width="100%;"height="110px;">
                <p class="caption"><?php echo ($articleRows[1]['articlename']); ?></p>
            </div>
        </div>
        <div class="item-bottom">
            <div class="item-right">
                <img src="/hellomarkertest/Public/images/article/banner/<?php echo ($articleRows[2]['articlebanner']); ?>" alt="" width="100%;"height="110px;">
                <p class="caption"><?php echo ($articleRows[2]['articlename']); ?></p>
            </div>
        </div>
    </div>

    <div data-role="navbar" >
        <ul >
            <li><a ><font class='pull-left'><i class="fa fa-coffee"></i>  吃喝玩乐</font></a></li>
        </ul>
    </div>
    <div data-role="main" class="content">
    <div class="list-group">
        <?php if(is_array($newShareRows)): $i = 0; $__LIST__ = $newShareRows;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a class="list-group-item" href="/hellomarkertest/mobile.php/Home/Index/noteShareInfo/id/<?php echo ($vo["noteid"]); ?>" data-ajax="false">
                    <img src="/hellomarkertest/Public/uploads/markerimage/<?php echo ($vo["imagesrc"]); ?>" alt="<?php echo ($vo["notename"]); ?>" >
                    <span class="ui-li-count" ><?php echo ($vo["notediscusscount"]); ?></span>
                    <font><?php echo ($vo["notename"]); ?></font>
                </a><?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
  </div><!--main-content-->
  <div data-role="navbar" >
    <ul >
        <li><a><font class='pull-left'><i class="fa fa-map-signs"></i>  城市</font></a></li>
    </ul>
  </div>
  <div data-role="main" class="content">
    <div class="ui-grid-b">
         <div class="ui-block-a">
             <a href="/hellomarkertest/mobile.php/Home/Index/markerShare/shareType/address/selectName/上海/cid/c1/sid/address" data-ajax="false">
                 <img src="/hellomarkertest/Public/images/city/shanghai.png" alt="shanghai"  class="nav-image animate-img">
                 <div class="mask">
                    <font><span>上海</span></font>
                 </div>
             </a>
         </div>
         <div class="ui-block-b">
             <a href="/hellomarkertest/mobile.php/Home/Index/markerShare/shareType/address/selectName/杭州/cid/c2/sid/address"data-ajax="false">
                 <img src="/hellomarkertest/Public/images/city/hangzhou.jpg" alt="hangzhou" class="nav-image animate-img">
                 <div class="mask">
                    <font><span>杭州</span></font>
                 </div>
             </a>
         </div>
         <div class="ui-block-c">
             <a href="/hellomarkertest/mobile.php/Home/Index/markerShare/shareType/address/selectName/苏州/cid/c3/sid/address" data-ajax="false">
                 <img src="/hellomarkertest/Public/images/city/suzhou.png" alt="suzhou" class="nav-image animate-img">
                 <div class="mask">
                    <font><span>苏州</span></font>
                 </div>
             </a>
         </div>
    </div><!--nav-end-->
    <div data-role="navbar" >
        <ul >
            <li><a ><font class='pull-left'><i class="fa fa-commenting-o"></i>  热门</font></a></li>
        </ul>
    </div>
    <div class="list-group">
        <?php if(is_array($hotDiscussRows)): $i = 0; $__LIST__ = $hotDiscussRows;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a class="list-group-item" href="/hellomarkertest/mobile.php/Home/Index/noteShareInfo/id/<?php echo ($vo["noteid"]); ?>" data-ajax="false">
                <span class="badge pull-right" ><?php echo ($vo["notediscusscount"]); ?></span>
                <font><?php echo ($vo["notename"]); ?></font>
            </a><?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
    <div data-role="navbar" >
        <ul >
            <li><a ><font class='pull-left'><i class="fa fa-newspaper-o"></i>  我说</font></a></li>
        </ul>
    </div>
    <?php if(is_array($articleRows)): $i = 0; $__LIST__ = $articleRows;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="/hellomarkertest/mobile.php/Home/Article/article/id/<?php echo ($vo["articleid"]); ?>"data-ajax="false">
        <div class="list-group-item ">
            <div class="news-item " style="background-image:url('/hellomarkertest/Public/images/article/<?php echo ($vo["articleimg"]); ?>');">
                <div class="mask-div"></div>
                <font><?php echo ($vo["articlename"]); ?><br><?php echo ($vo["articletime"]); ?></font>
            </div>
        </div>
    </a><?php endforeach; endif; else: echo "" ;endif; ?>
  </div><!--main-content-->
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