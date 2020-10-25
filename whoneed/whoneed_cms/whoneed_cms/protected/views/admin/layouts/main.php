<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<title>whoneed_cms管理后台</title>

<link href="/admin/js/dwz/themes/default/style.css" rel="stylesheet" type="text/css" media="screen"/>
<link href="/admin/js/dwz/themes/css/core.css" rel="stylesheet" type="text/css" media="screen"/>
<link href="/admin/js/dwz/themes/css/print.css" rel="stylesheet" type="text/css" media="print"/>
<link href="/admin/js/dwz/uploadify/css/uploadify.css" rel="stylesheet" type="text/css" media="screen"/>
<!--[if IE]>
<link href="/admin/js/dwz/themes/css/ieHack.css" rel="stylesheet" type="text/css" media="screen"/>
<![endif]-->

<script src="/admin/js/dwz/js/speedup.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/jquery-1.7.1.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/jquery.cookie.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/jquery.validate.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/jquery.bgiframe.js" type="text/javascript"></script>
<!-- <script src="/admin/js/dwz/xheditor/xheditor-1.1.12-zh-cn.min.js" type="text/javascript"></script>-->
<script src="/admin/js/xheditor-1.1.14/xheditor-1.1.14-zh-cn.min.js" type="text/javascript"></script>
<script src="/admin/js/dwz/uploadify/scripts/swfobject.js" type="text/javascript"></script>
<script src="/admin/js/dwz/uploadify/scripts/jquery.uploadify.v2.1.0.js" type="text/javascript"></script>

<script src="/admin/js/dwz/js/dwz.core.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/dwz.util.date.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/dwz.validate.method.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/dwz.regional.zh.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/dwz.barDrag.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/dwz.drag.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/dwz.tree.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/dwz.accordion.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/dwz.ui.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/dwz.theme.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/dwz.switchEnv.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/dwz.alertMsg.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/dwz.contextmenu.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/dwz.navTab.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/dwz.tab.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/dwz.resize.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/dwz.dialog.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/dwz.dialogDrag.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/dwz.sortDrag.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/dwz.cssTable.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/dwz.stable.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/dwz.taskBar.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/dwz.ajax.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/dwz.pagination.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/dwz.database.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/dwz.datepicker.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/dwz.effects.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/dwz.panel.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/dwz.checkbox.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/dwz.history.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/dwz.combox.js" type="text/javascript"></script>
<script src="/admin/js/dwz/js/dwz.print.js" type="text/javascript"></script>
<!--
<script src="/admin/js/dwz/bin/dwz.min.js" type="text/javascript"></script>
-->
<script src="/admin/js/dwz/js/dwz.regional.zh.js" type="text/javascript"></script>

<!--
<script type="text/javascript" src="/admin/ckeditor/ckeditor.js"></script>
-->

</head>
<body>
<?php echo $content;?>
<!--遮盖屏幕-->
</body>
<script type="text/javascript"> 
$(function()
{
	DWZ.init("/admin/js/dwz/dwz.frag.xml", { debug:false, /*调试模式*/ callback:function(){ initEnv();$("#themeList").theme({themeBase:"/admin/js/dwz/themes/"}); } }); }); 
	/* 清理浏览器内存,只对IE起效,FF不需要 */ 
	if ($.browser.msie) { window.setInterval("CollectGarbage();", 10000); 
}
</script>
</html>
