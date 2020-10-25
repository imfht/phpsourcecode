<?php
require '../conn.php';
require("../Appcode/conn.php");
require("clsAdmin_Info.php");
require 'global.func.php';

$secretkey = 'admin_'.strtolower(substr(DT_KEY, -6));
set_cookie($secretkey, '');

set_cookie('auth', '');
set_cookie('userid', '');
msg('已经安全退出网站后台管理', '/admin/ad_login.php');