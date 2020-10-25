<?php
namespace test;

use WxSDK\WxApp;
use WxSDK\core\module\QrcodeKit;

define("__DIR_", dirname(dirname(__FILE__)));

include '../Loader.php'; // 自动加载类

$app=new WxApp();

// $ret = QrcodeKit::qrcodeCreate4ShortTime($accessToken);
$ret = QrcodeKit::qrcodeCreate4Forever($app, null, 'test');

print_r(json_encode($ret));

?>
<html>
<body>
<img alt="ceshi" src="<?php echo "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$ret->getData()["ticket"]?>">
</body>
</html>

