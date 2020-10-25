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

<div class="modal fade" id="budgetModal" tabindex="-1" role="dialog" aria-labelledby="budgetModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">设置当前月预算</h4>
      </div>
      <form action="/hellomarkertest/marker.php/Home/Account/budgetChange/" method="post" accept-charset="utf-8">
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
</div><!--budget modal end-->

<div class="container marker-work">
    <div class="row" >
    <div class="col-md-9">
      <!-- <input id="testinput" type="text" placeholder="click here to see the magic" />    -->
      <script>
          // $('#testinput').calculator();
      </script>
      <div class="panel panel-blue">
        <div class=" panel-heading panel-heading-blue">
          <font class="panel-title">当前账目</font>
          <a id="accountBudgetBtn" class="btn btn-success btn-xs pull-right" data-toggle="modal" data-target="#budgetModal">设置月预算</a>
          <span class="pull-right visible-lg"><font>本月预算  <i class="fa fa-rmb"></i>  <strong ><?php echo ($userBudget); ?></strong> 元 , </font>
            <font>已经支出  <i class="fa fa-rmb"></i>  <strong ><?php echo ($accountOut['monthOut']); ?></strong> 元</font></span>
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
      <div class="visible-lg">
        <div class="breadcrumb text-center">
          <li>详细</li>
        </div>
        <div class="panel-body">
          <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
            <ul id="myTabs" class="nav nav-tabs" role="tablist">
              <li role="presentation" class="active">
                  <a href="#todayAccountRowsDiv" id="todayAccountRows-tab" role="tab" data-toggle="tab" aria-controls="todayAccountRows" aria-expanded="true">今日</a>
              </li>
              <li role="presentation">
                  <a href="#weekAccountRowsDiv" role="tab" id="weekAccountRowsDiv-tab" data-toggle="tab" aria-controls="weekAccountRowsDiv">本周</a>
              </li>
              <li role="presentation">
                  <a href="#monthAccountRowsDiv" role="tab" id="monthAccountRowsDiv-tab" data-toggle="tab" aria-controls="monthAccountRowsDiv">本月</a>
              </li>
            </ul>
            <div id="myTabContent" class="tab-content">
              <div role="tabpanel" class="tab-pane fade in active" id="todayAccountRowsDiv" aria-labelledBy="todayAccountRowsDiv-tab">
              <?php if($todayAccountCount > 0): ?><div class="panel panel-warning">
                 <div class="panel-heading text-center">
                 <font>今日 共有  <strong><?php echo ($todayAccountCount); ?></strong>  笔记账  </font>
                   <font>总支出  <i class="fa fa-rmb"></i>  <strong><?php echo ($accountOut['todayOut']); ?></strong></font>
                 </div>
                 <div class="col-md-12 width100">
                   <div class="width20"><font>名称</font></div>
                   <div class="width20"><font>类别</font></div>
                   <div class="width20"><font>时间</font></div>
                   <div class="width20"><font>金额</font></div>
                   <div class="width20"><font>&nbsp;&nbsp;&nbsp;&nbsp;</font></div>
                 </div>
                    <?php if(is_array($todayAccountRows)): $i = 0; $__LIST__ = $todayAccountRows;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="pointer-tr list-group col-md-12" onmouseover="accountDownImgShow('#todayAccountDownImgId<?php echo ($vo["accountid"]); ?>');" onmouseout="accountDownImgOut('#todayAccountDownImgId<?php echo ($vo["accountid"]); ?>');" onclick="accountDownTrToggle('#todayAccountTr<?php echo ($vo["accountid"]); ?>');">
                        <div class="width20"><?php echo ($vo["accountname"]); ?></div>
                        <div class="width20"><font color="#FF0066"><?php echo ($vo["typename"]); ?></font></div>
                        <div class="width20"><?php echo ($vo["accountdate"]); ?></div>
                        <div class="width20">  <i class="fa fa-rmb"></i>  <font color="#CC3366"><strong><?php echo ($vo["accountmoney"]); ?></strong></font></div>
                         <div class="width20"><font class="down-img hidden" id ="todayAccountDownImgId<?php echo ($vo["accountid"]); ?>" > ▼</font></div>
                      </div>
                        <div class="col-md-12 account-down-tr well" id="todayAccountTr<?php echo ($vo["accountid"]); ?>" >
                          <div class="list-group">
                            <div class="list-group-item">
                            <font><i class="fa fa-clock-o"></i>&nbsp;&nbsp;记录时间: <?php echo ($vo["notetime"]); ?></font>
                            </div>
                            <div class="list-group-item">
                              <font><i class="fa fa-file-o"></i>&nbsp;&nbsp;相关备注： <?php echo ($vo["accountother"]); ?></font>
                            </div>
                            <div class="list-group-item">
                              <a href="/hellomarkertest/marker.php/Home/Account/deleteAccount/id/<?php echo ($vo["accountid"]); ?>" onclick="javascript:return myNoteDeleteConfirm();" class="btn btn-sm btn-danger btn-long">删除</a>
                              &nbsp;&nbsp;&nbsp;&nbsp;<font><i class="fa fa-info"></i>  ： 删除后相应账目自动计算！</font>
                            </div>
                          </div><!--listgroup end-->
                       </div><!--well end--><?php endforeach; endif; else: echo "" ;endif; ?>
                  </div><!--panel end-->
               <?php else: ?>
                     <div class="col-md-12 text-center">
                       <h4><i class="fa fa-warning fa-2x"></i>  暂无支出</h4>
                     </div><?php endif; ?>
              </div><!--today tab panel end-->
              <div role="tabpanel" class="tab-pane fade" id="weekAccountRowsDiv" aria-labelledBy="weekAccountRowsDiv-tab">
                <?php if($weekAccountCount > 0): ?><div class="panel panel-warning">
                   <div class="panel-heading text-center">
                   <font>本周 共有  <strong><?php echo ($weekAccountCount); ?></strong>  笔记账  </font>
                     <font>总支出  <i class="fa fa-rmb"></i>  <strong><?php echo ($accountOut['weekOut']); ?></strong></font>
                   </div>
                   <div class="col-md-12 width100">
                     <div class="width20"><font>名称</font></div>
                     <div class="width20"><font>类别</font></div>
                     <div class="width20"><font>时间</font></div>
                     <div class="width20"><font>金额</font></div>
                     <div class="width20"><font>&nbsp;&nbsp;&nbsp;&nbsp;</font></div>
                   </div>
                      <?php if(is_array($weekAccountRows)): $i = 0; $__LIST__ = $weekAccountRows;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="pointer-tr list-group col-md-12" onmouseover="accountDownImgShow('#weekAccountDownImgId<?php echo ($vo["accountid"]); ?>');" onmouseout="accountDownImgOut('#weekAccountDownImgId<?php echo ($vo["accountid"]); ?>');" onclick="accountDownTrToggle('#weekAccountTr<?php echo ($vo["accountid"]); ?>');">
                          <div class="width20"><?php echo ($vo["accountname"]); ?></div>
                          <div class="width20"><font color="#FF0066"><?php echo ($vo["typename"]); ?></font></div>
                          <div class="width20"><?php echo ($vo["accountdate"]); ?></div>
                          <div class="width20">  <i class="fa fa-rmb"></i>  <font color="#CC3366"><strong><?php echo ($vo["accountmoney"]); ?></strong></font></div>
                           <div class="width20"><font class="down-img hidden" id ="weekAccountDownImgId<?php echo ($vo["accountid"]); ?>" > ▼</font></div>
                        </div>
                          <div class="col-md-12 account-down-tr well" id="weekAccountTr<?php echo ($vo["accountid"]); ?>" >
                            <div class="list-group">
                              <div class="list-group-item">
                              <font><i class="fa fa-clock-o"></i>&nbsp;&nbsp;记录时间: <?php echo ($vo["notetime"]); ?></font>
                              </div>
                              <div class="list-group-item">
                                <font><i class="fa fa-file-o"></i>&nbsp;&nbsp;相关备注： <?php echo ($vo["accountother"]); ?></font>
                              </div>
                              <div class="list-group-item">
                                <a href="/hellomarkertest/marker.php/Home/Account/deleteAccount/id/<?php echo ($vo["accountid"]); ?>" onclick="javascript:return myNoteDeleteConfirm();" class="btn btn-sm btn-danger btn-long">删除</a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<font><i class="fa fa-info"></i>  ： 删除后相应账目自动计算！</font>
                              </div>
                            </div><!--listgroup end-->
                         </div><!--well end--><?php endforeach; endif; else: echo "" ;endif; ?>
                 </div><!--panel end-->
                 <?php else: ?>
                   <div class="col-md-12 text-center">
                     <h4><i class="fa fa-warning fa-2x"></i>  暂无支出</h4>
                   </div><?php endif; ?>
              </div><!--week panel-->
              <div role="tabpanel" class="tab-pane fade in " id="monthAccountRowsDiv" aria-labelledBy="monthAccountRowsDiv-tab">
                  <?php if($monthAccountCount > 0): ?><div class="panel panel-warning">
                     <div class="panel-heading text-center">
                     <font>本月 共有  <strong><?php echo ($monthAccountCount); ?></strong>  笔记账  </font>
                       <font>总支出  <i class="fa fa-rmb"></i>  <strong><?php echo ($accountOut['monthOut']); ?></strong></font>
                     </div>
                     <div class="col-md-12 width100">
                       <div class="width20"><font>名称</font></div>
                       <div class="width20"><font>类别</font></div>
                       <div class="width20"><font>时间</font></div>
                       <div class="width20"><font>金额</font></div>
                       <div class="width20"><font>&nbsp;&nbsp;&nbsp;&nbsp;</font></div>
                     </div>
                        <?php if(is_array($monthAccountRows)): $i = 0; $__LIST__ = $monthAccountRows;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="pointer-tr list-group col-md-12" onmouseover="accountDownImgShow('#monthAccountDownImgId<?php echo ($vo["accountid"]); ?>');" onmouseout="accountDownImgOut('#monthAccountDownImgId<?php echo ($vo["accountid"]); ?>');" onclick="accountDownTrToggle('#monthAccountTr<?php echo ($vo["accountid"]); ?>');">
                            <div class="width20"><?php echo ($vo["accountname"]); ?></div>
                            <div class="width20"><font color="#FF0066"><?php echo ($vo["typename"]); ?></font></div>
                            <div class="width20"><?php echo ($vo["accountdate"]); ?></div>
                            <div class="width20">  
                               <i class="fa fa-rmb"></i>  <font color="#CC3366"><strong><?php echo ($vo["accountmoney"]); ?></strong></font>
                            </div>
                            <div class="width20">
                               <font class="down-img hidden" id ="monthAccountDownImgId<?php echo ($vo["accountid"]); ?>" > ▼ </font> 
                            </div>
                          </div>
                            <div class="col-md-12 account-down-tr well" id="monthAccountTr<?php echo ($vo["accountid"]); ?>" >
                              <div class="list-group">
                                <div class="list-group-item">
                                    <font><i class="fa fa-clock-o"></i>&nbsp;&nbsp;记录时间: <?php echo ($vo["notetime"]); ?></font>
                                </div>
                                <div class="list-group-item">
                                  <font><i class="fa fa-file-o"></i>&nbsp;&nbsp;相关备注： <?php echo ($vo["accountother"]); ?></font>
                                </div>
                                <div class="list-group-item">
                                  <a href="/hellomarkertest/marker.php/Home/Account/deleteAccount/id/<?php echo ($vo["accountid"]); ?>" onclick="javascript:return myNoteDeleteConfirm();" class="btn btn-sm btn-danger btn-long">删除</a>
                                  &nbsp;&nbsp;&nbsp;&nbsp;<font><i class="fa fa-info"></i>  ： 删除后相应账目自动计算！</font>
                                </div>
                              </div><!--listgroup end-->
                           </div><!--well end--><?php endforeach; endif; else: echo "" ;endif; ?>
                    </div><!--panel end-->
                   <?php else: ?>
                     <div class="col-md-12 text-center">
                       <h4><i class="fa fa-warning fa-2x"></i>  暂无支出</h4>
                     </div><?php endif; ?>
              </div><!--month panel end-->
            </div>
          </div><!-- /example -->      
        </div><!--panel body end-->
      </div><!-- visible end-->
      <div class="hidden-lg">
        <div class="breadcrumb text-center">
          <li>详细</li>
        </div>
        <div class="panel-body">
          <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
            <ul id="MmyTabs" class="nav nav-tabs" role="tablist">
              <li role="presentation" class="active">
                  <a href="#MtodayAccountRowsDiv" id="MtodayAccountRows-tab" role="tab" data-toggle="tab" aria-controls="MtodayAccountRows" aria-expanded="true">今日</a>
              </li>
              <li role="presentation">
                  <a href="#MweekAccountRowsDiv" role="tab" id="MweekAccountRowsDiv-tab" data-toggle="tab" aria-controls="MweekAccountRowsDiv">本周</a>
              </li>
              <li role="presentation">
                  <a href="#MmonthAccountRowsDiv" role="tab" id="MmonthAccountRowsDiv-tab" data-toggle="tab" aria-controls="MmonthAccountRowsDiv">本月</a>
              </li>
            </ul>
            <div id="MmyTabContent" class="tab-content">
              <div role="tabpanel" class="tab-pane fade in active" id="MtodayAccountRowsDiv" aria-labelledBy="MtodayAccountRowsDiv-tab">
              <?php if($todayAccountCount > 0): ?><div class="panel panel-warning">
                 <div class="panel-heading text-center">
                 <font>今日 共有  <strong><?php echo ($todayAccountCount); ?></strong>  笔记账  </font>
                   <font>总支出  <i class="fa fa-rmb"></i>  <strong><?php echo ($accountOut['todayOut']); ?></strong></font>
                 </div>
                    <?php if(is_array($todayAccountRows)): $i = 0; $__LIST__ = $todayAccountRows;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="col-md-12 pointer-tr list-group-item"onclick="accountDownTrToggle('#MtodayAccountTr<?php echo ($vo["accountid"]); ?>');">
                          <font><?php echo ($vo["accountname"]); ?> 
                          <font color="#CC3366" class="font-rmb-center"><i class="fa fa-rmb"></i>  <strong><?php echo ($vo["accountmoney"]); ?></strong></font> 
                          <font class="down-img pull-right"> ▼ </font></font>
                        </div>
                        <div class="col-md-12 account-down-tr well" id="MtodayAccountTr<?php echo ($vo["accountid"]); ?>" >
                          <div class="list-group">
                            <div class="list-group-item">
                              <font><i class="fa fa-columns"></i>&nbsp;&nbsp;类别: <?php echo ($vo["typename"]); ?></font>
                            </div>
                            <div class="list-group-item">
                              <font><i class="fa fa-clock-o"></i>&nbsp;&nbsp;时间: <?php echo ($vo["accountdate"]); ?></font>
                            </div>
                            <div class="list-group-item">
                              <font><i class="fa fa-file-o"></i>&nbsp;&nbsp;备注： <?php echo ($vo["accountother"]); ?></font>
                            </div>
                            <div class="list-group-item">
                              <a href="/hellomarkertest/marker.php/Home/Account/deleteAccount/id/<?php echo ($vo["accountid"]); ?>" onclick="javascript:return myNoteDeleteConfirm();" class="btn btn-sm btn-danger btn-long">删除</a>
                              &nbsp;&nbsp;&nbsp;&nbsp;<font><i class="fa fa-info"></i>  ： 删除后相应账目自动计算！</font>
                            </div>
                          </div><!--listgroup end-->
                       </div><!--well end--><?php endforeach; endif; else: echo "" ;endif; ?>
                  </div><!--panel end-->
               <?php else: ?>
                     <div class="col-md-12 text-center">
                       <h4><i class="fa fa-warning fa-2x"></i>  暂无支出</h4>
                     </div><?php endif; ?>
              </div><!--today tab panel end-->
              <div role="tabpanel" class="tab-pane fade" id="MweekAccountRowsDiv" aria-labelledBy="MweekAccountRowsDiv-tab">
                <?php if($weekAccountCount > 0): ?><div class="panel panel-warning">
                   <div class="panel-heading text-center">
                   <font>本周 共有  <strong><?php echo ($weekAccountCount); ?></strong>  笔记账  </font>
                     <font>总支出  <i class="fa fa-rmb"></i>  <strong><?php echo ($accountOut['weekOut']); ?></strong></font>
                   </div>
                   <div class="col-md-12 width100">
                     <div class="width20"><font>名称</font></div>
                     <div class="width20"><font>类别</font></div>
                     <div class="width20"><font>时间</font></div>
                     <div class="width20"><font>金额</font></div>
                     <div class="width20"><font>&nbsp;&nbsp;&nbsp;&nbsp;</font></div>
                   </div>
                      <?php if(is_array($weekAccountRows)): $i = 0; $__LIST__ = $weekAccountRows;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="col-md-12 pointer-tr list-group-item"onclick="accountDownTrToggle('#MweekAccountTr<?php echo ($vo["accountid"]); ?>');">
                          <font><?php echo ($vo["accountname"]); ?> </font>
                          <font color="#CC3366" class="font-rmb-center"><i class="fa fa-rmb"></i>  <strong><?php echo ($vo["accountmoney"]); ?></strong></font> 
                          <font class="down-img pull-right"> ▼ </font>
                        </div>
                        <div class="col-md-12 account-down-tr well" id="MweekAccountTr<?php echo ($vo["accountid"]); ?>" >
                          <div class="list-group">
                            <div class="list-group-item">
                              <font><i class="fa fa-columns"></i>&nbsp;&nbsp;类别: <?php echo ($vo["typename"]); ?></font>
                            </div>
                            <div class="list-group-item">
                              <font><i class="fa fa-clock-o"></i>&nbsp;&nbsp;时间: <?php echo ($vo["accountdate"]); ?></font>
                            </div>
                            <div class="list-group-item">
                              <font><i class="fa fa-file-o"></i>&nbsp;&nbsp;备注： <?php echo ($vo["accountother"]); ?></font>
                            </div>
                            <div class="list-group-item">
                              <a href="/hellomarkertest/marker.php/Home/Account/deleteAccount/id/<?php echo ($vo["accountid"]); ?>" onclick="javascript:return myNoteDeleteConfirm();" class="btn btn-sm btn-danger btn-long">删除</a>
                              &nbsp;&nbsp;&nbsp;&nbsp;<font><i class="fa fa-info"></i>  ： 删除后相应账目自动计算！</font>
                            </div>
                          </div><!--listgroup end-->
                       </div><!--well end--><?php endforeach; endif; else: echo "" ;endif; ?>
                 </div><!--panel end-->
                 <?php else: ?>
                   <div class="col-md-12 text-center">
                     <h4><i class="fa fa-warning fa-2x"></i>  暂无支出</h4>
                   </div><?php endif; ?>
              </div><!--week panel-->
              <div role="tabpanel" class="tab-pane fade in " id="MmonthAccountRowsDiv" aria-labelledBy="MmonthAccountRowsDiv-tab">
                  <?php if($monthAccountCount > 0): ?><div class="panel panel-warning">
                     <div class="panel-heading text-center">
                     <font>本月 共有  <strong><?php echo ($monthAccountCount); ?></strong>  笔记账  </font>
                       <font>总支出  <i class="fa fa-rmb"></i>  <strong><?php echo ($accountOut['monthOut']); ?></strong></font>
                     </div>
                     <div class="col-md-12 width100">
                       <div class="width20"><font>名称</font></div>
                       <div class="width20"><font>类别</font></div>
                       <div class="width20"><font>时间</font></div>
                       <div class="width20"><font>金额</font></div>
                       <div class="width20"><font>&nbsp;&nbsp;&nbsp;&nbsp;</font></div>
                     </div>
                      <?php if(is_array($monthAccountRows)): $i = 0; $__LIST__ = $monthAccountRows;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="col-md-12 pointer-tr list-group-item"onclick="accountDownTrToggle('#MmonthAccountTr<?php echo ($vo["accountid"]); ?>');">
                            <font><?php echo ($vo["accountname"]); ?> </font>
                            <font color="#CC3366" class="font-rmb-center"><i class="fa fa-rmb"></i>  <strong><?php echo ($vo["accountmoney"]); ?></strong></font> 
                            <font class="down-img pull-right"> ▼ </font>
                          </div>
                          <div class="col-md-12 account-down-tr well" id="MmonthAccountTr<?php echo ($vo["accountid"]); ?>" >
                            <div class="list-group">
                              <div class="list-group-item">
                                <font><i class="fa fa-columns"></i>&nbsp;&nbsp;类别: <?php echo ($vo["typename"]); ?></font>
                              </div>
                              <div class="list-group-item">
                                <font><i class="fa fa-clock-o"></i>&nbsp;&nbsp;时间: <?php echo ($vo["accountdate"]); ?></font>
                              </div>
                              <div class="list-group-item">
                                <font><i class="fa fa-file-o"></i>&nbsp;&nbsp;备注： <?php echo ($vo["accountother"]); ?></font>
                              </div>
                              <div class="list-group-item">
                                <a href="/hellomarkertest/marker.php/Home/Account/deleteAccount/id/<?php echo ($vo["accountid"]); ?>" onclick="javascript:return myNoteDeleteConfirm();" class="btn btn-sm btn-danger btn-long">删除</a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<font><i class="fa fa-info"></i>  ： 删除后相应账目自动计算！</font>
                              </div>
                            </div><!--listgroup end-->
                         </div><!--well end--><?php endforeach; endif; else: echo "" ;endif; ?>
                    </div><!--panel end-->
                   <?php else: ?>
                     <div class="col-md-12 text-center">
                       <h4><i class="fa fa-warning fa-2x"></i>  暂无支出</h4>
                     </div><?php endif; ?>
              </div><!--month panel end-->
            </div>
          </div><!-- /example -->      
        </div><!--panel body end-->
      </div><!-- visible end-->
    </div><!--panel blue end-->
      <div class="panel panel-default">
        <div class="panel-heading">
          <font class="panel-title">消费比例</font>
          <a id="accountBudgetBtn" class="btn btn-success btn-xs pull-right" href="/hellomarkertest/marker.php/Home/Account/allReport/">详细报表</a>
        </div>
        <div class="panel-body ">
          <div class="col-md-12 text-center">
          <h4>本月已经消费：  <i class="fa fa-rmb"></i>  <?php echo ($accountOut['monthOut']); ?></h4>
            <hr>
          </div>
              <div class="col-md-12">
                <div id="noShowPie" class="col-md-9 text-center" >
                 <div id="canvas-holder" class="visible-lg" >
                      <canvas id="chart-area1" width="300px" height="300px"/>
                  </div>
                  <div id="canvas-holder" class="hidden-lg" >
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
      </div>
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
                    <a href="/hellomarkertest/marker.php/Home/Account/allReport/" class="btn btn-info btn-lg btn-block">详细报表</a>
                </div>
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
    </div><!--row end-->
    </div><!--cintainer end-->
<script>
    judgeInputValue();
    accountIndexShowPie();
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