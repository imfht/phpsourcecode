<?php
/*****************************************
 *		album_constants.php
 * 	 	-------------------
 *   	Разработка: (C) 2003 Smartor
 *   	Модификация: Гутник Игорь ( чел )
 *****************************************/

if ( !defined('IN_PHPBB') )
{
	die('Hacking attempt');
}

define('PAGE_ALBUM', -19);

define('PERSONAL_GALLERY', 0);

define('ALBUM_ANONYMOUS', -1);
define('ALBUM_GUEST', -1);

define('ALBUM_USER', 0);
define('ALBUM_ADMIN', 1);
define('ALBUM_MOD', 2);
define('ALBUM_PRIVATE', 3);

define('ALBUM_UPLOAD_PATH', 'source/album/');
define('ALBUM_CACHE_PATH', 'source/album/cache/');

define('ALBUM_TABLE', $table_prefix.'album');
define('ALBUM_CAT_TABLE', $table_prefix.'album_cat');
define('ALBUM_CONFIG_TABLE', $table_prefix.'album_config');
define('ALBUM_COMMENT_TABLE', $table_prefix.'album_comment');
define('ALBUM_RATE_TABLE', $table_prefix.'album_rate');

?>