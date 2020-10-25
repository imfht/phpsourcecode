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
    
    


<div data-role="page"  >
    <div data-role="header" data-position="fixed" data-tap-toggle="false"data-theme="l">
      <a href="#sharePanel" style="display:inline-block;margin-top:10px;"><i class="fa fa-list"></i></a>
      <h1><font class="panel-title"><i class="fa fa-map-marker"></i>
          <?php echo ($clientAddress); ?> &nbsp;&nbsp;<small >[当前城市]</small></font></h1>
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
        <div class="panel-heading">
             <h4><small>更新时间 ：<?php echo ($weatherBasic['update']['loc']); ?></small></h4>
        </div>
        <div class="well text-center">
          <img src="<?php echo ($weatherNow['cond']['icon']); ?>" title="<?php echo ($weatherNow['cond']['text']); ?>" class="weather-icon-stylie">
        </div>
        <div class="text-left ">
            <li class="list-group-item ">
              <font class="color-888"><small>实时天气 ： </small><font class="colorCC0033"><?php echo ($weatherNow['cond']['txt']); ?></font></font>
            </li>
            <li class="list-group-item list-group-item-shadow">
                <font class="color-888"><small>环境温度 ：</small><font class="colorCC0033"><?php echo ($weatherNow['tmp']); ?></font> ℃
                <font class="pull-right"><small>体感温度 ： </small><font class="colorCC0033"><?php echo ($weatherNow['fl']); ?></font> ℃</font>
                </font>
            </li> 
            <li class="list-group-item list-group-item-shadow">
              <font class="color-888"><small>风力风向 ：</small><font class="colorCC0033"><?php echo ($weatherNow['wind']['dir']); ?></font> 
                 <span class="pull-right">风力：<font class="colorCC0033"><?php echo ($weatherNow['wind']['sc']); ?></font> 级</span>
               </font>
            </li>
            <li class="list-group-item list-group-item-shadow">
              <font class="color-888">能见度： <font class="colorCC0033"><?php echo ($weatherNow['vis']); ?></font> km</font>
            </li>
            <li class="list-group-item list-group-item-shadow">
              <font class="color-888">相对湿度 ： <font class="colorCC0033"><?php echo ($weatherNow['hum']); ?></font> ％
                 <font class="pull-right">降水量 ： <font class="colorCC0033"><?php echo ($weatherNow['pcpn']); ?></font> ㎜</font>
              </font>
            </li> 
          </div>
          <div class="list-group text-left ">
                <div data-role="navbar">
                  <ul>
                    <li><a href="#">
                        <font>空气质量指数 ： <font class="colorCC0033"><?php echo ($weatherAqi['aqi']); ?></font>
                        <span class="ui-li-count pull-right"><?php echo ($weatherAqi['qlty']); ?></span>
                        </font>
                    </a></li>
                  </ul>
                </div>
                <div class="list-group panel-body">
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
       <div class="panel panel-default ">
        <div class="panel-heading">
          <font class="panel-title">生活提示</font>
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
                    <h4><small>相对湿度 ： <span class="colorCC0033"><?php echo ($vo["hum"]); ?></span> ％</small></h4>
                    <h4><small>降水概率 ： <span class="colorCC0033"><?php echo ($vo["pop"]); ?></span> ％</small></h4>
                    <h4><small>能见度 ： <span class="colorCC0033"><?php echo ($vo["vis"]); ?></span> ㎞</small></h4>
                    <h4><small>风向 ： <span class="colorCC0033"><?php echo ($vo["wind"]["dir"]); ?></span> </small> <small>「  <?php echo ($vo["wind"]["sc"]); ?>  」</small></h4>
                    <h4>
                        <small>最高温度 ： <span class="colorCC0033"><?php echo ($vo["tmp"]["max"]); ?></span> ℃</small>
                    </h4>
                    <h4>
                        <small>最低温度 ： <span class="colorCC0033"><?php echo ($vo["tmp"]["min"]); ?></span> ℃</small>
                    </h4>
                  </div>
                  <div class="col-xs-12 text-center">
                    <hr>
                    <h4 class="pull-left"><i class="fa fa-sun-o"></i> &nbsp; <strong>白天</strong></h4>
                    <h4 class="pull-left"><small> &nbsp;  &nbsp; 日出 ： <?php echo ($vo["astro"]["sr"]); ?></small></h4>
                    <img src="<?php echo ($vo["cond"]["icon_d"]); ?>" alt="<?php echo ($vo["cond['txt_d']"]); ?>" class="weather-icon-stylie">
                    <div class="list-group text-center ">
                        <li class="list-group-item  list-group-item-warning">
                          <font class="panel-title">天气状况 ： <font class="colorCC0033"><?php echo ($vo["cond"]["txt_d"]); ?></font>
                          </font>
                        </li>
                    </div>
                  </div>
                  <div class="col-md-6 col-xs-12 text-center">
                    <h4 class="pull-left"><i class="fa fa-moon-o"></i> &nbsp; <strong>夜间</strong></h4>
                    <h4 class="pull-left"><small> &nbsp;  &nbsp; 日落 ： <?php echo ($vo["astro"]["ss"]); ?></small></h4>
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
     <div class="col-md-3" >
          <section class="side-div">
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
  </div><!--content end-->


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