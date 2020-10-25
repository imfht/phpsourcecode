<?php
namespace test;

use WxSDK\core\module\MenuKit;
use WxSDK\WxApp;

define("__DIR_", dirname(dirname(__FILE__)));

include '../Loader.php'; // 自动加载类

$app=new WxApp();
// $button1 = MenuKit::createButtonClick("点击", "test_click");
// $button2 = MenuKit::createButtonLocationSelect("地理位置", "location_up");
// // $button3 = MenuKit::createButtonMediaId("获取素材", "1234");
// $button4 = MenuKit::createButtonPicPhotoOrAlbum("拍照或图片", "pic_or_select");
// $button5 = MenuKit::createButtonPicSysphoto("系统拍照", "sys_pic");
// $button6 = MenuKit::createButtonPicWeixin("微信相册", "wx_image");
// $button7 = MenuKit::createButtonScancodePush("扫码", "sc");
// $button8 = MenuKit::createButtonScancodeWaitmsg("扫码等回复", "scan_waitmsg");
// $button9 = MenuKit::createButtonView("心灵相约", "http://www.51xlxy.com");
// // $button10 = MenuKit::createButtonViewLimited("图文", "34432");

// $button11 = MenuKit::createParentButton("1-4男", $button1,$button2,$button4);
// $button12 = MenuKit::createParentButton("5-8", $button5,$button6,$button7,$button8);
// $button13 = MenuKit::createParentButton("9-10", $button9);
// $menu = new Menu(null,$button11,$button12,$button13);
// $menu->matchrule = new MatchRule();
// $menu->matchrule->sex = "1";
// $ret = MenuKit::sendConditionMenu($accessToken, $menu);
// $ret = MenuKit::tryMatchConditionMenu($accessToken, "oUIjzjqtAVuPY2sIT96Ouy2V8Ejs");
$ret = MenuKit::deleteMenu($app);
$ret = MenuKit::getMenu($app);
print_r(json_encode($ret));
