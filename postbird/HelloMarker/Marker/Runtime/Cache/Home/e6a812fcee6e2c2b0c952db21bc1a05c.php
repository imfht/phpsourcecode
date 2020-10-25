<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="hellomarker,postbird,李瞻文,ptbird">
    <meta name="description" content="hellomarker,一本正经的吃喝玩乐！Powered by postbird!">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>HelloMarker | 一本正经地吃喝玩乐</title>
    <link rel="stylesheet" href="/hellomarkertest/Public/css/bootstrap.min.css">
    <link rel="stylesheet" href="/hellomarkertest/Public/css/font-awesome.min.css">
    <link rel="stylesheet" href="/hellomarkertest/Marker/Home/View/Public/css/marker.css">
    <link rel="stylesheet" href="/hellomarkertest/Marker/Home/View/Public/css/jquery-clock.css">
    <link rel="stylesheet" href="/hellomarkertest/Marker/Home/View/Public/css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="/hellomarkertest/Marker/Home/View/Public/css/weatherIcon.css">
    <script src="/hellomarkertest/Public/js/jquery-1.10.2.js"></script>
    <script src="/hellomarkertest/Public/js/bootstrap.min.js"></script>
    <script>
      var appUrl="/hellomarkertest/marker.php";
    </script>
    <script src="/hellomarkertest/Marker/Home/View/Public/js/marker.js"></script>
    <script src="/hellomarkertest/Public/js/kindeditor/kindeditor-min.js"></script>
</head>
<body style="background-color:#F4F8FA;letter-spacing:0.2px;">
    <div style="background-color:#fff;">
        <div class="container">
              <div class="log-nav">
              <div style="float:left;">
                   <a href="/hellomarkertest" class="navbar-band logo-a"></a>
                   <span class="slogan visible-lg"> | 一 本 正 经 地 吃 喝 玩 乐 </span>
              </div>
                <ul class="nav navbar-right">
                 <?php if($userLoginFlag == 0): ?><li ><a href="/hellomarkertest/marker.php/Home/User/index">登录</a></li>
                    <li><a href="/hellomarkertest/marker.php/Home/User/userSign">注册</a></li>
                <?php else: ?>
                    <li title='我的Marker'><a href="/hellomarkertest/marker.php/Home/Index/myNote/"><i class='fa fa-bookmark-o' ></i></a></li>
                    <li title='我的应用' class="visible-lg"><a href="/hellomarkertest/marker.php/Home/Work/myWork/"><i class='fa fa-cubes' ></i></a></li>
                    <li class="visible-lg dropdown user-header ">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="javascript:"><i class='fa fa-user-secret'></i> <i class="fa fa-caret-down"></i></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="/hellomarkertest/marker.php/Home/Index/myHome/user/<?php echo ($usersessionname); ?>">我的主页</a></li>
                            <li><a href="/hellomarkertest/marker.php/Home/User/myChange/">编辑个人资料</a></li>
                            <li class="divider"></li>
                            <li><a href="/hellomarkertest/marker.php/Home/User/userLogout">退出</a></li>
                        </ul>
                    </li>
                    <li title='主页' class="hidden-lg"><a href="/hellomarkertest/marker.php/Home/Index/myHome/user/<?php echo ($usersessionname); ?>"><i class='fa fa-user-secret' ></i></a></li>
                    <li title='退出' class="hidden-lg"><a href="/hellomarkertest/marker.php/Home/User/userLogout"><i class='fa fa-power-off' ></i></a></li><?php endif; ?>
               </ul> 
               </div>
        </div>
    </div>
    <nav class="navbar navbar-inverse">
        <div class="container">
            <div class="navbar-header ">
              <button type="button" class="navbar-toggle collapsed " data-toggle="collapse" data-target="#nav-header" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand visible-xs" href="#">专 注 生 活 </a>
            </div>
            <div class="collapse navbar-collapse" id="nav-header">
                <ul class="nav navbar-nav ">
                    <li ><a href="/hellomarkertest/marker.php/Home/Index/">首页</a></li>
                    <li ><a href="/hellomarkertest/marker.php/Home/Article/">吾说八道</a></li>
                    <li><a href="/hellomarkertest/marker.php/Home/Index/markerShare/sid/all">吃喝玩乐</a></li>
                    <li ><a href="/hellomarkertest/marker.php/Home/Work/">一「本」正经</a></li>
                    <li><a href="/hellomarkertest/marker.php/Home/Account/">柴米油盐</a></li>
                    <li ><a href="/hellomarkertest/marker.php/Home/Weather/">未雨绸缪</a></li>
                    <li ><a href="/hellomarkertest/marker.php/Home/App/">也有APP</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-ul">
                <li><a href="/hellomarkertest/marker.php/Home/Bug/"><i class="fa fa-map-o"></i>&nbsp;&nbsp; 「 略懂七八 」</a></li>
                </ul>
            </div>
        </div>
    </nav>

<div class="gallery-home-banner">
    <div class="item">
        <a href="/hellomarkertest/marker.php/Home/Article/article/id/<?php echo ($articleRows[0]['articleid']); ?>">
            <div class="image" style="background-image:url(/hellomarkertest/Public/images/article/banner/<?php echo ($articleRows[0]['articlebanner']); ?>);" >
                <h3 class="text"><?php echo ($articleRows[0]['articlename']); ?></h3>
                <div class="mask"></div>
            </div>
        </a>
    </div>
</div>
<!--HomeBanner-->
<div class="container">
    <div class="row city-row">
        <div class="col-md-8">
            <h3><i class="fa fa-send-o"></i>&nbsp;吃喝玩乐</h3>
            <div class="row gallery-home-marker">
                <?php if(is_array($newShareRows)): $i = 0; $__LIST__ = $newShareRows;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$newShare): $mod = ($i % 2 );++$i;?><div id="<?php echo ($newShare["noteid"]); ?>" class="col-md-6 item" onmouseover="galleryImageFadeOut('#<?php echo ($newShare["noteid"]); ?> .image .text','#<?php echo ($newShare["noteid"]); ?> .image .text2','#<?php echo ($newShare["noteid"]); ?> .image .mask');" onmouseout="galleryImageFadeIn('#<?php echo ($newShare["noteid"]); ?> .image .text','#<?php echo ($newShare["noteid"]); ?> .image .text2','#<?php echo ($newShare["noteid"]); ?> .image .mask')">
                        <a href="/hellomarkertest/marker.php/Home/Index/noteShareInfo/id/<?php echo ($newShare["noteid"]); ?>">
                            <div class="image" style="background-image:url(/hellomarkertest/Public/uploads/markerimage/<?php echo ($newShare["imagesrc"]); ?>);" >
                                <h3 class="text">「 <?php echo ($newShare["notename"]); ?> 」 </h3>
                                <h4 class="text2"><i class="fa fa-user"></i>&nbsp;&nbsp;<?php echo ($newShare["usernickname"]); ?></h4>
                                <div class="mask"></div>
                            </div>
                        </a>
                    </div><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        </div>
        <div class="col-md-4 gallery-home-right">
            <h4><i class="fa fa-comments-o"></i>&nbsp;&nbsp;讨论</h4>
            <div class="gallery-home-hot-discuss">
                <div class="list-group">
                <?php if(is_array($hotDiscussRows)): $i = 0; $__LIST__ = $hotDiscussRows;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="/hellomarkertest/marker.php/Home/Index/noteShareInfo/id/<?php echo ($vo["noteid"]); ?>" class="list-group-item"><?php echo ($vo["notename"]); ?> <span class="badge pull-right" data-toggle="tooltip" data-placement="top" title="近期讨论"><?php echo ($vo["notediscusscount"]); ?></span></a><?php endforeach; endif; else: echo "" ;endif; ?>
                </div>
            </div>
            <div class="index-fix-div .visible-lg"></div>
            <h4><i class="fa fa-smile-o"></i>&nbsp;&nbsp;热门</h4>
            <div class="gallery-home-hot-discuss">
                <div class="list-group">
                <?php if(is_array($hotCollectRows)): $i = 0; $__LIST__ = $hotCollectRows;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="/hellomarkertest/marker.php/Home/Index/noteShareInfo/id/<?php echo ($vo["noteid"]); ?>" class="list-group-item"><?php echo ($vo["notename"]); ?><span class="pull-right" data-toggle="tooltip" data-placement="top" title="收藏人数"><i class="fa fa-heart"></i> <?php echo ($vo["notecollectcount"]); ?></span></a><?php endforeach; endif; else: echo "" ;endif; ?>
                </div>
            </div>
        </div>
    </div><!--end of row-->
    <div class="row city-row">
        <h3 style="display:block;margin-left:10px;"><i class="fa fa-map-signs"></i>&nbsp;城市</h3>
        <div class=" gallery-home-city">
            <div class="col-md-4">
                    <div id="c1" class="item" onmouseover="galleryHomeCityImageHide('.gallery-home-city #c1 .image .text','.gallery-home-city #c1 .image .mask');" onmouseout="galleryHomeCityImageShow('.gallery-home-city #c1 .image .text','.gallery-home-city #c1 .image .mask')">
                        <a href="/hellomarkertest/marker.php/Home/Index/markerShare/shareType/selectName/address/上海/cid/c1/sid/address">
                            <div class="image" style="background-image:url(/hellomarkertest/Public/images/city/shanghai.png);" >
                                <h3 class="text"> 上海  </h3>
                                <div class="mask"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div id="c2" class="item"  onmouseover="galleryHomeCityImageHide('.gallery-home-city #c2 .image .text','.gallery-home-city #c2 .image .mask');" onmouseout="galleryHomeCityImageShow('.gallery-home-city #c2 .image .text','.gallery-home-city #c2 .image .mask')">
                         <a href="/hellomarkertest/marker.php/Home/Index/markerShare/shareType/address/selectName/杭州/cid/c2/sid/address">
                            <div class="image" style="background-image:url(/hellomarkertest/Public/images/city/hangzhou.jpg);" >
                                <h3 class="text"> 杭州  </h3>
                                <div class="mask"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div id="c3"class="item" onmouseover="galleryHomeCityImageHide('.gallery-home-city #c3 .image .text','.gallery-home-city #c3 .image .mask');" onmouseout="galleryHomeCityImageShow('.gallery-home-city #c3 .image .text','.gallery-home-city #c3 .image .mask')">
                        <a href="/hellomarkertest/marker.php/Home/Index/markerShare/shareType/address/selectName/苏州/cid/c3/sid/address">
                            <div class="image" style="background-image:url(/hellomarkertest/Public/images/city/suzhou.png);" >
                                <h3 class="text"> 苏州  </h3>
                                <div class="mask"></div>
                            </div>
                        </a>
                    </div>
                </div>
        </div>
    </div><!--row end-->
    <div class="row city-row">
        <h3 style="display:block;margin-left:10px;"><i class="fa fa-newspaper-o"></i>&nbsp;我说</h3>
        <div class=" gallery-home-marker">
        <?php if(is_array($articleRows)): $i = 0; $__LIST__ = $articleRows;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="col-md-3">
                <div id="indexArticle<?php echo ($vo["articleid"]); ?>" class="item" onmouseover="galleryImageFadeOut('#indexArticle<?php echo ($vo["articleid"]); ?> .image2 .text','#indexArticle<?php echo ($vo["articleid"]); ?> .image2 .text2','#indexArticle<?php echo ($vo["articleid"]); ?> .image2 .mask');" onmouseout="galleryImageFadeIn('#indexArticle<?php echo ($vo["articleid"]); ?> .image2 .text','#indexArticle<?php echo ($vo["articleid"]); ?> .image2 .text2','#indexArticle<?php echo ($vo["articleid"]); ?> .image2 .mask')">
                        <a href="/hellomarkertest/marker.php/Home/Article/article/id/<?php echo ($vo["articleid"]); ?>">
                            <div class="image2" style="background-image:url(/hellomarkertest/Public/images/article/<?php echo ($vo["articleimg"]); ?>);" >
                                <h3 class="text"><?php echo ($vo["articlename"]); ?> </h3>
                                <h4 class="text2"><?php echo ($vo["articletime"]); ?> </h4>
                                <div class="mask"></div>
                            </div>
                        </a>
                </div>
            </div><?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
    </div><!--row end-->
    <div class="row city-row">
        <h3 style="display:block;margin-left:10px;"><i class="fa fa-credit-card"></i>&nbsp;记事</h3>
        <a href="/hellomarkertest/marker.php/Home/Work/">
        <img src="/hellomarkertest/Public/images/other/work1.png" alt="hellomarker" width="100%"  class="hover-to-large">
        </a>
    </div><!--row end-->
    <div class="row city-row">
        <h3 style="display:block;margin-left:10px;"><i class="fa fa-area-chart"></i>&nbsp;账本</h3>
        <a href="/hellomarkertest/marker.php/Home/Work/">
        <img src="/hellomarkertest/Public/images/other/account0.png" alt="hellomarker" width="100%"  class="hover-to-large">
        </a>
    </div><!--row end-->
</div><!-- end of container -->

<div class="container">
	<footer>
       <p class="pull-right "><a href="#" class="scrollToTop">Back to top</a></p>
    </footer>
</div>
<footer class="footer"style="margin-top:20px">

		<div class=" text-center ">
	        <h3><small>powered by <a href="http://www.ptbird.cn" target="_blank">postbird</a></small></h3>
        </div>
</footer>
</body>
</html>