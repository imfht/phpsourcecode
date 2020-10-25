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

<div class="container marker-share">
    <div class="row" >
        <div class="col-md-9">
                <div class="panel panel-default panel-share">
                    <div class="panel-heading">
                        <div class="nav nav-pills nav-top">
                          <li id="all" role="presentation"><a href="/hellomarkertest/marker.php/Home/Index/markerShare/sid/all">全部</a></li>
                          <li id="address" role="presentation" ><a href="/hellomarkertest/marker.php/Home/Index/markerShare/shareType/address/selectName/上海/sid/address">城市</a></li>
                            <li id="search" role="presentation" ><a href="/hellomarkertest/marker.php/Home/Index/markerShare/sid/search">搜索</a></li>
                        </div>
                        <ul id="all-div" class="nav nav-pills nav-bottom hidden">
                          <li class="pull-left">共分享 •  <font color="red"><?php echo ($count); ?></font></li>
                          <li class="pull-right visible-lg">在这里,发现属于你的吃、喝、玩、乐！</li>
                        </ul>
                        <ul id="address-div" class="nav nav-pills nav-bottom hidden">
                          <li id="c1" role="presentation" class="second-nav-active"><a href="/hellomarkertest/marker.php/Home/Index/markerShare/shareType/selectName/address/上海/cid/c1/sid/address">上海</a></li>
                          <li id="c2" role="presentation" ><a href="/hellomarkertest/marker.php/Home/Index/markerShare/shareType/address/selectName/杭州/cid/c2/sid/address">杭州</a></li>
                          <li id="c3" role="presentation"><a href="/hellomarkertest/marker.php/Home/Index/markerShare/shareType/address/selectName/苏州/cid/c3/sid/address">苏州</a></li>
                          <li class="pull-right visible-lg"><?php echo ($count); ?> •   条</li>
                        </ul>
                        <ul id="search-div" class="nav nav-pills nav-bottom hidden ">
                          <li class="pull-left">
                          <form action="/hellomarkertest/marker.php/Home/Index/markerShare/sid/search/" method="get" class="form-inline" autocomplete="on" role="search">
                              <div class="form-group">
                                  <input id="searchInput"name="selectName" type="text" class="form-control navbar-form navbar-form-left " placeholder="搜索...">
                                  <button type="submit" class="btn btn-default navbar-form search-button"><i class="fa fa-search"></i></button>
                                  <div class="radio-group">
                                     <label><input type="radio" name="shareType" value="address" class="form-control form-radio" checked>地址</label>
                                    <label><input type="radio" name="shareType" value="name" class="form-control form-radio">名称</label>                                   
                                  </div>

                              </div>
                          </form>
                          </li>
                          <li class="pull-right visible-lg searchCount"><?php echo ($count); ?> •   条</li>
                        </ul>
                     </div>
                      <?php if($count == 0): ?><div class="noSelectDiv">
                              <i class="fa fa-circle-o-notch fa-5x"></i><br>
                              <font class="noSelectFont"><i class="fa fa-warning fa-1x"></i>  暂 无 内 容 !</font>
                          </div>
                      <?php else: ?>
                        <div class="list-group share-list">
                            <?php if(is_array($noteShare)): $i = 0; $__LIST__ = $noteShare;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li id="#<?php echo ($vo["noteid"]); ?>" class="list-group-item <?php echo ($vo["noteid"]); ?>" onmouseover="shareListHover('.<?php echo ($vo["noteid"]); ?>');" onmouseout="shareListHoverOut('.<?php echo ($vo["noteid"]); ?>');">
                                    <div class="media">
                                        <div class="media-left media-middle">
                                            <a href="/hellomarkertest/marker.php/Home/Index/userHome/user/<?php echo ($vo["usernickname"]); ?>/">
                                                <img src="/hellomarkertest/Public/uploads/user/<?php echo ($vo["userlogo"]); ?>" class="media-object img-circle" alt="">
                                            </a>
                                        </div>
                                        <div class="media-body">
                                            <a href="/hellomarkertest/marker.php/Home/Index/noteShareInfo/id/<?php echo ($vo["noteid"]); ?>" class="media-heading"><font><?php echo ($vo["notename"]); ?></font></a>
                                            
                                            <div class="media-meta">
                                            <i class="fa fa-user"></i> •  <a href="/hellomarkertest/marker.php/Home/Index/userHome/user/<?php echo ($vo["usernickname"]); ?>/"><?php echo ($vo["usernickname"]); ?></a> 分享于<?php echo ($vo["sharetime"]); ?>&nbsp;&nbsp;<i class="fa fa-comment-o"></i>
                                             
                                              <?php if($vo["discusstime"] == 0): ?>暂无评论
                                              <?php else: ?> 最后由 <a href="/hellomarkertest/marker.php/Home/Index/userHome/user/<?php echo ($vo["discussname"]); ?>/"><?php echo ($vo["discussname"]); ?></a>  评论于<?php echo ($vo["discusstime"]); endif; ?> 
                                            </div>

                                        </div>

                                        <div class="media-right media-middle">
                                            <a href="/hellomarkertest/marker.php/Home/Index/noteShareInfo/id/<?php echo ($vo["noteid"]); ?>"><span class="badge" ><?php echo ($vo["notediscusscount"]); ?></span></a>
                                        </div>
                                        <div class="media-meta text-center">
                                                <img src="/hellomarkertest/Public/uploads/markerimage/<?php echo ($vo["imagesrc"]); ?>" alt="<?php echo ($vo["notename"]); ?>">
                                        </div>
                                    </div>
                                   
                                </li><?php endforeach; endif; else: echo "" ;endif; ?>
                    </div><?php endif; ?>
                    <div class="panel-footer">
                           <ul class="pagination">
                              <li> <?php echo ($show); ?></li>
                           </ul>
                    </div>
                </div>
            </div>
        <div class="col-md-3" >
            <section class="side-div">
                <div class="panel">
                    <a href="/hellomarkertest/marker.php/Home/Index/newNote/" class="btn btn-primary btn-lg btn-block">添加我所感兴趣的</a>
                </div>
                <div class="panel panel-default gallery-home-hot-discuss">
                    <div class="panel-heading">
                        热门
                    </div>
                    <div class="list-group">
                        <?php if(is_array($hotCollectRows)): $i = 0; $__LIST__ = $hotCollectRows;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="/hellomarkertest/marker.php/Home/Index/noteShareInfo/id/<?php echo ($vo["noteid"]); ?>" class="list-group-item"><?php echo ($vo["notename"]); ?></a><?php endforeach; endif; else: echo "" ;endif; ?>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        关注微信
                    </div>
                    <div class="panel-body">
                        <img src="/hellomarkertest/Public/uploads/wechat.jpg" alt="hellomarker" width="100%">
                    </div>
                </div>
                 <div class="panel panel-primary">
                    <div class="panel-heading">
                        安卓客户端
                    </div>
                    <div class="panel-body">
                        <img src="/hellomarkertest/Public/uploads/apk.png" alt="hellomarker" width="100%">
                    </div>
                </div>
                <div class="panel panel-info">
                    <div class="panel-heading">
                        IOS客户端
                    </div>
                    <div class="panel-body">
                        <img src="/hellomarkertest/Public/uploads/ios.png" alt="hellomarker" width="100%">
                    </div>
                </div>
                 <div class="panel panel-default">
                    <div class="panel-heading">
                        友情支持
                    </div>
                    <div class="panel-body">
                        <a href="http://www.tsingwa.com" target="_blank"><img src="/hellomarkertest/Public/uploads/tsingwa.jpg" alt="hellomarker" width="100%"></a>
                    </div><hr>
                    <div class="panel-body">
                        <a href="http://www.ptbird.cn" target="_blank"><img src="/hellomarkertest/Public/uploads/postbird.png" alt="hellomarker" width="100%"></a>
                    </div>
                </div>
            </section>
        </div>
    </div><!--row end-->
</div><!--cintainer end-->
<script>
        changeNavBottomColor();
</script>

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