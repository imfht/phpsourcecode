<?php
namespace test;

use WxSDK\WxApp;
use WxSDK\core\module\AccountKit;
use WxSDK\core\module\QrcodeKit;
define("__DIR_", dirname(dirname(__FILE__)));

include '../Loader.php'; // 自动加载类

$app=new WxApp();

$ret = AccountKit::urlTrans2Short($app,"https://www.niuable.cn");

print_r(json_encode($ret));

?>
<html>
<body>
<img alt="ceshi" src="<?php echo QrcodeKit::getUrlByTicket($ret->data["ticket"]); ?>">
</body>
</html>

