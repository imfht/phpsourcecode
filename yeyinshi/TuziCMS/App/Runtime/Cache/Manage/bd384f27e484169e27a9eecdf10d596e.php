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
        <td height="31"><div class="titlebt">修改栏目广告</div></td>
      </tr>
    </table>

<div class="main">
    
	<div class="form">
		<form method='post' id="form_do" name="form_do" action="/tuzicms/index.php/manage/banner/upload" enctype="multipart/form-data">
		<dl>
			<dt> 栏目名称：</dt>
			<dt>
			<div style="width:80px; background:#079B04; color:#FFFFFF" align="center"><?php echo ($v["column_name"]); ?></div>
				
			</dt>
		</dl>
			
		<dl>
			<dd></dd>
		</dl>
		
		<dl>
			<dt> 图片附件：</dt>
			<dd>
				<input name="column_images" id="image" type="file" style="height:30px; margin-top:-18px;"/>
				<img src="/tuzicms/Uploads/<?php echo ($v["column_images"]); ?>" width="180px" height="50px" border="0"/>
			</dd>
		</dl>		
			
		
		<div class="form_b">
			<input type="hidden" name="id" value="<?php echo ($v["id"]); ?>" />	
			<input type="submit" class="btn_blue" id="submit" value="提 交">
		</div>
	   </form>
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
</html>