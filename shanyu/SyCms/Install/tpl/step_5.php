<?php if (!defined('SHANYU_INSTALL')) exit('Access Denied!')?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $lang['install_title'];?></title>
<link rel="stylesheet" href="css/global.css" type="text/css" />
</head>
<body>
	<?php require 'tpl/header.php';?>
	<div class="main">
		<div class="complete">
				<p style="font-family: microsoft yahei,simhei; font-size:20px; font-weight:bold; color:#FF3300"><?php echo $lang['congratulations_installation_success'];?></p>
				<p><a href="../" target="_blank"  class="btn_blue"><?php echo $lang['visit_home'];?></a><a href="../index.php/Admin" target="_blank" class="btn_blue"><?php echo $lang['enter_admin'];?></a></p>
				<p><?php echo $lang['safe_notes'];?></p>
		
		</div>
	</div>
</body>
</html>