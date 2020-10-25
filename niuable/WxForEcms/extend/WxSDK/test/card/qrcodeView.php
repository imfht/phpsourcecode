<?php
namespace test\card;


use WxSDK\core\module\QrcodeKit;
define("__DIR_", dirname(dirname(dirname(__FILE__))));

include '../Loader.php'; // 自动加载类


$url = QrcodeKit::getUrlByTicket("gQFV8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyVjA1NEI1ZzhiUGUxUzdGaU51NDgAAgQHNvFcAwSAM_EB");

?>

<html>
<body>
<img src="<?php echo $url;?>" alt="卡券二维码">
</body>
</html>