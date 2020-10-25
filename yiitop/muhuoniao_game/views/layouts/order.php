<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/css.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/918_chongzhi.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.min.js"></script>
<script type="text/javascript">
var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F8cc15c51c6678071800fd36ffda82d58' type='text/javascript'%3E%3C/script%3E"));
</script>

<title>918平台游戏--支付宝充值</title>
</head>
<body>
<!-----------------头部---------------------------->
<div id="header">
  <div id="main_header">
    <div id="nav"> <img src="<?php echo Yii::app()->request->baseUrl;?>/images/logo.jpg" style="float:left;" />
            <ul>
        <li style="padding-left:40px;"><a href="<?php echo Yii::app()->request->baseUrl; ?>/" ><img src="<?php echo Yii::app()->request->baseUrl;?>/images/home.jpg" /></a></li>
        <li><a href="<?php echo Yii::app()->request->baseUrl; ?>/member/"><img src="<?php echo Yii::app()->request->baseUrl;?>/images/buluo.jpg" /></a></li>
        <li><a href="<?php echo Yii::app()->request->baseUrl; ?>/order/" class="home"><img src="<?php echo Yii::app()->request->baseUrl;?>/images/chongzhi.jpg" /></a></li>
      </ul>
    </div>
  </div>
</div>
<!-----------------头部 end---------------------------->
<div id="container">

<div id="main">
  <h2>当前帐号：<a href="<?php echo Yii::app()->request->baseUrl; ?>/member/"><?php echo Yii::app()->user->name;?></a></h2>
  <div id="main_left"> 
  <ul>
  <li class="a_link02"><a href="918_recharge.html">网上银行</a></li>
  <li class="a_link01"><a href="918_zhifubao.html">支付宝</a></li>
  </ul>
  </div>

<!-----------------游戏充值---------------------------->
<?php echo $content; ?>

<!-----------------游戏充值 end---------------------------->
</div>
</div>
<!-----------------fotter---------------------------->
<div id="fotter">
  <div id="fotter_main">
    <div id="fotter_left"> Copyright©2005-2012 918.CN All Rights Reserved 918网页游戏中心 <br />
      -京ICP证080047号- 文网文[2009]024 </div>
    <div id="fotter_mid"> <a href="#">游戏商务合作</a> <a href="#">游戏组件未成年人家长监护工程</a> <a href="#">918试玩中心</a> </div>
    <div id="fotter_right"> <a href="918_index.html"><img src="<?php echo Yii::app()->request->baseUrl;?>/images/logo_bottom.png" width="80" /></a> </div>
  </div>
</div>
<!-----------------fotter end---------------------------->
</body>
</html> 
