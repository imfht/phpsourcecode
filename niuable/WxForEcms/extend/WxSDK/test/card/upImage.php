<?php
namespace test\card;

use WxSDK\WxApp;
use WxSDK\core\model\card\BaseInfo;
use WxSDK\core\model\card\Card;
use WxSDK\core\model\card\GroupOn;
use WxSDK\core\module\CardKit;

include '../../Loader.php'; // 自动加载类

$accessToken = new WxApp();

$card = new Card("GROUPON");
$groupOn = new GroupOn();
$groupOn->base_info["logo_url"] = "http://www.baidu.com";

$card->groupon = $groupOn;
$groupOn->base_info = new BaseInfo("http://www.niuable.cn", $code_type,
    $brand_name, $title, $color, $notice, $description, $sku, $date_info);
$ret = CardKit::create($accessToken,$card);
print_r(json_encode($ret,JSON_UNESCAPED_UNICODE));
