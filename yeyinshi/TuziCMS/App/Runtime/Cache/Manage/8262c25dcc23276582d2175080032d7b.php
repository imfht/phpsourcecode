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
        <td height="31"><div class="titlebt"><?php echo ($v["column_name"]); ?></div></td>
      </tr>
    </table>

<div class="main">
    
	<div class="form">
		<form method='post' id="form_do" name="form_do" action="<?php echo U('Page/do_edit');?>">
		
		<dl>
			<dt> 摘要：</dt>
			<dd>
				<textarea name="column_descr" class="tarea_default" ><?php echo ($v["column_descr"]); ?></textarea>
			</dd>
		</dl>
		
				
		<!--载入kindeditor编辑器开始-->
		<script type="text/javascript" charset="utf-8" src="/tuzicms/Data/kindeditor/kindeditor.js"></script>
		<script charset="utf-8" src="/tuzicms/Data/kindeditor/lang/zh_CN.js"></script>
		<script language="javascript">
		var editor;
		KindEditor.ready(function(K) {
		editor = K.create('#intro');
		// editor = K.create('#editor_id');多个
		});
		</script>
		<!--<textarea id="editor_id" name="content" style="width:280px;height:160px;"></textarea>-->
		<!--载入kindeditor编辑器结束-->
		<dl>
			<dt> 内容：</dt>
			<dd>
				
		<div>
		<textarea id="intro" name="column_content" style="width:700px;height:400px;"/><?php echo ($v["column_content"]); ?></textarea>
		</div>
				
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