<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="<?php echo base_url();?>Public/admin_style/css/admin.css" type="text/css"/>
<title><?php echo $web->webname;?>--后台管理</title>
</head>

<frameset rows="127,*,11" frameborder="no" border="0" framespacing="0">
  <frame src="<?php echo site_url('admin/top');?>" name="topFrame" scrolling="No" noresize="noresize" id="topFrame" />
  <frame src="<?php echo site_url('admin/center');?>" name="mainFrame" id="mainFrame" />
  <frame src="<?php echo site_url('admin/down');?>" name="bottomFrame" scrolling="No" noresize="noresize" id="bottomFrame" />
</frameset>
<noframes>
<body>
</body>
</noframes>
</html>
