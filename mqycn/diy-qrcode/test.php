<?php

/**
 * 文件：test.php
 * 作者：mqycn
 * 博客：http://www.miaoqiyuan.cn
 * 源码：http://gitee.com/mqycn/diy-qrcode/
 * 说明：测试脚本
 */

/**
 * 落地网址，请在 qrcode.*** 中修改
 */

// 当前访问的网址
$root_uri = (isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : ($_SERVER['HTTPS'] == 'on' ? 'https' : 'http')) . '://' . $_SERVER['SERVER_NAME'] . '/';

// 程序安装的路径
$ins_arr = explode('/', $_SERVER['SCRIPT_NAME']);
$ins_arr[count($ins_arr) - 1] = '';
unset($ins_arr[0]);
$ins_path = join('/', $ins_arr);

// 二维码样式，参考 qrcode.skin1
$skin = isset($_GET['skin']) ? $_GET['skin'] : 'skin1';
if (empty($skin)) {
	$skin = 'skin1';
}
$skin_options = '';
foreach (scandir('./') as $skin_name) {
	if (strpos($skin_name, 'qrcode.') > -1) {
		$temp = explode('.', $skin_name);
		$skin_options .= '<option';
		if ($temp[1] == $skin) {
			$skin_options .= ' selected';
		}
		$skin_options .= '>' . $temp[1] . ' </option>';
	}
}

// 二维码内容
$qrcode = isset($_GET['qrcode']) ? $_GET['qrcode'] : '123456';
$key = base64_encode($qrcode);

// 传统方式调用
$img_url = "{$root_uri}{$ins_path}main.php?key={$key}&skin={$skin}";

// 伪静态方式调用
$img_url_rewrite = "{$root_uri}apps/qrcode/{$key}/{$skin}.png";

?>
<!DOCTYPE html>
<html lang="zh-CN">
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta charset="utf-8">
		<link href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
		<script src="https://cdn.bootcss.com/jquery/1.12.4/jquery.min.js"></script>
	</head>
	<body style="background:#CCC;padding-top:30px">
		<div class="container">
			<div class="row">
<?php

if (isset($_GET['page'])) {
	// 根据不同的页面，输出不同的广告代码
	switch ($_GET['page']) {
	case 'share':
		$url = 'http://www.miaoqiyuan.cn/js/ad/cyjl.m/taobao.asp';
		break;
	case 'skin3':
		$url = 'http://www.miaoqiyuan.cn/js/ad/cyjl.m/jd.asp';
		break;
	default:
		$url = 'http://m.youku.com/';
	}
	$page_ad = '<iframe src="' . $url . '" width="100%" height="600" frameborder="0"></iframe>';
	?>
				<div class="col-xs-12">
					<div class="panel panel-info">
						<div class="panel-heading">
							<b>您的邀请码</b>
							<kbd><?php echo $qrcode; ?></kbd>
							<b>，扫码模板</b>
							<kbd><?php echo $skin; ?></kbd>
						</div>
						<div class="panel-body">
							<?php echo $page_ad; ?>
						</div>
					</div>
				</div>
<?php
}
?>
				<div class="col-xs-12">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<b>DiyQrcode测试</b>
						</div>
						<div class="panel-body text-center">
							<form class="form-inline">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon">请输入邀请码</div>
										<input type="text" class="form-control" name="qrcode" value="<?php echo $qrcode; ?>">
									</div>
								</div>
								<div class="form-group">
									<select name="skin" class="form-control"><option>==请选择要模版==</option><?php echo $skin_options; ?></select>
								</div>
								<div class="form-group">
									<button type="submit" class="btn btn-danger"><span class="glyphicon glyphicon-qrcode"></span> <b>生成宣传海报<b></button>
								</div>
								<div class="form-group">
									<div class="btn-group">
										<a href="http://www.miaoqiyuan.cn/" target="_blank" class="btn btn-info"><span class="glyphicon glyphicon-home"></span> <b>作者博客</b></a>
										<a href="https://gitee.com/mqycn/diy-qrcode/" target="_blank" class="btn btn-warning"><span class="glyphicon glyphicon-link"></span> <b>最新代码</b></a>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="col-sm-4 col-xs-12">
					<div class="panel panel-danger">
						<div class="panel-heading">
							<b>正常调用模式</b>
						</div>
						<div class="panel-body text-center">
							<img src="<?php echo $img_url; ?>" width="80%">
							<div class="input-group">
								<input class="form-control" value="<?php echo $img_url; ?>">
								<div class="input-group-btn">
									<a href="<?php echo $img_url; ?>" target="_blank" class="btn btn-primary">访问</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-4 col-xs-12">
					<div class="panel panel-info">
						<div class="panel-heading">
							<b>伪静态方式调用</b>
						</div>
						<div class="panel-body text-center">
							<img src="<?php echo $img_url_rewrite; ?>" width="80%">
							<div class="input-group">
								<input class="form-control" value="<?php echo $img_url_rewrite; ?>">
								<div class="input-group-btn">
									<a href="<?php echo $img_url_rewrite; ?>" target="_blank" class="btn btn-primary">访问</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-4 col-xs-12">
					<div class="panel panel-warning">
						<div class="panel-heading">
							<b>生成静态文件，Ajax返回路径</b>
						</div>
						<div class="panel-body text-center">
							<img alt="正在加载中..." src="" width="80%" id="qrcode_image">
							<div class="input-group">
								<input class="form-control" value="加载中..." id="qrcode_input">
								<div class="input-group-btn">
									<a href="#" target="_blank" class="btn btn-primary" id="qrcode_link">访问</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			$(function(){
				$.getJSON('<?php echo $img_url . '&response_type=json'; ?>', function(res){
					$('#qrcode_image').attr('src', res.url);
					$('#qrcode_input').val(res.url);
					$('#qrcode_link').attr('href', res.url);
				});
			});
		</script>
	</body>
</html>
