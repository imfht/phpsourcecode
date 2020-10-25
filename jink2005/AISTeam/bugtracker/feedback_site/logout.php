<?php
/* Copyright (c) 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: logout.php,v 1.3 2008/11/28 10:36:09 alex Exp $
 *
 */
include("include/header.php");

unset($_SESSION[SESSION_PREFIX.'feedback_email']);
unset($_SESSION[SESSION_PREFIX.'feedback_uid']);
unset($_SESSION[SESSION_PREFIX.'feedback_customer']);
echo "<h2 align=center><a href=index.php>Back to Index</a></h2>"; 
echo "<script>";
echo "location.href = \"index.php\";";
echo "</script>";
include("include/tail.php");
?>