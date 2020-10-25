<?php
namespace test\card;

use WxSDK\WxApp;
use WxSDK\core\model\card\send\Card4LandingPage;
use WxSDK\core\model\card\send\SendCard;
use WxSDK\core\module\CardKit;

define("__DIR_", dirname(dirname(dirname(__FILE__))));

include '../Loader.php'; // 自动加载类

$accessToken = new WxApp();
$sendCard = new SendCard();
$sendCard->card_id = "pUIjzjvWIeWKPl7PnTgCr2Kbz29o";
$sendCard->outer_str="test";

$c1 = new Card4LandingPage("pUIjzjt6z6Sazm98S7SYLPvk7VpU","http://www.51xlxy.com");
$c2 = new Card4LandingPage("pUIjzjvWIeWKPl7PnTgCr2Kbz29o","http://pic9.nipic.com/20100923/2531170_140325352643_2.jpg");
$ret = CardKit::createLandingPage($accessToken,"http://www.51xlxy.com","卡券商城",true,"SCENE_QRCODE",$c1,$c2);
echo json_encode($ret,JSON_UNESCAPED_UNICODE);
