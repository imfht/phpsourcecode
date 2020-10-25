<?php
include("../config/config.php");
include("../include/function.php");
exec("chcp 936");
sleep(1);
start:
system('title Video Encode System Install');
Col_echo("Install Video Encode System\nVersion:" . $version . "\nBuild:" . $build . "\nPress ENTER continue......\n", 'light_blue');
trim(fgets(STDIN));
Col_echo("Connecting Mysql......", 'yellow');
$db_link = mysqli_connect($mysql_address, $mysql_username, $mysql_password, '', $mysql_port);
if (!$db_link) {
    Col_echo("[Error]\n", 'light_red');
    exit;
} else {
    Col_echo("[Success]\n", 'light_green');
}
Col_echo("Create Database......", 'yellow');
if (mysqli_query($db_link, "CREATE DATABASE " . $mysql_db_name . " DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci")) {
    Col_echo("[Success]\n", 'light_green');
} else {
    Col_echo("[Error]\n", 'light_red');
    exit;
}
mysqli_query($db_link,"USE `video_server`;");
//Screenshot Table
Col_echo("Create [ScreenShot]     ",'yellow');
if (mysqli_query($db_link,"CREATE TABLE `screenshot` (`ID` int(11) NOT NULL AUTO_INCREMENT,`video_id` int(11) NOT NULL,`type` int(11) NOT NULL COMMENT '1=JPEG 2=GIF',`files` text NOT NULL COMMENT 'file JSON',PRIMARY KEY (`ID`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;")){
    Col_echo("[Success]\n", 'light_green');
} else {
    Col_echo("[Error]\n", 'light_red');
    exit;
}
//Setting Table
if (mysqli_query($db_link,"CREATE TABLE `setting` (`ID` int(11) NOT NULL AUTO_INCREMENT,`name` text NOT NULL,`data` text NOT NULL,PRIMARY KEY (`ID`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;")){
    Col_echo("[Success]\n", 'light_green');
} else {
    Col_echo("[Error]\n", 'light_red');
    exit;
}
//VideoList Table
if (mysqli_query($db_link,"CREATE TABLE `video_list` (`ID` int(11) NOT NULL AUTO_INCREMENT,`filename` text NOT NULL,`random` text NOT NULL,`day` int(11) NOT NULL,`time` int(11) NOT NULL,`status` int(11) NOT NULL,`md5` text NOT NULL,PRIMARY KEY (`ID`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;")){
    Col_echo("[Success]\n", 'light_green');
} else {
    Col_echo("[Error]\n", 'light_red');
    exit;
}
//Done!
Col_echo("Install Successful!\n",'light_green');


