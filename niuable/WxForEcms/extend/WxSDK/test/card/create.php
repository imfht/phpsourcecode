<?php
namespace test\card;

use WxSDK\WxApp;
use WxSDK\core\model\card\AdvancedInfo;
use WxSDK\core\model\card\BaseInfo;
use WxSDK\core\model\card\Card;
use WxSDK\core\model\card\DateInfo;
use WxSDK\core\model\card\GroupOn;
use WxSDK\core\model\card\Sku;
use WxSDK\core\module\CardKit;

define("__DIR__", dirname(dirname(dirname(__FILE__))));

include '../Loader.php'; // 自动加载类

spl_autoload_register('Loader::autoload'); // 注册自动加载方法
$accessToken = new WxApp();

$card = new Card("GROUPON");
$advacedInfo= new AdvancedInfo();
$groupOn = new GroupOn();
$groupOn->advanced_info = $advacedInfo;
$dateInfo = new DateInfo("DATE_TYPE_FIX_TIME_RANGE",1559214517,1559414517);
$baseInfo = new BaseInfo("http://www.51xlxy.com","CODE_TYPE_TEXT","键盘旋律vip","欢迎加入键盘旋律","Color010","请尽快使用","该卡片存在有效期，请留意",new Sku(100),$dateInfo);
$groupOn->base_info = $baseInfo;
$groupOn->deal_detail = "团购详情";
$card->groupon = $groupOn;
$ret = CardKit::create($accessToken,$card);
//card_id : pUIjzjvWIeWKPl7PnTgCr2Kbz29o
echo json_encode($ret,JSON_UNESCAPED_UNICODE);
