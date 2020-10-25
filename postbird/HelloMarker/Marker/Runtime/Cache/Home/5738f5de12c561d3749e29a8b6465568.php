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

<div class="container marker-work">
    <div class="row" >
    <div class="col-md-9">
       <form name="CLD" class="content">
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="datetable table  table-striped ">
            <thead>
            <tr>
            <td colSpan=7><span>公历</span>
                <select id="selectSY" name="SY" onchange="changeCld();" style="font-SIZE: 9pt">
                <script>
                    for(i=1900;i<2050;i++) document.write('<option>'+i+'</option>');
                </script>
                 </select><span>年</span>
                 <select id="selectSM"name="SM" onchange="changeCld();" style="font-SIZE: 9pt">
                <script>
                    for(i=1;i<13;i++) document.write('<option>'+i);
                </script>
                </select><span>月</span>
                <span class=" btn-header" id="returnCalendar">返回今日</span>
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
      <button class="btn btn-default" id="oldWorkDivBtn">日前 <i class="fa fa-location-arrow"></i></button>
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
      </div>
      <hr>
    </div><!--asd-->
        <div class="col-md-3" >
            <section class="side-div">
                <div class="panel">
                    <a href="/hellomarkertest/marker.php/Home/Work/newWork/" class="btn btn-primary btn-lg btn-block">新记事</a>
                </div>
            </section>
            <section id="workDiv" class="side-div">
              <font class="panel-title" id="workDivTime"><i class="fa fa-clock-o"></i>&nbsp;&nbsp;当前  •  <span id="clickDateSpan"><?php echo ($dateNow); ?></span></font>
              <hr>
                <!-- <div class="panel" id="workDivText"> -->
                <div id="clickDateDiv">
                  <?php if($nowWorkCount == 1): ?><li class="list-group-item">
                      <?php echo ($nowWorkRows[0]['workname']); ?>
                    </li>
                    <div class="oneWorkWell text-center">
                        <i class="fa fa-level-down"></i>
                    </div>
                    <div class="oneWorkWell">
                      <div class="panel">
                        <?php echo ($nowWorkRows[0]['workother']); ?>
                      </div>
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
        </div>
        <div class="col-md-9">
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
        </div><!--timeline end-->
    </div><!--row end-->
</div><!--cintainer end-->
<script>
  initial();
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