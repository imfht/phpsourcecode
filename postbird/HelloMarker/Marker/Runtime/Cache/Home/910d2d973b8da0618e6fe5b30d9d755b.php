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

    <script src="/hellomarkertest/Marker/Home/View/Public/js/jquery-moment.min.js"></script>
    <script src="/hellomarkertest/Marker/Home/View/Public/js/jquery-clock.js"></script>
    <script src="/hellomarkertest/Marker/Home/View/Public/js/jquery-calendar.js"></script>
    <script src="/hellomarkertest/Marker/Home/View/Public/js/bootstrap-datepicker.js"></script>

<div class="container">
    <div class="row">
        <div class="col-md-9  user-login">
        <form action="/hellomarkertest/marker.php/Home/Work/newWorkAdd/" method="post" accept-charset="utf-8" class="form">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4>新记事</h4>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="worktitle" title="必填项"><i class="fa fa-calendar-plus-o"></i> 标题</label>
                        <input type="text" name="worktitle" id="worktitle" class="form-control" value="<?php echo ($workArray['worktitle']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="worktime" title="必填项"><i class="fa fa-calendar-plus-o"></i> 时间</label>
                          <input type="text" name="worktime" id="pickCalendar" placeholder="日期..." value="<?php echo ($workArray['worktime']); ?>" class="form-control">
                          <script>
                              $('#pickCalendar').datepicker({
                                  format: "yyyy-mm-dd",
                                  language: "cn",
                                  autoclose: true,
                                  todayHighlight: true
                              });
                          </script>
                    </div>
                    <div class="form-group">
                        <label for="workother" title="可以为空"><i class="fa fa-calendar-plus-o"></i> 描述</label>
                        <textarea id="workother" name="workother" placeholder="其他描述...(可以为空)" class="form-control" rows="3" wrap="hard"><?php echo ($workArray['workother']); ?></textarea>
                    </div>
                    <div class="from-group">
                        <label for="worklevel" title="必填项"><i class="fa fa-calendar-plus-o"></i> 级别</label>
                        <div class="radio">
                            <label><input type="radio" name="worklevel" value="1" checked>低</label>
                            &nbsp;<label><input type="radio" name="worklevel" value="2" >中</label>
                             &nbsp;<label><input type="radio" name="worklevel" value="3" >高</label>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="form-group">
                        <button type="submit" id="userloginbutton" class="btn btn-success">添加记事</button>
                    </div>
                </div>
            </div>
        </div>
        </form>
        <div class="col-md-3 login-error">
            <?php if($newNoteErrorFlag == 1): ?><div class="alert alert-danger .alert-dismissible">
                 <a href="" class="pull-right"><i data-dismiss="alert" aria-label="Close" aria-hidden="true" class="fa fa-close "></i></a>
                 <i class="fa fa-exclamation-circle"></i> &nbsp;&nbsp; <strong><?php echo ($newNoteErrorInfo); ?></strong>
                 </div>
                <?php elseif($newNoteErrorFlag == 2): ?>
                     <div class="alert alert-success .alert-dismissible">
                     <a href="/hellomarkertest/marker.php/Home/Work/Index/" class="pull-right"><i data-dismiss="alert" aria-label="Close" aria-hidden="true" class="fa fa-close "></i></a>
                     <a href="/hellomarkertest/marker.php/Home/Work/Index/" ><i class="fa fa-check-square-o"></i> &nbsp;&nbsp; <strong><?php echo ($newNoteErrorInfo); ?></strong></a>
                     </div><?php endif; ?>
        </div>
    </div>
</div>

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