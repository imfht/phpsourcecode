<?php
namespace app\test;
use app\services\IntConvert;

/**
 * 文件：index.php
 * 作者：mqycn
 * 博客：http://www.miaoqiyuan.cn
 * 源码：https://gitee.com/mqycn/IntConvert
 * 说明：数字字符串互换类，常见的应用场景比如邀请码
 */

# 如果使用 TP框架，会自动引入
require '../services/IntConvert.php';

header('content-type: text/html; charset=utf-8');
$arr = [];
for ($i = 0; $i < 1000000; $i += 10000) {
	for ($j = 0; $j < 5; $j++) {
		$res = [
			'加密前' => $i + $j,
			'加密后' => IntConvert::toString($i + $j),
		];
		$res['解密'] = IntConvert::toInt($res['加密后']);
		$res['碰撞'] = IntConvert::toInt('0' . substr($res['加密后'], 1));
		$arr[] = $res;
	}
}

$code = json_encode($arr);
?><h1>建议使用 Firefox 或 Chrome 测试</h1>
<h2>请按F12打开调试模式，切换到 Console|控制台 标签</h2>
<script>
	var data=<?php echo $code; ?>;
	console.table(data);
</script>
<hr /><pre><?php
print_r($arr);
?>