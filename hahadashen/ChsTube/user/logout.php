<?php
session_start();
session_unset($_SESSION['nickname']);
session_unset($_SESSION['username']);
session_unset($_SESSION['level']);
session_unset($_SESSION['money']);
session_unset($_SESSION['regtime']);
session_unset($_SESSION['UID']);
session_unset($_SESSION['email']);
$_SESSION['login_type'] = 0;
header("Location:../index.php");
?>