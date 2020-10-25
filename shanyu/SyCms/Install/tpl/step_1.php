<?php if (!defined('SHANYU_INSTALL')) exit('Access Denied!')?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $lang['install_license'];?>-<?php echo $lang['install_title'];?></title>

<link rel="stylesheet" href="css/global.css" type="text/css" />
</head>
<body>
	<?php require 'tpl/header.php';?>
	<div class="main">
		<div class="license">
		<?php echo format_textarea($license)?>			
		</div>
		<div class="action"><a href="index.php?step=2" class="btn_blue"><?php echo $lang['agree_and_accept'];?></a></div>
	</div>
</body>
</html>