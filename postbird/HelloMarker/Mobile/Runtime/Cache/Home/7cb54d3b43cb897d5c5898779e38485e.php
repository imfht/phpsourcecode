<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>HelloMarker | 一本正经地吃喝玩乐</title>
    <link href="//cdn.bootcss.com/bootstrap/3.0.1/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="/hellomarkertest/Public/css/font-awesome.min.css"> -->
    <link href="//cdn.bootcss.com/font-awesome/4.5.0/css/font-awesome.min.css" rel="stylesheet">

    <link rel="stylesheet" href="/hellomarkertest/Mobile/Home/View/Public/css/marker.css">
    <!-- <link rel="stylesheet" href="/hellomarkertest/Public/css/jquery.mobile-1.4.5.min.css"> -->
    <link rel="stylesheet" href="//cdn.bootcss.com/jquery-mobile/1.4.5/jquery.mobile.min.css">
    
    <script src="//cdn.bootcss.com/jquery/1.10.1/jquery.min.js"></script>
    <script src="//cdn.bootcss.com/jquery-mobile/1.4.5/jquery.mobile.min.js"></script>
    <script src="//cdn.bootcss.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
    <script src="/hellomarkertest/Mobile/Home/View/Public/js/marker.js"></script>
    <script>
      var appUrl="/hellomarkertest/mobile.php";
    </script>
</head>
    <script src="/hellomarkertest/Marker/Home/View/Public/js/jquery-calculator.js"></script>
    <script src="/hellomarkertest/Marker/Home/View/Public/js/jquery-charts.js"></script>
    <script src="/hellomarkertest/Marker/Home/View/Public/js/bootstrap-datepicker.js"></script>
    
    


<div data-role="page" >
    <div data-role="header" data-position="fixed" data-tap-toggle="false"data-theme="h">
      <a href="#sharePanel" style="display:inline-block;margin-top:10px;" ><i class="fa fa-list"></i></a>
      <h1>详细报表</h1>
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
<?php if($accountErrorFlag == 1): ?><div class="alert alert-danger alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <strong>Error：</strong><?php echo ($accountErrorInfo); ?>
    </div>
  <?php elseif($accountErrorFlag == 2): ?>
    <div class="alert alert-success alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <strong>Success：</strong><?php echo ($accountErrorInfo); ?>
    </div>
  <?php elseif($accountErrorFlag == 3): ?>
   <div class="alert alert-warning alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <strong>Warning:</strong><?php echo ($accountErrorInfo); ?>
    </div><?php endif; ?>
    <div data-role="collapsible">
      <h1>报表查询方式</h1>
      <p>
          <form class="form-inline">
              <button type="button" id="pickTodayMonth" class="btn btn-default btn-sm col-md-2 col-xs-12" onclick="allAccountReport('todayMonth','<?php echo ($todayMonth); ?>');">本月</button>
              <button class="btn btn-primary btn-sm col-md-2 col-xs-12" type="button" data-toggle="collapse" data-target="#pickMonthDiv" aria-expanded="false" aria-controls="pickMonthDiv">
              查询月报表  <i class="fa fa-caret-down"></i>
              </button>
                  <div class="collapse text-center" id="pickMonthDiv">
                    <div class="well text-center" >
                      <font>选择月份:</font>
                       <input type="text"  id="pickMonthInput"  class="form-control" placeholder="点击选择月份...">
                       <button type="button" class="btn btn-primary  btn-sm" id="pickMonthBtn" onclick="allAccountReportInput('todayMonth');">查询</button>
                         <script>
                            $('#pickMonthInput').datepicker({
                                  format: "yyyy-mm",
                                  startView: 0,
                                  autoclose: true,
                                  orientation: "bottom auto",
                            });
                        </script>
                    </div>  
                  </div>
              <button class="btn btn-danger btn-pink btn-sm  col-md-2 col-xs-12" type="button" data-toggle="collapse" data-target="#pickYearDiv" aria-expanded="false" aria-controls="pickYearDiv">
              查询年报表  <i class="fa fa-caret-down"></i>
              </button>
                <div class="collapse jumbotron col-md-12 col-xs-12 text-center" id="pickYearDiv">
                  <div class="well text-center" >
                        <font>选择年份:</font>
                          <input type="text"  id="pickYearInput"  class="form-control" placeholder="选择年份...">
                          <button type="button" class="btn btn-danger btn-pink btn-sm"  id="pickYearBtn" onclick="allAccountReportInput('todayYear');">查询</button>
                       <script>
                          $('#pickYearInput').datepicker({
                              format: "yyyy",
                              autoclose: true,
                              orientation: "bottom auto",
                          });
                      </script>
                  </div>
                </div>
                <button type="button" class="btn btn-default btn-sm col-md-1 col-xs-12" id="pickTodayYearBtn" onclick="allAccountReport('todayYear','<?php echo ($todayYear); ?>');">本年</button>
                <button type="button" class="btn btn-default btn-sm col-md-1 col-xs-12" id="pickQuarterBtn" onclick="allAccountReport('todayQuarter','<?php echo ($todayQuater); ?>');">本季度</button>
                <button class="btn  btn-success btn-sm col-md-2 col-xs-12" type="button" data-toggle="collapse" data-target="#pickLenghDiv" aria-expanded="false" aria-controls="pickLenghDiv">
                日期区间报表  <i class="fa fa-caret-down"></i>
                </button>
                  <div class="collapse jumbotron col-md-12 col-xs-12 text-center" id="pickLenghDiv">
                    <div class="well text-center" >
                            从  <input type="text"  id="pickLenghInput1"  class="form-control" placeholder="开始日期...">
                            到  <input type="text"  id="pickLenghInput2"  class="form-control" placeholder="结束日期...">
                            <button type="button" class="btn btn-success  btn-sm"  id="pickLenghBtn" onclick="allAccountReport('pickLength','<?php echo ($todayQuater); ?>');">查询</button>
                         <script>
                            $('#pickLenghInput1').datepicker({
                                format: "yyyy-mm-dd",
                                language: "cn",
                                orientation: "bottom auto",
                                autoclose: true,
                                todayHighlight: true
                            });
                            $('#pickLenghInput2').datepicker({
                                format: "yyyy-mm-dd",
                                language: "cn",
                                orientation: "bottom auto",
                                autoclose: true,
                                todayHighlight: true
                            });
                        </script>
                    </div>
                  </div>
          </form>
       </p>
    </div>
    <div id="dataFlagDiv" class=" text-center">
      <div data-role="navbar">
        <ul>
          <li> <a href="#"><font id="dateHeading textspace"></font>  共<font id="countHeading" color="red"></font>笔记账  总消费 <i class="fa fa-rmb"></i><font id="moneyHeading"color="red"></font></a> </li>
        </ul>
      </div>
      <div class="jumbotron" style="display:none">
          <i class="fa fa-warning fa-2x"></i>
          <font><strong>无任何记录！</strong></font>
      </div>
      <div class="well text-center " id="fade1">
        <div id="pieShowLg" class="visible-lg" >
            <canvas id="chart-area1" width="150px" height="150px"/>
        </div>
        <div class="list-group col-md-3 visible-lg" id="pieShowItem">
                    </div>
        <div id="pieShowXs" class="hidden-lg list-group col-md-3 " >
            <canvas id="chart-area2" style="width:100px;height:100px;"/>
        </div>
      </div>          
  </div>
      <div class="panel panel-primary" id="fade2">
        <div class="dataFlagDiv">
            <div class="panel-heading ">
              <font class="panel-title" id="downTitle"></font>
            </div>
            <div class="well">
             <div id="canvas-holder-line" >
                  <canvas id="chart-area-line" width="300px" heigth="50px"></canvas>
             </div>
            </div>        
        </div>
      <div class="dataFlagDiv" id="fade3">
        <div class="panel panel-default" >
            <div class="panel-heading ">
              <font class="panel-title" id="downItemTitle"></font>
            </div>
         <div data-role="navbar">
           <ul>
              <li><a href="#">名称</a></li>
              <li><a href="#">类别</a></li>
              <li><a href="#">金额</a></li>
            </ul>
         </div>
         <div id="listItem" class="">
           
         </div>
        </div><!--panel-end-->
      </div>

        
</div>     
<script>
  $(document).ready(function(){
    var date=new Date();
    var month=date.getMonth()+1;
    var year=date.getFullYear();
    var todayMonth=year+'-'+month;
    allAccountReport('todayMonth',todayMonth);
  });
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