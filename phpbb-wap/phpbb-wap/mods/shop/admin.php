<?php

$template->set_filenames(array(
	'body' => 'shop_admin.tpl')
);

$template->assign_vars(array(
	'U_ADMIN_MODS'		=> append_sid('admin_mods.php'),
	'U_ADMIN_CONFIG'	=> append_sid('admin_mods.php?mode=admin&mods=shop&load=config'),
	'U_ADMIN_QQ'		=> append_sid('admin_mods.php?mode=admin&mods=shop&load=qq'),
	'U_ADMIN_AD'		=> append_sid('admin_mods.php?mode=admin&mods=shop&load=ad'),
	'U_ADMIN_GOOD'		=> append_sid('admin_mods.php?mode=admin&mods=shop&load=good'))
);

$template->pparse('body');
?>