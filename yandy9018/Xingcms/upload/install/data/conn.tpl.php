<?php
$db_config['DB_HOST'] = '~db_host~'; //数据库地址
$db_config['DB_USER'] = '~db_user~'; //mysql帐号
$db_config['DB_PASS'] = '~db_pwd~'; //mysql密码
$db_config['DB_NAME'] = '~db_name~'; //数据库名称
$db_config['DB_CHARSET'] = 'utf8';  //数据库编码
$db_config['DB_ERROR'] = true;
$db_config['TB_PREFIX'] = '~db_tablepre~';//数据表前缀
define('CHARSET', '~db_charset~'); //文件编码
define('TIMEZONE', '-8'); //时区设置
define('INC_DIR', 'include/'); //包含目录
define('TPL_DIR', 'templates/'); //模板目录
define('HTML_DIR', ''); //静态文件目录
define('CACHETIME',3600); //缓存时间
define('COOKIETIME',3600); //缓存时间
define('ADMIN_PAGE', 'adm.php'); //后
?>