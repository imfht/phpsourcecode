<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		{META}
		<!-- BEGIN use_tpl_css -->
		<link href="{ROOT_PATH}theme/style.css" rel="stylesheet" type="text/css" media="screen, projection"/>
		<!-- END use_tpl_css -->
		{MODULE_HEAD}
		<title>{PAGE_TITLE}</title>
		{FREE_HEAD}
	</head>
	<body>
		<div id="wrap">
 			<header><a href="{U_INDEX}"/>{MODULE_HEADER}</a></header>
			<!-- BEGIN show_ban_info -->
			<div id="index-ban">{show_ban_info.BAN_INFORMATION}</div>
			<!-- END show_ban_info -->