<?php
namespace test;

use WxSDK\core\model\mass\Mass;
use WxSDK\core\module\MassKit;
use WxSDK\WxApp;

include_once './com.php';

$accessToken = new WxApp();

// $article = new Article("测试图文","RRI5T1s9iMds03CYAeoHQVBPf2fAMBThtaMttpf3EQ-L8yJLP8BoXs2i5xDzQmRC","这是一篇不带图片的测试图文","http://www.51xlxy.com");
// $news = new News($article);
// $ret = UpKit::uploadNews4Mass($accessToken,$news);
//media_id:   dZpeMOyMusxS0BqQlaQmGws9wLM0iL29s4nHl6kFaqLEpZpE2NKUxzTztDEWHMD1

$filter = Mass::createFilter(FALSE, NULL);
$mass = Mass::createMpnewsMass("rBfqMHvB3ajj7Vvo5UL_kWqljLUICgJLIWczBKAE3cA",$filter);
$ret = MassKit::sendMassByTag($accessToken, $mass);

// ////测试号没有群发权限的！这是一个历史悠久的腾讯官方坑！
// //$mass = Mass::createMpnewsMass("rBfqMHvB3ajj7Vvo5UL_kaNJ1TycSecUvnxkkkGp-Fw",$filter);
// $ret = MassKit::sendMassByTag($accessToken, $mass);


print_r(json_encode($ret,JSON_UNESCAPED_UNICODE));
