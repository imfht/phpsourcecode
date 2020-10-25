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
    
    


<div class="container marker-work marker-report">
    <div class="row" >
    <div class="col-md-9">
      <div class="panel panel-primary ">
        <div class=" panel-heading">
          <font class="panel-title">天气 ： <i class="fa fa-map-marker"></i>
          <?php echo ($clientAddress); ?> &nbsp;&nbsp;<small >[当前城市]</small></font>
             <font class="panel-title hidden-lg"><br>更新时间 ：<?php echo ($weatherBasic['update']['loc']); ?></font>
             <font class="panel-title pull-right visible-lg">更新时间 ：<?php echo ($weatherBasic['update']['loc']); ?></font>
        </div>
        <div class="panel-body">
          <div class="col-md-6 text-center col-xs-12">
            <img src="<?php echo ($weatherNow['cond']['icon']); ?>" title="<?php echo ($weatherNow['cond']['text']); ?>" class="weather-icon-stylie">
            <div class="list-group text-left ">
              <li class="list-group-item list-group-item-shadow list-group-item-success">
                <font>实时天气 ： <font class="colorCC0033"><?php echo ($weatherNow['cond']['txt']); ?></font></font>
              </li>
              <li class="list-group-item list-group-item-shadow">
                <font>环境温度 ：<font class="colorCC0033"><?php echo ($weatherNow['tmp']); ?></font> ℃
                   <font class="pull-right">体感温度 ： <font class="colorCC0033"><?php echo ($weatherNow['fl']); ?></font> ℃</font>
                </font>
                
              </li> 
              <li class="list-group-item list-group-item-shadow">
                <font>风力风向 ：<font class="colorCC0033"><?php echo ($weatherNow['wind']['dir']); ?></font> 
                   <span class="pull-right">风力：<font class="colorCC0033"><?php echo ($weatherNow['wind']['sc']); ?></font> 级 |  风速：<font class="colorCC0033"><?php echo ($weatherNow['wind']['spd']); ?></font> kmph</span>
                 </font>
              </li>
              <li class="list-group-item list-group-item-shadow">
                <font>能见度&nbsp;&nbsp;&nbsp;&nbsp;： <font class="colorCC0033"><?php echo ($weatherNow['vis']); ?></font> km</font>
              </li>
              <li class="list-group-item list-group-item-shadow">
                <font>相对湿度 ： <font class="colorCC0033"><?php echo ($weatherNow['hum']); ?></font> ％
                   <font class="pull-right">降水量 ： <font class="colorCC0033"><?php echo ($weatherNow['pcpn']); ?></font> ㎜</font>
                </font>
              </li> 
          </div>
        </div>
          <div class="col-md-6 col-xs-12 ">
            <div class="list-group text-left ">
                <li class="list-group-item list-group-item-shadow list-group-item-info">

                  <font class="panel-title">空气质量指数 ： <font class="colorCC0033"><?php echo ($weatherAqi['aqi']); ?></font>
                     <span class="pull-right">级别 ：<span class="badge"><?php echo ($weatherAqi['qlty']); ?></span></span>
                  </font>
                </li>
                <li class="list-group-item list-group-item-shadow">
                  <font class="panel-title">PM 2.5<span class="pull-right"><font class="colorCC0033"><?php echo ($weatherAqi['pm25']); ?></font>  ug/m³</span></font>
                </li>
                <li class="list-group-item list-group-item-shadow">
                  <font class="panel-title">CO <span class="pull-right"><font class="colorCC0033"><?php echo ($weatherAqi['co']); ?></font>  ug/m³ </span></font>
                </li> 
                <li class="list-group-item list-group-item-shadow">
                  <font class="panel-title">CO₂ <span class="pull-right"><font class="colorCC0033"><?php echo ($weatherAqi['no2']); ?></font>  ug/m³</span></font>
                </li>
                <li class="list-group-item list-group-item-shadow">
                  <font class="panel-title">O3 <span class="pull-right"><font class="colorCC0033"><?php echo ($weatherAqi['o3']); ?></font>  ug/m³</span></font>
                </li>
                <li class="list-group-item list-group-item-shadow">
                  <font >SO₂ <span class="pull-right"><font class="colorCC0033"><?php echo ($weatherAqi['so2']); ?></font>  ug/m³</span></font>
                </li>
                <li class="list-group-item list-group-item-shadow">
                  <font >PM 10<span class="pull-right"><font class="colorCC0033"><?php echo ($weatherAqi['pm10']); ?></font>   ug/m³</span></font>
                </li> 
            </div>
          </div>
        </div><!--panel-body-->
      </div><!----panel-primary -->
       <div class="panel panel-primary ">
        <div class=" panel-heading">
        <font class="panel-title">建议</font>
        </div>
        <div class="panel-body">
          <div class="list-group">
            <li class="list-group-item">
            <h4 class="list-group-item-heading">户外运动 ：<font class="colorCC0033"><?php echo ($weatherSuggestion['sport']['brf']); ?></font></h4>
              <hr>
              <p class="list-group-item-text"><?php echo ($weatherSuggestion['sport']['txt']); ?></p>
            </li>
            <li class="list-group-item">
            <h4 class="list-group-item-heading">外出旅行 ：<font class="colorCC0033"><?php echo ($weatherSuggestion['trav']['brf']); ?></font></h4>
              <hr>
              <p class="list-group-item-text"><?php echo ($weatherSuggestion['trav']['txt']); ?></p>
            </li>
          </div>
        </div><!--panel-body-->
      </div><!----panel-primary -->
      <div class="panel panel-default">
        <div class="panel-heading">
          <font class="panel-title">七天天气预报</font>
        </div>
        <div class="panel-body">
          <div  data-example-id="togglable-tabs">
            <ul id="myTabs" class="nav nav-tabs" role="tablist">
            <?php if(is_array($weatherDailyForecast)): $k = 0; $__LIST__ = $weatherDailyForecast;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k; if($k == 1): ?><li role="presentation"  class="active">
              <?php else: ?>
                  <li role="presentation" ><?php endif; ?>
                  <a href="#weatherDailyForecastDiv<?php echo ($vo["date"]); ?>" id="weatherDailyForecastDiv<?php echo ($vo["date"]); ?>-tab" role="tab" data-toggle="tab" aria-controls="weatherDailyForecastDiv<?php echo ($vo["date"]); ?>" aria-expanded="true"><?php echo ($vo["subDate"]); ?></a>
              </li><?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
            <div id="myTabContent" class="tab-content">
              <?php if(is_array($weatherDailyForecast)): $k = 0; $__LIST__ = $weatherDailyForecast;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k; if($k == 1): ?><div role="tabpanel" class="tab-pane fade active in" id="weatherDailyForecastDiv<?php echo ($vo["date"]); ?>" aria-labelledby="weatherDailyForecastDiv<?php echo ($vo["date"]); ?>-tab">
                <?php else: ?>
                    <div role="tabpanel" class="tab-pane fade" id="weatherDailyForecastDiv<?php echo ($vo["date"]); ?>" aria-labelledby="weatherDailyForecastDiv<?php echo ($vo["date"]); ?>-tab"><?php endif; ?>
                  <div class="col-md-12 text-center">
                    <h5>时间 ： <strong><?php echo ($vo["date"]); ?></strong></h5>
                    <h4><small>相对湿度 ： <?php echo ($vo["hum"]); ?> ％</small></h4>
                    <h4><small>降水概率 ： <?php echo ($vo["pop"]); ?> ％</small></h4>
                    <h4><small>能见度 ： <?php echo ($vo["vis"]); ?> ㎞</small></h4>
                    <h4><small>风向 ： <?php echo ($vo["wind"]["dir"]); ?> </small> <small>「  <?php echo ($vo["wind"]["sc"]); ?>  」</small></h4>
                    <h4>
                        <span class="pull-left ">最高温度 ： <span class="colorCC0033"><?php echo ($vo["tmp"]["max"]); ?></span> ℃</span>
                        <span class="pull-right">最低温度 ： <span class="colorCC0033"><?php echo ($vo["tmp"]["min"]); ?></span> ℃</span>
                    </h4>
                    <hr>
                  </div>
                  <div class="col-md-6 col-xs-12 text-center">
                    <h4><i class="fa fa-sun-o"></i> &nbsp; <strong>白天</strong></h4>
                    <h4><small>日出 ： <?php echo ($vo["astro"]["sr"]); ?></small></h4>
                    <hr>
                    <img src="<?php echo ($vo["cond"]["icon_d"]); ?>" alt="<?php echo ($vo["cond['txt_d']"]); ?>" class="weather-icon-stylie">
                    <div class="list-group text-center ">
                        <li class="list-group-item  list-group-item-warning">
                          <font class="panel-title">天气状况 ： <font class="colorCC0033"><?php echo ($vo["cond"]["txt_d"]); ?></font>
                          </font>
                        </li>
                    </div>
                  </div>
                  <div class="col-md-6 col-xs-12 text-center">
                    <h4><i class="fa fa-moon-o"></i> &nbsp; <strong>夜间</strong></h4>
                    <h4><small>日落 ： <?php echo ($vo["astro"]["ss"]); ?></small></h4>
                    <hr>
                    <img src="<?php echo ($vo["cond"]["icon_n"]); ?>" alt="<?php echo ($vo["cond['txt_n']"]); ?>" class="weather-icon-stylie">
                    <div class="list-group text-center ">
                        <li class="list-group-item  list-group-item-warning">
                          <font class="panel-title">天气状况 ： <font class="colorCC0033"><?php echo ($vo["cond"]["txt_n"]); ?></font>
                          </font>
                        </li>
                    </div>
                  </div>
                </div><!--tab div end--><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
          </div><!--tab-list end-->
        </div><!--panel body end-->
      </div><!--panel end-->
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
          <section class="side-div visible-lg">
            <li class="list-group-item color-f0ad4e col-md-12 text-center"><font class="panel-title">每三小时天气情况</font></li>
            <li class="clearfix visible-lg" >&nbsp;</li>
            <?php if(is_array($weatherHourlyForecast)): $i = 0; $__LIST__ = $weatherHourlyForecast;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="panel panel-default">
                <div class="panel-heading">
                    <font class="panel-title"><?php echo ($vo["date"]); ?></font>
                </div>
                <div class="list-group weather-hour-div" >
                  <li class="list-group-item">
                    <font>温度 ： <?php echo ($vo["tmp"]); ?> ℃</font>
                  </li>
                  <li class="list-group-item">
                    <font>湿度 ： <?php echo ($vo["hum"]); ?> ％</font>
                  </li>
                  <li class="list-group-item">
                    <font>降水 ： <?php echo ($vo["pop"]); ?> ％</font>
                  </li>
                  <li class="list-group-item">
                    <font> <?php echo ($vo["wind"]["dir"]); ?> 「  <?php echo ($vo["wind"]["sc"]); ?>  」</font>
                  </li>
                </div>
              </div><?php endforeach; endif; else: echo "" ;endif; ?>
          </section>
        </div><!--col-md-3 end -->
    </div><!--row end-->
    </div><!--cintainer end-->
<script>

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