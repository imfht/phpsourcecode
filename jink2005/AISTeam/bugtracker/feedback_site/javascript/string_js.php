<?php
/* Copyright(c) 2003-2007 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: string_js.php,v 1.5 2009/07/07 15:13:52 alex Exp $
 *
 */
// Set cache limiter to public. So when session_start(), the 
session_cache_limiter("public");

// We need to start seesion so string_function.php can get the correct language
session_start();

ini_set('include_path', ".".PATH_SEPARATOR."..".PATH_SEPARATOR."include".PATH_SEPARATOR."../include".PATH_SEPARATOR.ini_get('include_path'));
include("../include/db.php");
include("../include/misc.php");
include("../include/string_function.php");

echo 'var STRING = new Array();';
foreach($STRING as $key=>$value) {
        echo "STRING['".$key."']='".addslashes($value)."';\n";
}
?>
