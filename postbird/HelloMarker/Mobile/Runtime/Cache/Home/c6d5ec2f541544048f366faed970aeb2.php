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
    <script src="/hellomarkertest/Mobile/Home/View/Public/js/jquery-moment.min.js"></script>
    <script src="/hellomarkertest/Mobile/Home/View/Public/js/jquery-clock.js"></script>
    <script src="/hellomarkertest/Mobile/Home/View/Public/js/jquery-calendar.js"></script>
    <script src="/hellomarkertest/Mobile/Home/View/Public/js/bootstrap-datepicker.js"></script>

<div data-role="page" >
    <div data-role="header" data-position="fixed" data-tap-toggle="false"data-theme="g">
      <a href="#sharePanel"style="display:inline-block;margin-top:10px;"><i class="fa fa-list"></i></a>
      <h1>添加记事</h1>
    </div>
        <div data-role="panel" id="sharePanel"> 
            <h4><small><strong>应用列表</strong></small></h4>
            <li class="list-group-item" ><i class="fa fa-file-text-o"></i> 一本正经</li>
                <a class="list-group-item  text-center" href="/hellomarkertest/mobile.php/Home/Work/newWork/" data-ajax="false">
                    <font>添加新记事</font>
                    <i class="pull-right fa fa-angle-right"></i>
                </a>
                <a class="list-group-item  text-center" href="/hellomarkertest/mobile.php/Home/Work/" data-ajax="false">
                    <font>查看记事本</font>
                    <i class="pull-right fa fa-angle-right"></i>
                </a>
                <a class="list-group-item  text-center" href="/hellomarkertest/mobile.php/Home/Work/myWork/" data-ajax="false">
                    <font>所有记事列表</font>
                    <i class="pull-right fa fa-angle-right"></i>
                </a>
            <li class="list-group-item" ><i class="fa fa-cny"></i> 柴米油盐</li>
                <a class="list-group-item  text-center" href="/hellomarkertest/mobile.php/Home/Account/newAccount/" data-ajax="false">
                    <font>添加记账</font>
                    <i class="pull-right fa fa-angle-right"></i>
                </a>
                <a class="list-group-item  text-center" href="/hellomarkertest/mobile.php/Home/Account/setBudget/" data-ajax="false">
                    <font>设置预算</font>
                    <i class="pull-right fa fa-angle-right"></i>
                </a>
                <a class="list-group-item  text-center" href="/hellomarkertest/mobile.php/Home/Account/" data-ajax="false">
                    <font>查看账本</font>
                    <i class="pull-right fa fa-angle-right"></i>
                </a>
                <a class="list-group-item  text-center" href="/hellomarkertest/mobile.php/Home/Account/allReport" data-ajax="false">
                    <font>详细报表</font>
                    <i class="pull-right fa fa-angle-right"></i>
                </a>
            <a class="list-group-item text-left" href="/hellomarkertest/mobile.php/Home/Weather/" data-ajax="false">未雨绸缪<i class="pull-right fa fa-meh-o"></i></a>
            <?php if($myChangeErrorFlag == 1): ?><div class="alert alert-danger"><strong><i class="fa fa-info-circle"></i><?php echo ($myChangeError); ?></strong></div><?php endif; ?>
    </div>
    <div data-role="main" class="ui-content">
        <form action="/hellomarkertest/mobile.php/Home/Work/newWorkAdd/" method="post" data-ajax="false" accept-charset="utf-8" class="form">
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
                <label for="worklevel" title="必填项"><i class="fa fa-calendar-plus-o"></i> 级别</label>
                <fieldset data-role="controlgroup" data-type="horizontal">
                      <label for="11">低</label>
                      <input type="radio" name="worklevel" id="11" value="1" checked>
                      <label for="12">中</label>
                      <input type="radio" name="worklevel" id="12" value="2"> 
                      <label for="13">高</label>
                      <input type="radio" name="worklevel" id="13" value="3"> 
                </fieldset>
            <div class="panel-footer">
                <button type="submit" id="userloginbutton" class="btn btn-success">添加记事</button>
            </div>
        </div>
      </form>
        <?php if($newNoteErrorFlag == 1): ?><div class="alert alert-danger .alert-dismissible">
             <a href="" class="pull-right"><i data-dismiss="alert" aria-label="Close" aria-hidden="true" class="fa fa-close "></i></a>
             <i class="fa fa-exclamation-circle"></i> &nbsp;&nbsp; <strong><?php echo ($newNoteErrorInfo); ?></strong>
             </div>
            <?php elseif($newNoteErrorFlag == 2): ?>
            <button type="">
               <a href="/hellomarkertest/mobile.php/Home/Work/" data-ajax="false" class="btn btn-success btn-xs width100"><font class=" color-fff"><i class="fa fa-check-square-o"></i> &nbsp;&nbsp; <?php echo ($newNoteErrorInfo); ?></font></a>
            </button><?php endif; ?>
    </div><!--content end -->
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