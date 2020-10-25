<?php

session_start();

define('SYSTEM_ROOT', 'X');
require 'ixlab_adminer_desc.php';
require '../../config.php';

if ($_SESSION['ixnet_adminer_auth']) {
    $_GET['server'] = DB_HOST;
    $_GET['username'] = DB_USER;
    $_GET['db'] = DB_NAME;
    include IXLAB_ADMINER_FILE;
} else {
    exit('Invalid Session');
}