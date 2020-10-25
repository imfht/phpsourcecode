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

<table width="100%" height="31px" border="0" cellpadding="0" cellspacing="0" class="left_topbg" id="table2">
      <tr>
        <td height="31"><div class="titlebt">网站设置	</div></td>
      </tr>
    </table>

<div class="main">
    
	<div class="form">
		<form method='post' id="form_do" name="form_do" action="/tuzicms/index.php/manage/system/modify_system">
		
		<input type="hidden" name="id" class="inp_one inp_w250" value="<?php echo ($id); ?>" />
		<dl>
			<dt> 网站名称：</dt>
			<dd>
				<input type="text" name="config_webname" class="inp_one inp_w250" value="<?php echo ($config_webname); ?>" />
			</dd>			
			<dd class="desc"></dd>
			<dd></dd>
		</dl>
			
		<dl>
			<dt> 网站标题：</dt>
			<dd>
				<input type="text" name="config_webtitle" class="inp_one inp_w250" value="<?php echo ($config_webtitle); ?>" />
				 站点title的设置
			</dd>
			
			<dd></dd>
		</dl>	
		
		<dl>
			<dt> 站点关键词：</dt>
			<dd>
				<input type="text" name="config_webkw" class="inp_one inp_w250" value="<?php echo ($config_webkw); ?>" />
			</dd>
			<dd class="desc"></dd>
			<dd></dd>
		</dl>
		<dl>
			<dt> 站点描述：</dt>
			<dd>
				<textarea name="config_cp" class="tarea_default"><?php echo ($config_cp); ?></textarea><br/>
				
			</dd>
		</dl>	
		<dl>
			<dt> 网站版权信息：</dt>
			<dd>
				<textarea name="config_powerby" class="tarea_default"><?php echo ($config_powerby); ?></textarea><br/>
				
			</dd>
		</dl>
		<dl>
			<dt> 网站备案号：</dt>
			<dd>
				<input type="text" name="config_icp" class="inp_one inp_w250" value="<?php echo ($config_icp); ?>" />
			</dd>
			<dd class="desc"></dd>
			<dd></dd>
		</dl>
		
		<dl>
			<dt>公司名称：</dt>
			<dd>
				<input type="text" name="config_name" class="inp_one inp_w250" value="<?php echo ($config_name); ?>" />
			</dd>
			<dd class="desc"></dd>
			<dd></dd>
		</dl>
		
		
		<dl>
			<dt> 公司介绍：</dt>
			<dd>
				<textarea name="config_company" class="tarea_default"><?php echo ($config_company); ?></textarea><br/>
				
			</dd>
		</dl>

		

		<dl>
			<dt>公司地址：</dt>
			<dd>
				<input type="text" name="config_address" class="inp_one inp_w250" value="<?php echo ($config_address); ?>" />
			</dd>
			<dd class="desc"></dd>
			<dd></dd>
		</dl>

		<dl>
			<dt>公司电话：</dt>
			<dd>
				<input type="text" name="config_tel" class="inp_one inp_w250" value="<?php echo ($config_tel); ?>" />
			</dd>
			<dd class="desc"></dd>
			<dd></dd>
		</dl>
		
		<dl>
			<dt>公司网址：</dt>
			<dd>
				<input type="text" name="config_weburl" class="inp_one inp_w250" value="<?php echo ($config_weburl); ?>" />
				&nbsp;注:链接不带http://
			</dd>
			<dd class="desc"></dd>
			<dd></dd>
		</dl>
		
		<dl>
			<dt>客服QQ：</dt>
			<dd>
				<input type="text" name="config_qq" class="inp_one inp_w250" value="<?php echo ($config_qq); ?>" />
			</dd>
			<dd class="desc"></dd>
			<dd></dd>
		</dl>		
		
		<dl>
			<dt>客服邮箱：</dt>
			<dd>
				<input type="text" name="config_email" class="inp_one inp_w250" value="<?php echo ($config_email); ?>" />
			</dd>
			<dd class="desc"></dd>
			<dd></dd>
		</dl>
	

		</div>
		<div class="form_b">
			<input type="submit" class="btn_blue" id="submit" value="提 交">
		</div>
	   </form>
	</div>
<div style="height:50px;"></div>
<div class="cont-ft">
            <div class="copyright">
                <div class="fl">感谢使用<a href="http://www.tuzicms.com" target="_blank">TuziCMS</a>企业网站内容管理系统</div>
                <div class="fr"><?php echo (C("setting.Version")); ?></div>
            </div>
</div>
</body>
</html>