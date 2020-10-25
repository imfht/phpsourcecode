{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

<html>
	<head>
		<title>{l s='Invalid security token'}</title>
	</head>
	<body style="font-family:Arial,Verdana,Helvetica,sans-serif;background-color:#EC8686">
		<div style="background-color:#FAE2E3;border:1px solid #000000;color:#383838;font-weight:700;line-height:20px;margin:0 0 10px;padding:10px 15px;width:500px">
			<img src="../img/admin/error2.png" style="margin:-4px 5px 0 0;vertical-align:middle">
			{l s='Invalid security token'}
		</div>
		<a href="{$url}" method="get" style="float:left;background: #E3E3E3;border-color: #CCCCCC #BBBBBB #A0A0A0;border-left: 1px solid #BBBBBB;border-radius: 3px 3px 3px 3px;border-right: 1px solid #BBBBBB;border-style: solid;border-width: 1px;color: #000000;margin: 20px 10px;padding:10px;text-align:center;vertical-align:middle;">
			{l s='I understand the risks and I really want to display this page'}
		</a>
		<a href="index.php" method="get" style="float:left;background: #E3E3E3;border-color: #CCCCCC #BBBBBB #A0A0A0;border-left: 1px solid #BBBBBB;border-radius: 3px 3px 3px 3px;border-right: 1px solid #BBBBBB;border-style: solid;border-width: 1px;color: #000000;margin: 20px 10px;padding:10px;text-align:center;vertical-align:middle;">
			{l s='Take me out of here!'}
		</a>
	</body>
</html>