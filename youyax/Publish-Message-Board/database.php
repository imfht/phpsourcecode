<?php
@session_start();
ini_set('session.cookie_path', '/');
$con=mysql_connect("10.4.12.173","uPDE25DhWpcyo","pQ1losEDOzeuS");
mysql_select_db("d5c5398c1a37842f89b2a03c418fe16a9",$con);
mysql_query("set sql_mode=''");
mysql_query("set names utf8");
?>