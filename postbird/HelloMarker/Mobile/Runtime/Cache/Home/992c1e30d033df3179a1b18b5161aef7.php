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
      <a href="#sharePanel" style="display:inline-block;margin-top:10px;"><i class="fa fa-list " ></i></a>
      <h1>记事本</h1>
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
        <div data-role="navbar" >
          <ul>
            <li><a href="#" id="workDivTime"><font ><i class="fa fa-clock-o"></i> 当前：<span id="clickDateSpan"><?php echo ($dateNow); ?></span></font></a></li>
          </ul>
        </div>
        <section id="workDiv" class="side-div">
          <div id="clickDateDiv">
            <?php if($nowWorkCount == 1): ?><div class="list-group-item text-center">
                <h4> <?php echo ($nowWorkRows[0]['workname']); ?></h4>
                <h5><small>
                  Lev: <font color="#D9534F"><?php if($vo["worklevel"] == 3): ?>！！！  <?php elseif($vo["worklevel"] == 2): ?>！！  <?php else: ?>！<?php endif; ?></font>
                </small></h5>
                <hr>
                <p><?php echo ($nowWorkRows[0]['workother']); ?></p>
              </div>
              <?php elseif($nowWorkCount > 1): ?>
                  <?php if(is_array($nowWorkRows)): $i = 0; $__LIST__ = $nowWorkRows;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a class="list-group-item"  role="button" data-toggle="collapse" href="#workid<?php echo ($vo["workid"]); ?>" aria-expanded="false" aria-controls="workid<?php echo ($vo["workid"]); ?>"><span ><?php echo ($vo["workname"]); ?></span> <font color="#D9534F"><?php if($vo["worklevel"] == 3): ?>！！！  <?php elseif($vo["worklevel"] == 2): ?>！！  <?php else: ?>！<?php endif; ?></font><i class="fa fa-plus pull-right"></i></a>
                    <div class="collapse" id="workid<?php echo ($vo["workid"]); ?>">
                        <div class="well">
                          <?php echo ($vo["workother"]); ?>
                        </div>
                    </div><?php endforeach; endif; else: echo "" ;endif; ?> 
              <?php else: ?>
                <div class="panel text-center" id="workDivText">
                  <font class="noWork"><i class="fa fa-calendar-times-o"></i>  暂无记事</font>
                </div><?php endif; ?>
        </div>
        </section>
         <form name="CLD" class="content">
          <table width="100%" border="0" cellpadding="0" cellspacing="0" class="datetable table  table-striped ">
              <thead>
              <tr>
              <td colSpan=7><span>年</span>
                  <select id="selectSY" name="SY" onchange="changeCld();" style="font-SIZE: 9pt">
                  <script>
                      for(i=1900;i<2050;i++) document.write('<option>'+i+'</option>');
                  </script>
                   </select><span>月</span>
                   <select id="selectSM"name="SM" onchange="changeCld();" style="font-SIZE: 9pt">
                  <script>
                      for(i=1;i<13;i++) document.write('<option>'+i);
                  </script>
                  </select>
                  <span class="btn btn-primary btn-xs width100" id="returnCalendar">返回今日</span>
                  <span id="clockLogin">
                  </span>
                  <span id="GZ"></span>
                </td>
              </tr>
              </thead>
              <tbody >
                <tr style="background:#eee;">
                  <td width="54">日</td>
                  <td width="54">一</td>
                  <td width="54">二</td>
                  <td width="54">三</td>
                  <td width="54">四</td>
                  <td width="54">五</td>
                  <td width="54">六</td>
                </tr>            
                <script>
                var gNum;
                for(i=0;i<6;i++) {
                   document.write('<tr align="center" id="calendarTr">' );
                   for(j=0;j<7;j++) {
                      gNum = i*7+j;
                      document.write('<td  class="calendarTd" id="GD' + gNum +'"><font  class="font1" id="SD' + gNum +'"  size=2 face="Arial Black"');
                      if(j == 0) document.write('color="red"');
                      if(j == 6) document.write('color="#000080"');
                      document.write('></font><br/><span id="LD' + gNum + '" size=2 style="font-size:9pt"></span></td>');
                   }
                   document.write('</tr>');
                }
               </script>
             </tbody>
          </table>
        </form>
      <a class="btn btn-default width100" id="oldWorkDivBtn" onclick="oldWorkDivClick();" data-ajax="false">日前 <i class="fa fa-location-arrow"></i></a>
      <hr>
      <div id="oldWorkDiv">
        <?php if($oldHtmlCount == 0): ?><div class="col-md-12 text-center">
             <h4><i class="fa fa-warning "></i> 无记事</h4>
            </div>
        <?php else: ?>
        <div id="oldtimeline">
            <?php if(is_array($oldHtmlRows)): foreach($oldHtmlRows as $key=>$vo): ?><div class="timeline-item">
                    <div class="timeline-icon">
                    </div>
                    <div class="timeline-content <?php if($key%2 == 0): else: ?>right<?php endif; ?>">
                      <h2><?php echo ($vo["worktime"]); ?></h2>
                      <?php if(is_array($vo)): foreach($vo as $key=>$voItem): if($voItem.workname|strlen > 1): else: ?>
                      <a class="list-group-item" role="button" data-toggle="collapse" href="#oldItem<?php echo ($voItem["workid"]); ?>" aria-expanded="false" aria-controls="oldItem<?php echo ($voItem["workid"]); ?>"><span><?php echo ($voItem["workname"]); ?></span>  <font color="#D9534F"><?php if($voItem["worklevel"] == 3): ?>！！！  <?php elseif($voItem["worklevel"] == 2): ?>！！  <?php else: ?>！<?php endif; ?></font><i class="fa fa-plus pull-right"></i></a>
                        <div class="collapse" id="oldItem<?php echo ($voItem["workid"]); ?>">
                          <div class="well">
                          <?php echo ($voItem["workother"]); ?>
                          </div>
                        </div><?php endif; endforeach; endif; ?>
                    </div>
                </div><?php endforeach; endif; ?>
        </div><?php endif; ?>
      </div><!--old div end-->
      <hr>
        <div class="FutureWorkTitle text-center">
          <h4>目前</h4>
          <hr>
        </div>
        <?php if($futureHtmlCount == 0): ?><div class="col-md-12 text-center">
             <h4><i class="fa fa-warning "></i> 无记事</h4>
          </div>
        <?php else: ?>
          <div id="timeline">
            <?php if(is_array($futureHtmlRows)): foreach($futureHtmlRows as $key=>$vo): ?><div class="timeline-item">
                    <div class="timeline-icon">
                    </div>
                    <div class="timeline-content <?php if($key%2 == 0): else: ?>right<?php endif; ?>">
                      <h2><?php echo ($vo["worktime"]); ?></h2>
                      <?php if(is_array($vo)): foreach($vo as $key=>$voItem): if($voItem.workname|strlen > 1): else: ?>
                      <a class="list-group-item" role="button" data-toggle="collapse" href="#futureItem<?php echo ($voItem["workid"]); ?>" aria-expanded="false" aria-controls="futureItem<?php echo ($voItem["workid"]); ?>"><span><?php echo ($voItem["workname"]); ?></span>  <font color="#D9534F"><?php if($voItem["worklevel"] == 3): ?>！！！  <?php elseif($voItem["worklevel"] == 2): ?>！！  <?php else: ?>！<?php endif; ?></font><i class="fa fa-plus pull-right"></i></a>
                        <div class="collapse" id="futureItem<?php echo ($voItem["workid"]); ?>">
                          <div class="well">
                          <?php echo ($voItem["workother"]); ?>
                          </div>
                        </div><?php endif; endforeach; endif; ?>
                    </div>
                </div><?php endforeach; endif; ?>
          </div><?php endif; ?>
  </div>
<script>
  initial();
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