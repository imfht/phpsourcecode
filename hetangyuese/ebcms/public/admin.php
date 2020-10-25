<?php
session_start();
$_SESSION['login_auth'] = 1;
header("Location:./ebcms");