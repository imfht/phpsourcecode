<?php
/*
*	Package:		PHPCrazy.QQConnect
*	Link:			http://git.oschina.net/Crazy-code/PHPCrazy.QQConnect
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/

$sqlList = <<<SQL
INSERT INTO crazy_config (config_name, config_value) VALUES('qqc_appid', '');
INSERT INTO crazy_config (config_name, config_value) VALUES('qqc_appkey', '');
INSERT INTO crazy_config (config_name, config_value) VALUES('qqc_scope', 'get_user_info');
ALTER TABLE crazy_users ADD qq_openid VARCHAR(64) DEFAULT '';
SQL;

LoadFunc('sql');

RunSQL($sqlList, $PDO);

LoadFunc('admin');

header('Location: '.AdminUrl('qqconnect'));

unlink(__FILE__);

AppEnd();