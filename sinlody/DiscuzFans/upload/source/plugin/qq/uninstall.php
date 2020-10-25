<?php

/**
 * 维清 [ Discuz!应用专家，深圳市维清互联科技有限公司旗下Discuz!开发团队 ]
 *
 * Copyright (c) 2011-2099 http://www.wikin.cn All rights reserved.
 *
 * Author: wikin <wikin@wikin.cn>
 *
 * $Id: uninstall.php 2015-5-13 15:24:06Z $
 */
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = <<<EOF
DROP TABLE IF EXISTS `pre_qq_member`;
DROP TABLE IF EXISTS `pre_qq_member_guest`;
DROP TABLE IF EXISTS `pre_qq_member_bindlog`;
EOF;

runquery($sql);

$finish = TRUE;
?>