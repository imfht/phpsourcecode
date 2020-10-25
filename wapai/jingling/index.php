<?php    
header("Location: http://". $_SERVER['SERVER_NAME'] . rtrim(dirname(rtrim($_SERVER['SCRIPT_NAME'], '/')), '/').'/public/index.php' );exit();
?>