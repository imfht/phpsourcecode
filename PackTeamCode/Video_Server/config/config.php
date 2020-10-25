<?php
ini_set('display_errors','On');
error_reporting(E_ERROR | E_WARNING | E_PARSE);
//Version
$copyright = "PackTeam@haha_Dashen 2016 - 2018 Copyright.<br>Follow MIT License";
$version = "0.35";
$build="20180716T3";
//Admin Setting
$admin_username="admin";
$admin_password="admin";
//Redis Setting
$redis_address="127.0.0.1";
$redis_port="6379";
$redis_auth="";
//Mysql Setting
$mysql_address="127.0.0.1";
$mysql_port="3306";
$mysql_username="root";
$mysql_password="root";
$mysql_db_name="video_server";
//Dual Socket Support (Only Enable When You Have Dual Socket CPU System)
//TODO:Add to Config Table
$dual_socket_support='1';