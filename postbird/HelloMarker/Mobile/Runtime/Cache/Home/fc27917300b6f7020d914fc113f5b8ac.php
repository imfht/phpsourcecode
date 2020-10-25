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
<script src="/hellomarkertest/Mobile/Home/View/Public/js/responsiveslides.min.js"></script>
<div data-role="page" >
    <div data-role="header" data-position="fixed"data-tap-toggle="false" data-theme="f">
      <h1>详细信息</h1>
    </div>
    <div class="text-center">
            <li role="presentation" class="list-group-item"><h4><?php echo ($noteInfo[0]['notename']); ?></h4></li>
            <img src="/hellomarkertest/Public/uploads/markerimage/<?php echo ($noteInfo[0]['imagesrc']); ?>" alt="<?php echo ($noteInfo[0]['notename']); ?>">
    </div>   
    <div data-role="main" class="content">
    <div class="list-group">
        <li class="list-group-item no-border text-right" >
            <?php if($noteInfo[0]['isshare'] == 1): ?><h4><small>已分享</small></h4>
            <?php elseif($noteInfo[0]['userid'] == $usersessionid): ?>
               <h4><small><i class="fa fa-share-alt"></i> 分享 </small></h4><?php endif; ?>
        </li>
          <li class="list-group-item" title="分享用户"><i class="fa fa-user" ></i> ： <a href="/hellomarkertest/mobile.php/Home/Index/userHome/user/<?php echo ($noteInfo[0]['usernickname']); ?>/"><?php echo ($noteInfo[0]['usernickname']); ?></a></li>
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
                 </span>
           </li>
    </div>
    <li class="list-group-item">
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
    </li>
  </div><!--main-content-->
  <div data-role="main" class="content">
      <?php if($userLoginFlag == 1): ?><form action="/hellomarkertest/mobile.php/Home/Index/noteAddDiscuss/" data-ajax="false" method="post" accept-charset="utf-8">
                <textarea name="content" class="form-control text-content" rows='10'></textarea>
                <input type="hidden" name="noteid" value="<?php echo ($noteInfo[0]['noteid']); ?>">
                <div class="form-group text-right">
                  <input type="submit" data-role="form-inline" value="添加评论" >
                </div>
            </form>
      <?php else: ?>
        <div class="panel-body">
          需要先 <a href="/hellomarkertest/mobile.php/Home/User/index" data-ajax="false">登录</a> 才能回复。
        </div><?php endif; ?>
     <?php if($noteInfoFlag == 1): ?><div class="panel panel-info">
            <div class="panel-heading">
              <h4 >相关讨论 <small>  &nbsp; • &nbsp;  <?php echo ($noteDiscussCount); ?> 条</small></h4>
            </div>
            <div class="panel-body">
              
             <?php if(is_array($noteDiscussRows)): $i = 0; $__LIST__ = $noteDiscussRows;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if($vo["isdelete"] == 1): ?><div class="media">
                      <div class="media-body">
                        <font color="#BBB"><s>该评论已删除</s></font>
                        <hr>
                      </div>
                    </div>
                  <?php else: ?>
                  <div id="<?php echo ($vo["discussid"]); ?>-media-div">
                    <a href="/hellomarkertest/mobile.php/Home/Index/userHome/user/<?php echo ($vo["usernickname"]); ?>" data-ajax="false">
                      <img src="/hellomarkertest/Public/uploads/user/<?php echo ($vo["userlogo"]); ?>" alt="<?php echo ($vo["usernickname"]); ?>" >
                    </a>
                    <h5><small >用户：<?php echo ($vo["usernickname"]); ?></small></h5>
                    <h5><small >时间：<?php echo ($vo["discusstime"]); ?></small></h5>
                     <span class="pull-right">
                        <?php if($vo["usernickname"] == $usersessionname): ?><i onclick="noteDiscussDelete(<?php echo ($usersessionid); ?>,<?php echo ($vo["discussid"]); ?>)" class="fa fa-trash-o fa-a"></i> &nbsp; • &nbsp;
                        <?php else: endif; ?>
                        <?php if($vo["discusslike"] == 1): ?><i id="<?php echo ($vo["discussid"]); ?>" class="fa fa-thumbs-up fa-a"onclick="noteDiscussLike(<?php echo ($usersessionid); ?>,<?php echo ($vo["discussid"]); ?>)"></i>
                        <?php else: ?>
                          <i id="<?php echo ($vo["discussid"]); ?>" class="fa fa-thumbs-o-up fa-a" onclick="noteDiscussLike(<?php echo ($usersessionid); ?>,<?php echo ($vo["discussid"]); ?>)"></i><?php endif; ?> 
                        <span id="<?php echo ($vo["discussid"]); ?>span"><?php echo ($vo["discusslikecount"]); ?></span>
                    </span>         
                    <div class="media-meta">
                        <p ><?php echo ($vo["discusstext"]); ?></p>
                        <hr>
                    </div>
                        <div class="media-right media-middle">
                            <a href="#"><span class="badge" ><?php echo ($vo["notediscusscount"]); ?></span></a>
                        </div>
                  </div><?php endif; endforeach; endif; else: echo "" ;endif; ?>
            </div><!--panel-body end-->
          </div><?php endif; ?>
  </div><!--main-content end-->
<script>

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