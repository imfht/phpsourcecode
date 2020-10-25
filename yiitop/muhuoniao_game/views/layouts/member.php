<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/self.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/self_game.css" type="text/css" media="screen" />
<?php Yii::app()->clientScript->registerCoreScript('jquery');?>

<title>918平台游戏--个人用户</title>
</head>
<body>
<div id="top">
  <div id="top_01">
    <p>您好&nbsp;,&nbsp;&nbsp;<span><?php echo Yii::app()->user->name;?></span><a href="<?php echo Yii::app()->request->baseUrl;?>/site/logout">安全退出</a></p>
  </div>
  <div id="top_02">
    <ul>
      <li><a href="/">首页</a></li>
      <li><a href="<?php echo Yii::app()->request->baseUrl; ?>/order/">充值</a></li>
    </ul>
  </div>
</div>
<div id="main">

  <div id="main_left">
    <div class="self"> 
    <img src="<?php $memberMessage=Member::model()->getMemberMessage(Yii::app()->user->id);echo 'http://918s-headimg.stor.sinaapp.com/'.$memberMessage['headimg'];?>"  width="100" height="100"/>
      <p><?php echo Yii::app()->user->name;?></p>
      </div>
<input type="hidden" value="<?php echo Yii::app()->request->url;?>" id="current_url"/>
      <div id="nav">
      <ul>
	  <?php if(Yii::app()->getController()->getAction()->id=='order'){$memberclass='a_link01';}else{$memberclass='a_link02';};?>
     <li class="a_link01"><a>会员中心</a></li>
     <li class="a_link02"><a href="<?php echo Yii::app()->request->baseUrl; ?>/member">个人资料</a></li>
      <li class="a_link02"><a href="<?php echo Yii::app()->request->baseUrl; ?>/member/updateHeadimg">头像设置</a></li>
      <li class="a_link02"><a href="<?php echo Yii::app()->request->baseUrl; ?>/member/updateData">修改资料</a></li>
      <li class="a_link01"><a>账号安全</a></li>
      <li class="a_link02"><a href="<?php echo Yii::app()->request->baseUrl; ?>/member/updatePassword">修改密码</a></li>
      <li class="a_link02"><a href="<?php echo Yii::app()->request->baseUrl; ?>/member/email">邮箱认证</a></li>
      <li class="a_link02"><a href="<?php echo Yii::app()->request->baseUrl; ?>/member/idcard">防沉迷认证</a></li>
      <li class="a_link01"><a>充值信息</a></li>
       <li class="a_link02"><a href="<?php echo Yii::app()->request->baseUrl; ?>/member/order/">充值记录</a></li>
      <!--<li class="<?php if(Yii::app()->getController()->getAction()->id=='order'){echo 'a_link01';}else{echo 'a_link02';};?>"><a href="<?php echo Yii::app()->request->baseUrl; ?>/member/order/">充值记录</a></li>-->
      </ul>
    </div>
    
  </div>
  <div id="main_right"> 
  <h1><?php echo Yii::app()->user->name;?>个人中心</h1>
<?php echo $content; ?>
  
  </div>
</div>
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
<a href="918_index.html"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/logo_bottom.png" width="80" border="0" /></a>
</div>
</div>
</div>
<script type="text/javascript">
var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F8cc15c51c6678071800fd36ffda82d58' type='text/javascript'%3E%3C/script%3E"));
</script>
</body>
</html>
