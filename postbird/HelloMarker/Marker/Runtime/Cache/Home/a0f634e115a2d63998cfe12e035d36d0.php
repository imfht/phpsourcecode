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

    <script src="/hellomarkertest/Marker/Home/View/Public/js/jquery-calculator.js"></script>
    <script src="/hellomarkertest/Marker/Home/View/Public/js/jquery-charts.js"></script>
    <script src="/hellomarkertest/Marker/Home/View/Public/js/bootstrap-datepicker.js"></script>
    
    



<div class="modal fade" id="addAccountModal" tabindex="-1" role="dialog" aria-labelledby="addAccountModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">添加新账目</h4>
      </div>
      <form action="/hellomarkertest/marker.php/Home/Account/addAccount/" method="post" accept-charset="utf-8">
        <div class="modal-body">
            <div class="form-group">
              <label for="accountname">※ 名称： </label>
              <input id="accountnameInput" type="text" name="accountname" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="accounttime">※ 选择时间 </label>
              <input id="accounttimeInput" type="text" name="accounttime" class="form-control" placeholder="点击选择..." required>
              <script>
                $('#accounttimeInput').datepicker({
                    format: "yyyy-mm-dd",
                    language: "cn",
                    orientation: "bottom auto",
                    autoclose: true,
                    todayHighlight: true
                });  
              </script>
            </div>
            <div class="form-group">
              <label for="accountmoney">※ 金额： </label>
              <input id="accountmoneyInput"type="number" name="accountmoney" class="form-control" placeholder="点击选择计算器..."required>
              <script>
                  $('#accountmoneyInput').calculator();
              </script> 
            </div>
            <div class="form-group">
              <label for="accounttype">※ 类别：</label>
              <select id="accounttypeInput"type="text" name="accounttype" class="form-control" placeholder="点击选择计算器..."required>
              <option value="1">一日三餐</option>
              </select>
              <script>
                  showTypeOption("#accounttypeInput");
              </script> 
            </div>
            <div class="form-group">
              <label for="accountother">备注： </label>
              <textarea id="accountotherInput"  name="accountother" class="form-control" placeholder="可以为空..." rows="3" wrap="hard"></textarea> 
            </div>
        </div>
        <div class="modal-footer">
          <button id="addAccountBtn" type="submit" class="btn btn-info ">添加</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        </div>
      </form>
    </div>
  </div>
</div><!--budget modal end-->

<div class="container marker-work marker-report">
    <div class="row" >
    <div class="col-md-9">
      <div class="panel panel-primary ">
        <div class=" panel-heading">
          <font class="panel-title">报表查询</font>
        </div>
        <div class="well col-md-12 well-heading">
          <form class="form-inline">
              <button type="button" id="pickTodayMonth" class="btn btn-default btn-sm col-md-2 col-xs-12" onclick="allAccountReport('todayMonth','<?php echo ($todayMonth); ?>');">本月</button>
              <button class="btn btn-primary btn-sm col-md-2 col-xs-12" type="button" data-toggle="collapse" data-target="#pickMonthDiv" aria-expanded="false" aria-controls="pickMonthDiv">
              查询月报表  <i class="fa fa-caret-down"></i>
              </button>
                  <div class="collapse jumbotron col-md-12 col-xs-12 text-center" id="pickMonthDiv">
                    <div class="well text-center" >
                      <font>选择月份:</font>
                       <input type="text"  id="pickMonthInput"  class="form-control" placeholder="点击选择月份...">
                       <button type="button" class="btn btn-primary  btn-sm" id="pickMonthBtn" onclick="allAccountReportInput('todayMonth');">查询</button>
                         <script>
                            $('#pickMonthInput').datepicker({
                                format: "yyyy-mm",
                                startView: 1,
                                minViewMode: 1,
                                language: "cn",
                                autoclose: true,
                                todayHighlight: true,
                                orientation: "bottom auto"
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
                              startView: 2,
                              minViewMode: 2,
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
        </div><!--well-->
          <div id="dataFlagDiv" class=" text-center">
            <table class="table  table-striped table-responsive text-center table-high">
              <tbody>
                <tr>
                  <td class="textspace"><font id="dateHeading"></font>  共<font id="countHeading" color="red"></font>笔记账  总消费 <i class="fa fa-rmb"></i><font id="moneyHeading"color="red"></font></td>
                </tr>
              </tbody>
            </table>
            <div class="jumbotron" style="display:none">
                <i class="fa fa-warning fa-2x"></i>
                <font><strong>无任何记录！</strong></font>
            </div>
            <div class="well text-center col-md-12" id="fade1">
              <div id="pieShowLg" class="visible-lg col-md-9" >
                  <canvas id="chart-area1" width="300px" height="300px"/>
              </div>
              <div class="list-group col-md-3 visible-lg" id="pieShowItem">
                          </div>
              <div id="pieShowXs" class="hidden-lg" >
                  <canvas id="chart-area2" />
              </div>
            </div>          
        </div>
      </div><!--panel end-->
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
      </div><!--panel-end-->
      <div class="dataFlagDiv visible-lg" id="fade3">
        <div class="panel panel-default" >
            <div class="panel-heading ">
              <font class="panel-title" id="downItemTitle"></font>
            </div>
         <div class="col-md-12 width100 visible-lg">
           <div class="width20"><font>名称</font></div>
           <div class="width20"><font>类别</font></div>
           <div class="width20"><font>时间</font></div>
           <div class="width20"><font>金额</font></div>
           <div class="width20"><font>&nbsp;&nbsp;&nbsp;&nbsp;</font></div>
         </div>
         <div id="listItem" class="visible-lg">
           
         </div>
        </div><!--panel-end-->
      </div>


    </div><!--col-9 end-->
     <div class="col-md-3" >
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
          <section class="side-div">
              <div class="panel">
                  <a href="javascript:" class="btn btn-warning btn-lg" id="addAcountBtn" data-toggle="modal" data-target="#addAccountModal">记一笔</a>
              </div>
              <div class="panel">
                  <a href="/hellomarkertest/marker.php/Home/Account/index/" class="btn btn-info btn-lg btn-block">账本首页</a>
              </div>
             <div class="panel panel-default ">
                <div class="panel-heading">
                   <font >在线彩票</font>
                   <font class="pull-right"><i class="fa fa-money"></i></font>
                </div>
                <div class="list-group">
                  <a href="http://www.zhcw.com/" target="_blank" class="list-group-item"> 福彩网 <font class="pull-right"><i class="fa fa-chevron-right"></i></font></a>
                  <a href="http://www.lottery.gov.cn/" target="_blank" class="list-group-item"> 体彩网 <font class="pull-right"><i class="fa fa-chevron-right"></i></font></a>
                  <a href="http://www.cpdyj.com/?regfrom=bjtime" target="_blank" class="list-group-item"> 在线投注 <font class="pull-right"><i class="fa fa-chevron-right"></i></font></a>
                </div>
              </div>
              <div class="panel panel-default ">
                <div class="panel-heading">
                   <font >存款利率</font>
                    <font class="pull-right"><i class="fa fa-rmb"></i></font>
                </div>
                <div class="list-group">
                  <a href="http://www.icbc.com.cn/ICBC/%e9%87%91%e8%9e%8d%e4%bf%a1%e6%81%af/%e5%ad%98%e8%b4%b7%e6%ac%be%e5%88%a9%e7%8e%87%e8%a1%a8/%e4%ba%ba%e6%b0%91%e5%b8%81%e5%ad%98%e6%ac%be%e5%88%a9%e7%8e%87%e8%a1%a8/" target="_blank" class="list-group-item"> 工商银行 <font class="pull-right"><i class="fa fa-angle-double-right"></i></font></a>
                  <a href="http://www.ccb.com/cn/personal/interest/rmbdeposit.html" target="_blank" class="list-group-item"> 建设银行 <font class="pull-right"><i class="fa fa-angle-double-right"></i></font></a>
                  <a href="http://www.abchina.com/cn/PublicPlate/Quotation/bwbll/200909/t20090924_1639.htm/" target="_blank" class="list-group-item"> 农业银行 <font class="pull-right"><i class="fa fa-angle-double-right"></i></font></a>
                  <a href="http://www.psbc.com/portal/zh_CN/PersonalBanking/InformationSearch/443.html" target="_blank" class="list-group-item"> 邮政储蓄 <font class="pull-right"><i class="fa fa-angle-double-right"></i></font></a>
                  <a href="http://www.boc.cn/finadata/lilv/fd31/" target="_blank" class="list-group-item"> 中国银行 <font class="pull-right"><i class="fa fa-angle-double-right"></i></font></a>
                  <a href="http://www.cmbchina.com/CmbWebPubInfo/InterestRate.aspx?chnl=ckrate" target="_blank" class="list-group-item"> 招商银行 <font class="pull-right"><i class="fa fa-angle-double-right"></i></font></a>
                  <a href="http://www.cebbank.com/Channel/2028361" target="_blank" class="list-group-item"> 光大银行 <font class="pull-right"><i class="fa fa-angle-double-right"></i></font></a>
                </div>
              </div>
              <div class="panel panel-default ">
                <div class="panel-heading">
                   <font >股票证券</font> 
                    <font class="pull-right"><i class="fa fa-line-chart"></i></font>
                </div>
                <div class="list-group ">
                  <a href="http://www.stcn.com/" target="_blank" class="list-group-item"> 中国证券 <font class="pull-right"><i class="fa fa-link"></i></font></a>
                  <a href="http://www.eastmoney.com/" target="_blank" class="list-group-item"> 东方财富 <font class="pull-right"><i class="fa fa-link"></i></font></a>
                  <a href="http://www.hexun.com/" target="_blank" class="list-group-item"> 和讯财经 <font class="pull-right"><i class="fa fa-link"></i></font></a>
                  <a href="http://www.stcn.com/" target="_blank" class="list-group-item"> 证券时报 <font class="pull-right"><i class="fa fa-link"></i></font></a>
                  <a href="http://finance.ifeng.com/stock/" target="_blank" class="list-group-item"> 凤凰股票 <font class="pull-right"><i class="fa fa-link"></i></font></a>
                </div>
              </div>
              <div class="panel panel-default ">
                <div class="panel-heading">
                   <font >财经指数</font>
                    <font class="pull-right"><i class="fa fa-pie-chart"></i></font>
                </div>
                <div class="list-group">
                  <a href="http://gold.hexun.com/hjxh/" target="_blank" class="list-group-item"> 黄金价格 <font class="pull-right"><i class="fa fa-arrow-circle-o-right"></i></font></a>
                  <a href="http://app.finance.ifeng.com/hq/rmb/list.php" target="_blank" class="list-group-item"> 外汇牌价 <font class="pull-right"><i class="fa fa-arrow-circle-o-right"></i></font></a>
                  <a href="http://finance.sina.com.cn/mac/" target="_blank" class="list-group-item"> 宏观数据 <font class="pull-right"><i class="fa fa-arrow-circle-o-right"></i></font></a>
                  <a href="http://finance.sina.com.cn/money/forex/hq/DINIW.shtml" target="_blank" class="list-group-item"> 美元指数 <font class="pull-right"><i class="fa fa-arrow-circle-o-right"></i></font></a>
                </div>
              </div>
          </section>
        </div><!--col-md-3 end -->
      
    </div><!--row end-->
    </div><!--cintainer end-->
<script>
  $(document).ready(function(){
    var date=new Date();
    var month=date.getMonth()+1;
    var year=date.getFullYear();
    var todayMonth=year+'-'+month;
    allAccountReport('todayMonth',todayMonth);
  });
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