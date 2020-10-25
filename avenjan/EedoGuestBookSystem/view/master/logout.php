<?php
header("Content-type: text/html; charset=utf-8");
 setcookie('username', '', time()-3600, '/', '',0,0);
 setcookie('u_name', '', time()-3600, '/', '',0,0);
 echo "OK";
?>