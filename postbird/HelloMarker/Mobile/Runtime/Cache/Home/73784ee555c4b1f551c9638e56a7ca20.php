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
    <div data-role="header" data-position="fixed" data-tap-toggle="false"data-theme="h">
      <a href="#sharePanel" style="display:inline-block;margin-top:10px;" ><i class="fa fa-list"></i></a>
      <h1>设置预算</h1>
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
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">设置当前月预算</h4>
          </div>
          <form action="/hellomarkertest/mobile.php/Home/Account/budgetChange/" method="post" data-ajax="false"accept-charset="utf-8">
            <div class="modal-body">
                <label for="budget">预算金额： </label>
                <input id="budgetInput"type="number" name="budgetValue" class="form-control">&nbsp;
                <small id="messageSmall"><i class="fa fa-info-circle "></i>：只能填写数字，且必须大于0！</small>
            </div>
            <div class="modal-footer">
              <button id="budgetBtn"type="submit" class="btn btn-info ">提交</button>
              <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
          </form>
        </div>
      </div>
    </div>
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