<?php 
/*
 * This file is part of the emlog for bootstrap Project. See CREDITS and LICENSE files
 *
 * emlog for bootstrap Project URL:https://git.oschina.net/ysfl/emlog-for-bootstrap
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
/**
 * 自定义404页面
 */
if(!defined('EMLOG_ROOT')) {exit('error!');}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>错误提示-页面未找到</title>
<style type="text/css">
<!--
body {
	background-color:#F7F7F7;
	font-family: Arial;
	font-size: 12px;
	line-height:150%;
}
.main {
	background-color:#FFFFFF;
	font-size: 12px;
	color: #666666;
	width:650px;
	margin:60px auto 0px;
	border-radius: 10px;
	padding:30px 10px;
	list-style:none;
	border:#DFDFDF 1px solid;
}
.main p {
	line-height: 18px;
	margin: 5px 20px;
}
-->
</style>
</head>
<body>
<div class="main">
<p>抱歉，你所请求的页面不存在！</p>
<p><a href="javascript:history.back(-1);">&laquo;点击返回</a></p>
</div>
</body>
</html>