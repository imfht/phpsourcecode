<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function module_reinstall_blockmyaccount()
{
	$res = true;
	$id_module = Db::getInstance()->getValue('SELECT id_module FROM '._DB_PREFIX_.'module where name="blockmyaccount"');
	if ($id_module)
	{
		$res &= Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'hook` 
			(`name`, `title`, `description`, `position`) VALUES 
			("displayMyAccountBlock", "My account block", "Display extra informations inside the \"my account\" block", 1)');
		// register left column, and header, and addmyaccountblockhook
		$hooks = array('leftColumn', 'header');
		foreach($hooks as $hook_name)
		{
			// do not pSql hook_name 
			$row = Db::getInstance()->getRow('SELECT h.id_hook, '.$id_module.' as id_module, MAX(hm.position)+1 as position
				FROM  `'._DB_PREFIX_.'hook_module` hm
				LEFT JOIN `'._DB_PREFIX_.'hook` h on hm.id_hook=h.id_hook
				WHERE h.name = "'.$hook_name.'" group by id_hook');
			$res &= Db::getInstance()->insert('hook_module', $row);
		}
		return $res;
	}
	return true;
}


