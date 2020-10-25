<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><
!DOCTYPE html><html lang="en">
	<head>
		<meta charset="utf-8">
		<title>PKADMIN-小刀科技</title>

		<style type="text/css">::selection {
	background-color: #E13300;
	color: white;
}

::-moz-selection {
	background-color: #E13300;
	color: white;
}

body {
	background-color: #fff;
	margin: 40px;
	font: 13px/20px normal Helvetica, Arial, sans-serif;
	color: #4F5155;
}

a {
	color: #003399;
	background-color: transparent;
	font-weight: normal;
}

h1 {
	color: #444;
	background-color: transparent;
	border-bottom: 1px solid #D0D0D0;
	font-size: 19px;
	font-weight: normal;
	margin: 0 0 14px 0;
	padding: 14px 15px 10px 15px;
}

code {
	font-family: Consolas, Monaco, Courier New, Courier, monospace;
	font-size: 12px;
	background-color: #f9f9f9;
	border: 1px solid #D0D0D0;
	color: #002166;
	display: block;
	margin: 14px 0 14px 0;
	padding: 12px 10px 12px 10px;
}

#body {
	margin: 0 15px 0 15px;
}

p.footer {
	text-align: right;
	font-size: 11px;
	border-top: 1px solid #D0D0D0;
	line-height: 32px;
	padding: 0 10px 0 10px;
	margin: 20px 0 0 0;
}

#container {
	margin: 10px;
	border: 1px solid #D0D0D0;
	box-shadow: 0 0 8px #D0D0D0;
}</style>
	</head>
	<body>

		<div id="container">
			<h1>Welcome to PKADMIN!</h1>

			<div id="body">
				<p>
					这是前台页面，如果你的系统存在前台，可以按照这个模式进行开发！
				</p>

				<p>
					如果你想编辑这个页面,可以按照这个路径进行查找：
				</p>
				<code>
				App/views/Home/home.php</code>

				<p>
					你可以找到这个页面的控制器位于：
				</p>
				<code>
				application/controllers/Home/Pkhome.php</code>

				<p>
					Thank you for using PKADMIN System
				</p>
			</div>

			<p class="footer">
				PKADMIN VERSION 1.0.0
			</p>
		</div>

	</body>
</html>