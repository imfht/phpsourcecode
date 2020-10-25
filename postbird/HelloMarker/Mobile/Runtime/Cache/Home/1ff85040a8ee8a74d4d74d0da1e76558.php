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
    <script src="/hellomarkertest/Marker/Home/View/Public/js/jquery-calculator.js"></script>
    <script src="/hellomarkertest/Marker/Home/View/Public/js/jquery-charts.js"></script>
    <script src="/hellomarkertest/Marker/Home/View/Public/js/bootstrap-datepicker.js"></script>
    
    


<div data-role="page" >
    <div data-role="header" data-position="fixed" data-tap-toggle="false"data-theme="h">
      <a href="#sharePanel" style="display:inline-block;margin-top:10px;" ><i class="fa fa-list"></i></a>
      <h1>柴米油盐</h1>
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
            <a type="button" class="close" data-ajax="false"data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a>
            <strong>Error：</strong><?php echo ($accountErrorInfo); ?>
          </div>
        <?php elseif($accountErrorFlag == 2): ?>
          <div class="alert alert-success alert-dismissible" role="alert">
            <a type="button" class="close" data-ajax="false"data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a>
            <strong>Success：</strong><?php echo ($accountErrorInfo); ?>
          </div>
        <?php elseif($accountErrorFlag == 3): ?>
         <div class="alert alert-warning alert-dismissible" role="alert">
            <a type="button" class="close" data-ajax="false"data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a>
            <strong>Warning:</strong><?php echo ($accountErrorInfo); ?>
          </div><?php endif; ?>
        <div data-role="navbar">
        <ul>
          <li>
              <a href="#"><span class=""><font>预算  <i class="fa fa-rmb"></i>  <strong ><?php echo ($userBudget); ?></strong> 元 , </font>
              <font>支出  <i class="fa fa-rmb"></i>  <strong ><?php echo ($accountOut['monthOut']); ?></strong> 元</font></span></a>
          </li>
        </ul>
        </div>
        <table class="table  table-striped table-responsive text-center table-high">
          <tbody>
            <tr>
              <td><i class="fa fa-calendar"></i></td>
              <td>今日</td>
              <td>本周</td>
              <td>本月</td>
              <td>本年</td>
            </tr>
             <tr>
              <td>支出</td>
              <td>  <i class="fa fa-rmb"></i>  <strong ><?php echo ($accountOut['todayOut']); ?></strong>  </td>
              <td>  <i class="fa fa-rmb"></i>  <strong ><?php echo ($accountOut['weekOut']); ?></strong>  </td>
              <td>  <i class="fa fa-rmb"></i>  <strong ><?php echo ($accountOut['monthOut']); ?></strong>  </td>
              <td>  <i class="fa fa-rmb"></i>  <strong ><?php echo ($accountOut['yearOut']); ?></strong>  </td>
            </tr>
          </tbody>
        </table>
        <div data-role="navbar"class=" text-center">
          <ul>
            <li><a href="#">详细</a></li>
          </ul>
        </div>
        <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
            <ul id="myTabs" class="nav nav-tabs" role="tablist">
              <li role="presentation" class="active">
                  <a href="#todayAccountRowsDiv" data-ajax="false" id="todayAccountRows-tab" role="tab" data-toggle="tab" aria-controls="todayAccountRowsDiv" aria-expanded="true">今日</a>
              </li>
              <li role="presentation">
                  <a href="#weekAccountRowsDiv" data-ajax="false"role="tab" id="weekAccountRowsDiv-tab" data-toggle="tab" aria-controls="weekAccountRowsDiv"aria-expanded="false">本周</a>
              </li>
              <li role="presentation">
                  <a href="#monthAccountRowsDiv" data-ajax="false"role="tab" id="monthAccountRowsDiv-tab" data-toggle="tab" aria-controls="monthAccountRowsDiv"  aria-expanded="false">本月</a>
              </li>
            </ul>
            <div id="myTabContent" class="tab-content">
              <div role="tabpanel" class="tab-pane fade in active" id="todayAccountRowsDiv" aria-labelledBy="todayAccountRowsDiv-tab">
                  <?php if($todayAccountCount > 0): ?><div class="panel panel-success">
                       <div class="panel-heading text-center">
                         <font>今日 共有  <strong><?php echo ($todayAccountCount); ?></strong>  笔记账  </font>
                           <font>总支出  <i class="fa fa-rmb"></i>  <strong><?php echo ($accountOut['todayOut']); ?></strong></font>
                       </div>
                       <div data-role="navbar">
                           <ul>
                              <li><a href="#">名称</a></li>
                              <li><a href="#">类别</a></li>
                              <li><a href="#">金额</a></li>
                            </ul>
                       </div>
                    </div>
                      <?php if(is_array($todayAccountRows)): $i = 0; $__LIST__ = $todayAccountRows;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div onclick="accountDownTrToggle('#todayAccountTr<?php echo ($vo["accountid"]); ?>');">
                             <div class="ui-grid-b">
                                <div class="ui-block-a text-center" ><li class=" no-border list-group-item over-hidden"><font><?php echo ($vo["accountname"]); ?></font></li></div>
                                <div class="ui-block-b text-center"><li class=" no-border list-group-item over-hidden" ><font color="#FF0066"><?php echo ($vo["typename"]); ?></font></li></div>
                                <div class="ui-block-c text-center"><li class=" no-border list-group-item over-hidden"><font color="#CC3366"><strong><?php echo ($vo["accountmoney"]); ?></strong></font> <i class="fa fa-caret-down"></i></li></div>
                            </div>
                        </div>
                          <div class="account-down-tr well " hidden id="todayAccountTr<?php echo ($vo["accountid"]); ?>" >
                            <div class="list-group">
                              <div class="list-group-item">
                                <h5><small>账目名称: <?php echo ($vo["accountname"]); ?></small></h5>
                              </div>
                              <div class="list-group-item">
                                <h5><small>账目日期: <?php echo ($vo["accountdate"]); ?></small></h5>
                              </div>
                              <div class="list-group-item">
                                <h5><small>记录时间: <?php echo ($vo["notetime"]); ?></small></h5>
                              </div>
                              <div class="list-group-item">
                                <h5><small>相关备注： <?php echo ($vo["accountother"]); ?></small></h5>
                              </div>
                              <div class="list-group-item">
                                <button type=""><a href="/hellomarkertest/mobile.php/Home/Account/deleteAccount/id/<?php echo ($vo["accountid"]); ?>" data-ajax="false" onclick="javascript:return myNoteDeleteConfirm();" class="btn btn-sm btn-danger btn-xs width100"><font class="color-fff">删除</font></a></button>
                                <h5><small><i class="fa fa-info"></i>  ： 删除后相应账目自动计算！</small></h5>
                              </div>
                            </div>
                         </div><?php endforeach; endif; else: echo "" ;endif; ?>
                   <?php else: ?>
                         <div class="col-md-12 text-center">
                           <h4><i class="fa fa-warning fa-2x"></i>  暂无支出</h4>
                         </div><?php endif; ?>
              </div><!--today tab panel end-->
              <div role="tabpanel" class="tab-pane fade " id="weekAccountRowsDiv" aria-labelledBy="weekAccountRowsDiv-tab">
                 <?php if($weekAccountCount > 0): ?><div class="panel panel-warning">
                       <div class="panel-heading text-center">
                           <font>本周 共有  <strong><?php echo ($weekAccountCount); ?></strong>  笔记账  </font>
                             <font>支出  <i class="fa fa-rmb"></i>  <strong><?php echo ($accountOut['todayOut']); ?></strong></font>
                       </div>
                       <div data-role="navbar">
                           <ul>
                              <li><a href="#">名称</a></li>
                              <li><a href="#">类别</a></li>
                              <li><a href="#">金额</a></li>
                            </ul>
                       </div>
                   </div>
                    <?php if(is_array($weekAccountRows)): $i = 0; $__LIST__ = $weekAccountRows;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div onclick="accountDownTrToggle('#weekAccountTr<?php echo ($vo["accountid"]); ?>');">
                           <div class="ui-grid-b">
                              <div class="ui-block-a text-center" ><li class="no-border list-group-item over-hidden"><font><?php echo ($vo["accountname"]); ?></font></li></div>
                              <div class="ui-block-b text-center"><li class="no-border list-group-item over-hidden" ><font color="#FF0066"><?php echo ($vo["typename"]); ?></font></li></div>
                              <div class="ui-block-c text-center"><li class="no-border list-group-item over-hidden"><font color="#CC3366"><strong><?php echo ($vo["accountmoney"]); ?></strong></font> <i class="fa fa-caret-down"></i></li></div>
                          </div>
                      </div>
                        <div class="account-down-tr well " hidden id="weekAccountTr<?php echo ($vo["accountid"]); ?>" >
                          <div class="list-group">
                            <div class="list-group-item">
                              <h5><small>账目名称: <?php echo ($vo["accountname"]); ?></small></h5>
                            </div>
                            <div class="list-group-item">
                              <h5><small>账目日期: <?php echo ($vo["accountdate"]); ?></small></h5>
                            </div>
                            <div class="list-group-item">
                              <h5><small>记录时间: <?php echo ($vo["notetime"]); ?></small></h5>
                            </div>
                            <div class="list-group-item">
                              <h5><small>相关备注： <?php echo ($vo["accountother"]); ?></small></h5>
                            </div>
                            <div class="list-group-item">
                              <button type=""><a href="/hellomarkertest/mobile.php/Home/Account/deleteAccount/id/<?php echo ($vo["accountid"]); ?>" data-ajax="false" onclick="javascript:return myNoteDeleteConfirm();" class="btn btn-sm btn-danger btn-xs width100"><font class="color-fff">删除</font></a></button>
                              <h5><small><i class="fa fa-info"></i>  ： 删除后相应账目自动计算！</small></h5>
                            </div>
                          </div>
                       </div><?php endforeach; endif; else: echo "" ;endif; ?>
                 <?php else: ?>
                   <div class="well text-center">
                     <h4><i class="fa fa-warning fa-2x"></i>  暂无支出</h4>
                   </div><?php endif; ?>
              </div><!--week panel-->
              <div role="tabpanel" class="tab-pane fade in " id="monthAccountRowsDiv" aria-labelledBy="monthAccountRowsDiv-tab">
                  <?php if($monthAccountCount > 0): ?><div class="panel panel-info">
                       <div class="panel-heading text-center">
                         <font>本月 共有  <strong><?php echo ($monthAccountCount); ?></strong>  笔记账  </font>
                           <font>支出  <i class="fa fa-rmb"></i>  <strong><?php echo ($accountOut['monthOut']); ?></strong></font>
                       </div>
                       <div data-role="navbar">
                         <ul>
                            <li><a href="#">名称</a></li>
                            <li><a href="#">类别</a></li>
                            <li><a href="#">金额</a></li>
                          </ul>
                       </div>
                    </div>
                      <?php if(is_array($monthAccountRows)): $i = 0; $__LIST__ = $monthAccountRows;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div onclick="accountDownTrToggle('#monthAccountTr<?php echo ($vo["accountid"]); ?>');">
                           <div class="ui-grid-b">
                              <div class="ui-block-a text-center" ><li class="no-border list-group-item over-hidden"><font><?php echo ($vo["accountname"]); ?></font></li></div>
                              <div class="ui-block-b text-center"><li class="no-border list-group-item over-hidden" ><font color="#FF0066"><?php echo ($vo["typename"]); ?></font></li></div>
                              <div class="ui-block-c text-center"><li class="no-border list-group-item over-hidden"><font color="#CC3366"><strong><?php echo ($vo["accountmoney"]); ?></strong></font> <i class="fa fa-caret-down"></i></li></div>
                          </div>
                      </div>
                        <div class="account-down-tr well " hidden id="monthAccountTr<?php echo ($vo["accountid"]); ?>" >
                            <div class="list-group">
                              <div class="list-group-item">
                                <h5><small>账目名称: <?php echo ($vo["accountname"]); ?></small></h5>
                              </div>
                              <div class="list-group-item">
                                <h5><small>账目日期: <?php echo ($vo["accountdate"]); ?></small></h5>
                              </div>
                              <div class="list-group-item">
                                <h5><small>记录时间: <?php echo ($vo["notetime"]); ?></small></h5>
                              </div>
                              <div class="list-group-item">
                                <h5><small>相关备注： <?php echo ($vo["accountother"]); ?></small></h5>
                              </div>
                              <div class="list-group-item">
                                <button type=""><a href="/hellomarkertest/mobile.php/Home/Account/deleteAccount/id/<?php echo ($vo["accountid"]); ?>" data-ajax="false" onclick="javascript:return myNoteDeleteConfirm();" class="btn btn-sm btn-danger btn-xs width100"><font class="color-fff">删除</font></a></button>
                                <h5><small><i class="fa fa-info"></i>  ： 删除后相应账目自动计算！</small></h5>
                              </div>
                            </div>
                        </div><?php endforeach; endif; else: echo "" ;endif; ?>
                     </div>
                   <?php else: ?>
                     <div class="col-md-12 text-center">
                       <h4><i class="fa fa-warning fa-2x"></i>  暂无支出</h4>
                     </div><?php endif; ?>
              </div><!--month panel end-->
        </div><!--tab-centent end-->
      </div>
      <div class="panel panel-default">
        <div class="panel-heading">
          <font class="panel-title">消费比例</font>
        </div>
        <div class="panel-body ">
          <div class="col-md-12 text-center">
          <h4>本月已经消费：  <i class="fa fa-rmb"></i>  <?php echo ($accountOut['monthOut']); ?></h4>
            <hr>
          </div>
              <div class="col-md-12">
                <div id="noShowPie" class="text-center" >
                 <div id="canvas-holder"  class="visible-lg" >
                      <canvas id="chart-area1" width="300px" height="300px"/>
                  </div>
                  <div id="canvas-holder" >
                      <canvas id="chart-area2" />
                  </div>
                </div>
                <?php if($monthAccountCount > 0): ?><div class="col-md-3 pull-right text-center visible-lg">
                      <div class="list-group" id="pieShowItem">
                      </div>
                  </div><?php endif; ?>
              </div>
          </div>
        </div>
        <div class="col-md-3" >
              
            <section class="side-div">
                  <div class="panel panel-default">
                    <div class="panel-heading">
                       <font >本周消费状况</font>
                    </div>
                    <div class="panel-body">
                       <div id="canvas-holder-line" >
                            <canvas id="chart-area-line" width="100%" height="60%"></canvas>
                       </div>
                       <div class="col-md-12" id="lineItemMoney">
                       </div>
                    </div>
                  </div>
                  <div class="panel panel-default">
                    <div class="panel-heading">
                       <font >近期日均消费状况</font>
                    </div>
                    <div class="panel-body">
                       <div id="canvas-holder-bar" >
                            <canvas id="chart-area-bar" width="100%" height="60%"></canvas>
                       </div>
                    </div>
                  </div>
                  <div class="panel panel-default">
                    <div class="panel-heading">
                       <font >本月最高日消费记录</font>
                    </div>
                    <div class="panel-body" id="monthMostMoney">

                    </div>
                  </div>
            </section>
        </div>
<script>
    judgeInputValue();
    accountIndexShowPie();
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