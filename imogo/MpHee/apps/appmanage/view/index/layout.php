<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>应用管理</title>
<meta content="IE=8" http-equiv="X-UA-Compatible" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="__PUBLIC__/css/base.css" />
<link rel="stylesheet" type="text/css" href="__PUBLIC__/css/font-awesome.css">
<script type="text/javascript" src="__PUBLIC__/js/core/jquery.js"></script>
</head>
<body>
<div id="contain">
  <div class="admin_title">
	<a href="{url('index/index')}" class="button w100"><i class="fa fa-list-ul"></i> 应用列表</a>
	<a href="{url('index/import')}" class="button w100"><i class="fa fa-download"></i> 导入应用</a>
	<a href="{url('index/create')}" class="button w100"><i class="fa fa-plus"></i> 创建应用</a> 
  </div>
  {include file="$__template_file"}
</div>
</body>
</html>
