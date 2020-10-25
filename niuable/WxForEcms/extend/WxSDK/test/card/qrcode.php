<?php
namespace test\card;

use WxSDK\WxApp;
use WxSDK\core\model\card\send\SendCard;
use WxSDK\core\module\CardKit;

define("__DIR_", dirname(dirname(dirname(__FILE__))));

include '../Loader.php'; // 自动加载类

$accessToken = new WxApp();
$sendCard = new SendCard();
$sendCard->card_id = "pUIjzjvWIeWKPl7PnTgCr2Kbz29o";
$sendCard->outer_str="test";

$ret = CardKit::createQrcode4GetCard($accessToken,$sendCard);
echo json_encode($ret,JSON_UNESCAPED_UNICODE);
