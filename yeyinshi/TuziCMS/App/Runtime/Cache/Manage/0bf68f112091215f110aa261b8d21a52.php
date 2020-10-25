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
        <td height="31"><div class="titlebt">电脑版缓存</div></td>
      </tr>
    </table>

<div class="main">
    
	<div class="form">
		<form method='post' id="form_do" name="form_do" action="/tuzicms/index.php/manage/system/do_pcruntime" >
		
		<dl>
			<dt>PC端静态缓存：</dt>
			<dd>
				<input type="hidden" value="<?php echo (C("HTML_CACHE_ON__HOME")); ?>" />

				<input type="radio" name='HTML_CACHE_ON__HOME' value='true'  <?php if(C('HTML_CACHE_ON__HOME') == 1): ?>checked="checked"<?php endif; ?> />开启电脑版缓存
				<input type="radio" name='HTML_CACHE_ON__HOME' value='false'  <?php if(C('HTML_CACHE_ON__HOME') == 0): ?>checked="checked"<?php endif; ?> />关闭电脑版缓存

			</dd>		
			<dd class="desc"></dd>
			<dd></dd>
		</dl>		
		
		<dl>
			<dt> 缓存规则：</dt>
			<dd>
			<div>缓存时间(秒)：0为永久缓存</div>					
			</dd>			
			<dd class="desc"></dd>
			<dd></dd>
		</dl>
		
		
		<dl>
			<dt></dt>
			<dd>
				首页缓存时间：<input type="text" name="HTML_TIME_INDEX__HOME" class="inp_one inp_w250" value="<?php echo (C("HTML_TIME_INDEX__HOME")); ?>" />
			</dd>

		</dl>
		
		<dl>
			<dt></dt>
			<dd>
				栏目缓存时间：<input type="text" name="HTML_TIME_GROUP__HOME" class="inp_one inp_w250" value="<?php echo (C("HTML_TIME_GROUP__HOME")); ?>" />
			</dd>

		</dl>
		
		<dl>
			<dt></dt>
			<dd>
				文章缓存时间：<input type="text" name="HTML_TIME_DETAIL__HOME" class="inp_one inp_w250" value="<?php echo (C("HTML_TIME_DETAIL__HOME")); ?>" />
			</dd>

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