<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/css.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/style.css" type="text/css" media="screen" />

<title>918平台游戏--home</title>

</head>
<body>
<div id="header">
  <div id="main_header">
    <div id="nav"> <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/logo.jpg" style="float:left;" />
      <ul>
        <li style="padding-left:40px;"><a href="<?php echo Yii::app()->request->baseUrl; ?>/" class="home"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/home.jpg" /></a></li>
        <li><a href="<?php echo Yii::app()->request->baseUrl; ?>/member/"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/buluo.jpg" /></a></li>
        <li><a href="<?php echo Yii::app()->request->baseUrl; ?>/order/"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/chongzhi.jpg" /></a></li>
      </ul>
    </div>
  </div>
</div>

	<?php echo $content; ?>

<div id="fotter"> 
<div id="fotter_main">
<div id="fotter_left">
Copyright©2005-2012 918.CN All Rights Reserved 918网页游戏中心
<br /> -京ICP证080047号- 文网文[2009]024 
</div>
<div id="fotter_mid">
<a href="#">游戏商务合作</a>
<a href="#">游戏组件未成年人家长监护工程</a>
<a href="#">918试玩中心</a>
</div>
<div id="fotter_right">
<a href="#"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/logo_bottom.png"/></a>
</div>
</div>
</div>
<script type="text/javascript">
var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F8cc15c51c6678071800fd36ffda82d58' type='text/javascript'%3E%3C/script%3E"));
</script>
</body>
</html>