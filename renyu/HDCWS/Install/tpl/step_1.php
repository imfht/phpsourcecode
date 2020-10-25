<?php if (!defined('INSTALL_STATUS')) exit('Access Denied!')?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $lang['install_license'];?>-HDCWS安装向导</title>

<link rel="stylesheet" href="css/global.css" type="text/css" />
</head>
<body>
	<?php require 'tpl/header.php';?>
	<div class="main">
		<div class="license">
		<?php echo format_textarea($license)?>			
		</div>
		<div class="action"><a href="index.php?step=2" class="btn_blue">同意并接受</a></div>
	</div>
</body>
</html>