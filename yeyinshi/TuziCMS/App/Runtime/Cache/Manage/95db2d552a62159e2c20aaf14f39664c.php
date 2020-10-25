<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo ($sitename); ?> - <?php echo (C("setting.Copyright")); ?> <?php echo (C("setting.Version")); ?> <?php echo (C("setting.Code")); ?></title>
<script language="javascript" type="text/javascript" src="/tuzicms/App/Manage/View/Default/js/jquery.js"></script>
<script src="/tuzicms/App/Manage/View/Default/js/frame.js" language="javascript" type="text/javascript"></script>
<link href="/tuzicms/App/Manage/View/Default/css/style.css" rel="stylesheet" type="text/css" />

<!--[if IE 6]>
<script src="/tuzicms/App/Manage/View/Default/Js/DD_belatedPNG.js" language="javascript" type="text/javascript"></script>
<script>
  DD_belatedPNG.fix('.nav ul li a,.top_link ul li,background');   /* string argument can be any CSS selector */
</script>
<![endif]-->
</head>
<body class="showmenu">
<link href="/tuzicms/App/Manage/View/Default/css/pintuer.css" rel="stylesheet" type="text/css" />
<script src="/tuzicms/App/Manage/View/Default/js/pintuer.js"></script>
<script src="/tuzicms/App/Manage/View/Default/js/jquery.js"></script>	
<style type="text/css">
	html{_overflow-y:scroll}
</style>
<div class="right_style">

<div class="column">
	<dl class="dbox winbg1" id="item3">
	    <dt class="lside">
	        <div class="l">我的个人信息</div>
	    </dt>
	    <dd>
			<div class="content">
				您好，<?php echo ($v["admin_name"]); ?><br/>
				<div class="clear"></div>
				上次登录时间：<?php echo (date('Y-m-d H:i:s',$v["admin_olddate"])); ?><br/>
				上次登录IP：<?php echo ($v["admin_oldip"]); ?><br/> 登录次数：<?php echo ($v["admin_login"]); ?> <br/>
			</div>
	    </dd>
	</dl>

	<dl class="dbox winbg1" id="item5">
	    <dt class="lside"><span class="l">站点统计</span></dt>
	    <dd>
			<div class="content">
			会员：<?php echo ($countUser); ?> <br>
			内容：<?php echo ($countNews); ?> <br>
			留言本：<?php echo ($countGuestbook); ?> <br>
			广告：<?php echo ($countAdvert); ?> <br>
			公告：<?php echo ($countNotice); ?> <br>
			数据库：<?php echo ($total); ?><br>
			
			</div>
	    </dd>
	</dl>

	<dl class="dbox winbg2" id="item1">
	    <dt class="lside"><span class="l">系统信息</span></dt>
	    <dd>
	        <div class="content">
	程序版本：TuziCMS<?php echo (C("setting.Version")); ?> [<?php echo (C("setting.License")); ?>] <br />
	授权编号：<?php echo (C("setting.Number")); ?> <br />
	操作系统：<?php echo PHP_OS; ?><br />
	php版本：<?php echo "PHP".PHP_VERSION; ?><br />
	MySQL 版本：<?php echo mysql_get_server_info(); ?><br />
	服务器时间：<?php echo $showtime=date("Y-m-d H:i:s");?><br />

          </div>
	    </dd>
	</dl>
</div>

<div class="column_right">

	<dl class="dbox winbg5" id="item1">
    <dt class="lside"><span class="l">TuziCMS安全提示</span></dt>
    <dd>
      <div id="safelist" class="content">
		1.为了保证软件系统正常运行，请使用正版授权的系统！<br/>
		2.为了软件系统安全起见，建议安装后删除install目录，更多问题可以查看官网论坛！<br/>
      </div>
    </dd>
  </dl>
 <dl class="dbox winbg5" id="item2">
	    <dt class="lside"><span class="l">TuziCMS授权</span></dt>
	    <dd>
	        <div class="content">
			如果您已购买TuziCMS产品商业使用授权，您可以在授权中心查询到相关商业授权信息。
			<br>没有查询到授权信息，请购买授权，支持正版软件。
我们的联系方式: QQ:176881336<br/>
			<div style="margin-top:4px;">
			<a href="http://www.tuzicms.com/index.php/license" target="_blank" style="padding:4px; background:#06A21E; color:white;">购买授权</a>
			<a href="http://www.tuzicms.com/index.php/license" target="_blank" style="padding:4px; background:#06A21E; color:white">商业授权查询</a>
			</div>
			</div>
	    </dd>
	</dl>
	<dl class="dbox winbg7" id="item2">
		<dt class="lside"><span class="l">TuziCMS介绍</span></dt>
		<dd>
			<div id="TuziCMS_News" class="content">
				基于ThinkPHP框架开发的企业网站管理系统，国内PHP+MYSQL开源建站程序<br>
				它具有操作简单、功能强大、稳定性好，二次开发及后期维护方便。
		  </div>
		</dd>
	</dl>
	
	<dl class="dbox winbg5" id="item2">
	    <dt class="lside"><span class="l">TuziCMS系统开发团队</span></dt>
	    <dd>
	        	        <div class="content">
			版权所有：<a href="http://www.yejiao.net" target="_blank">椰角网络</a><br />
			官方网站：<a href="http://www.yejiao.net" target="_blank">http://www.Yejiao.net</a><br />
			TuziCMS官方：<a href="http://www.TuziCMS.com/" target="_blank">http://www.tuzicms.com/</a> <br />
			官方QQ群：<a href="http://jq.qq.com/?_wv=1027&k=fHc7iL" target="_blank">383851010</a> <br />
			</div>
	    </dd>
	</dl>

</div>
</div>
<div style="height:50px;"></div>
<div class="cont-ft">
            <div class="copyright">
                <div class="fl">感谢使用<a href="http://www.tuzicms.com" target="_blank">TuziCMS</a>企业网站内容管理系统</div>
                <div class="fr"><?php echo (C("setting.Version")); ?></div>
            </div>
</div>
</body>