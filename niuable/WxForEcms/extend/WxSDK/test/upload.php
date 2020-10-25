<?php
namespace test;

use WxSDK\WxApp;
use WxSDK\core\model\mass\Article;
use WxSDK\core\model\mass\News;
use WxSDK\core\module\UpKit;
define("__DIR_", dirname(dirname(__FILE__)));

include '../Loader.php'; // 自动加载类

$accessToken = new WxApp();

$article = new Article("ceshi", "rBfqMHvB3ajj7Vvo5UL_kSov7JmKLahy42mTYbsemuE", "正在测试中", "http://www.51xlxy.com");
$news = new News($article);
$ret = UpKit::uploadNewsForever($accessToken, $news);

print_r(json_encode($ret));
