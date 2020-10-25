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

<div class="container share-note-info">
    <div class="row">
        <!-- <div class="col-md-12"> -->
            <?php if($noteInfoFlag == 1): ?><div class="col-md-5">
                    <img src="/hellomarkertest/Public/uploads/markerimage/<?php echo ($noteInfo[0]["imagesrc"]); ?>" alt="<?php echo ($noteInfo[0]['notename']); ?>" class="img-rounded" width="100%">
                </div>
                <div class="col-md-1 visitable-lg"></div>
                <div class="col-md-6 note-info">
                    <div class="list-group">
                       <li class="list-group-item note-name">
                           <?php if($userNoteCollectFlag == 1): ?><i  class="fa fa-heart fa-a <?php echo ($noteInfo[0]['noteid']); ?>-id" title="已收藏" onclick="noteCollectWork(<?php echo ($usersessionid); ?>,<?php echo ($noteInfo[0]['noteid']); ?>,'<?php echo ($noteInfo[0]['usernickname']); ?>')"></i>
                           <?php else: ?>
                               <i  class="fa fa-heart-o fa-a <?php echo ($noteInfo[0]['noteid']); ?>-id" title="点击收藏" onclick="noteCollectWork(<?php echo ($usersessionid); ?>,<?php echo ($noteInfo[0]['noteid']); ?>,'<?php echo ($noteInfo[0]['usernickname']); ?>')"></i><?php endif; ?> ： <font><?php echo ($noteInfo[0]['notename']); ?></font>
                           <?php if($noteInfo[0]['userid'] == $usersessionid): ?><a href="/hellomarkertest/marker.php/Home/Index/myNoteDelete/id/<?php echo ($noteInfo[0]["noteid"]); ?>" onclick="javascript:return myNoteDeleteConfirm();"><i class="fa fa-trash fa-a trash-a"></i><small> 删除</small></a><?php endif; ?>
                           <?php if($noteInfo[0]['isshare'] == 1): ?><button class="btn btn-default btn-xs pull-right disabled">已分享</button>
                               <?php elseif($noteInfo[0]['userid'] == $usersessionid): ?>
                               <button class="btn btn-success btn-xs pull-right"><i class="fa fa-share-alt"></i> 分享 </button><?php endif; ?>
                       </li>
                       <li class="list-group-item" title="分享用户"><i class="fa fa-user" ></i> ： <a href="/hellomarkertest/marker.php/Home/Index/userHome/user/<?php echo ($noteInfo[0]['usernickname']); ?>/"><?php echo ($noteInfo[0]['usernickname']); ?></a></li>
                       <li class="list-group-item" title="分享时间"><i class="fa fa-clock-o" ></i> ： <?php echo ($noteInfo[0]['notetime']); ?></li>
                       <li class="list-group-item" title="地址"><i class="fa fa-automobile" ></i> ： <?php echo ($noteInfo[0]['noteaddress']); ?></li>
                       <li class="list-group-item">
                           <span>
                               <i class="fa fa-comments" title="讨论次数"></i> ： <?php echo ($noteInfo[0]['notediscusscount']); ?>
                           </span>
                           <span><font id="collectWarningNoPersonal" color="red" class="hidden">不能收藏自己的记录！</font></span>
                           <span class="pull-right">
                                <?php if($userNoteCollectFlag == 1): ?><small id="<?php echo ($noteInfo[0]['noteid']); ?>small">取消收藏 → </small>
                                   <i class="fa fa-heart fa-a <?php echo ($noteInfo[0]['noteid']); ?>-id" title="取消收藏" onclick="noteCollectWork(<?php echo ($usersessionid); ?>,<?php echo ($noteInfo[0]['noteid']); ?>,'<?php echo ($noteInfo[0]['usernickname']); ?>')"></i>
                               <?php else: ?>
                                   <small id="<?php echo ($noteInfo[0]['noteid']); ?>small">点击收藏 → </small>
                                   <i class="fa fa-heart-o fa-a <?php echo ($noteInfo[0]['noteid']); ?>-id" title="点击收藏" onclick="noteCollectWork(<?php echo ($usersessionid); ?>,<?php echo ($noteInfo[0]['noteid']); ?>,'<?php echo ($noteInfo[0]['usernickname']); ?>')"></i><?php endif; ?>  <small>&nbsp; • &nbsp;  </small><font id="<?php echo ($noteInfo[0]['noteid']); ?>font"><?php echo ($noteInfo[0]['notecollectcount']); ?></font>
                               </li>
                           </span>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-danger alert-lg text-center" role="alert">
                    <h2><i class="fa fa-ban"></i> 内容不存在</h2>
                    <small>由于未知原因导致信息查看出现错误。</small>
                </div><?php endif; ?>
        <!-- </div> -->
        <?php if($noteInfoFlag == 1): ?><div class="col-md-9 col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>相关讨论 <small>  &nbsp; • &nbsp;  <?php echo ($noteDiscussCount); ?> 条  &nbsp; - &nbsp;  第 <?php echo ($noteDiscussPage); ?> 页</small></h4>
                    </div>
                    <?php if($noteDiscussFlag == 0): ?><div class="panel-body text-center">
                            <h3 ><i class="fa fa-info-circle"></i> 暂无评论</h3>
                        </div>
                    <?php else: ?>
                        <div class="list-group">
                             <?php if(is_array($noteDiscussRows)): $i = 0; $__LIST__ = $noteDiscussRows;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="list-group-item note-discuss">
                                    <?php if($vo["isdelete"] == 1): ?><div class="media">
                                            <div class="media-body">
                                            <h4 ><font color="#BBB"><s>该评论已删除</s></font></h4>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="media" id="<?php echo ($vo["discussid"]); ?>-media-div">
                                            <div class="media-left ">
                                                <a href="/hellomarkertest/marker.php/Home/Index/userHome/user/<?php echo ($vo["usernickname"]); ?>/">
                                                    <img src="/hellomarkertest/Public/uploads/user/<?php echo ($vo["userlogo"]); ?>" class="media-object img-circle" alt="">
                                                </a>
                                            </div>
                                            <div class="media-body">
                                                <a href="/hellomarkertest/marker.php/Home/Index/userHome/user/<?php echo ($vo["usernickname"]); ?>/" class="media-heading"><font><?php echo ($vo["usernickname"]); ?></font></a>
                                                <font class="note-discuss-time">&nbsp; • &nbsp;<?php echo ($vo["discusstime"]); ?></font>
                                                <span class="pull-right">
                                                    <?php if($vo["usernickname"] == $usersessionname): ?><i onclick="noteDiscussDelete(<?php echo ($usersessionid); ?>,<?php echo ($vo["discussid"]); ?>)" class="fa fa-trash-o fa-a"></i> &nbsp; • &nbsp;<?php endif; ?>
                                                        <?php if($vo["discusslike"] == 1): ?><i id="<?php echo ($vo["discussid"]); ?>" class="fa fa-thumbs-up fa-a"onclick="noteDiscussLike(<?php echo ($usersessionid); ?>,<?php echo ($vo["discussid"]); ?>)"></i>
                                                        <?php else: ?><i id="<?php echo ($vo["discussid"]); ?>" class="fa fa-thumbs-o-up fa-a" onclick="noteDiscussLike(<?php echo ($usersessionid); ?>,<?php echo ($vo["discussid"]); ?>)"></i><?php endif; ?> <span id="<?php echo ($vo["discussid"]); ?>span"><?php echo ($vo["discusslikecount"]); ?></span>
                                                </span>
                                                <div class="media-meta">
                                                    <p><?php echo ($vo["discusstext"]); ?></p>
                                                </div>
                                            </div>
                                            <div class="media-right media-middle">
                                                <a href="#"><span class="badge" ><?php echo ($vo["notediscusscount"]); ?></span></a>
                                            </div>
                                        </div><?php endif; ?>
                                </li><?php endforeach; endif; else: echo "" ;endif; ?>
                        </div>
                        <div class="panel-footer">
                            <ul class="pagination ">
                              <li> <?php echo ($show); ?></li>
                           </ul>
                        </div><?php endif; ?>
                </div><!--discuss list panel end-->
            <?php if($userLoginFlag == 1): ?><div id="addDiscuss" name="addDiscuss">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4>新评论</h4>
                        </div>
                        <form action="/hellomarkertest/marker.php/Home/Index/noteAddDiscuss/" method="post" accept-charset="utf-8">
                            <textarea name="content" class="form-control content" rows='10'></textarea>
                            <input type="hidden" name="noteid" value="<?php echo ($noteInfo[0]['noteid']); ?>">
                            <div class="panel-footer">
                                <button type="submit" class="btn btn-info">新增评论</button>
                            </div>
                        </form>
                    </div>
                </div> <!--add end-->
            <?php else: ?>
             <div class="col-md-9 col-xs-12">
               <div class="panel panel-default">
                  <div class="panel-body">
                    需要先 <a href="/hellomarkertest/marker.php/Home/User/index">登录</a> 才能回复。
                  </div>
                </div>
              </div><?php endif; ?>
      </div><!--div discuss end-->
      <div class="col-md-3 col-xs-12 ">
        <div class="addDiscussBtn">
                <a href="#addDiscuss" class="btn btn-primary btn-lg">添加评论</a>
        </div> 
      </div><!--right div end-->
      <div class="col-md-3 col-xs-12 note-other-info">
          <h5>其他相关</h5>
          <div class="list-group-item note-other-text">
            <?php if($noteInfo[0]['noteohterstrlen'] == 0): ?><div class="text-center">
                  <i class="fa fa-warning text-center"></i>
                  <span class="text-center">暂无其他信息</span>
              </div>
            <?php else: ?>
            <div >
              <p><?php echo ($noteInfo[0]['noteother']); ?></p>
            </div><?php endif; ?>
          </div>
      </div><!--right div end-->
         <?php else: endif; ?>
    </div><!--row end-->
</div><!--container end-->

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