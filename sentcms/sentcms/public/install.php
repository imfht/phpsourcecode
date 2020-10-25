<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace think;

session_start();
require __DIR__ . '/../vendor/autoload.php';

$app = new App();

if (is_file($app->getRootPath() . '.env') && is_file($app->getRootPath() . 'install.lock')) {
	header('Location: index.php');exit();
}
session('error', 0);

define('ROOT_PATH', $app->getRootPath());
$action = $app->request->param('action', 'index');

$title = "";
if ($action == 'index') {
	$title = "系统安装";
}elseif ($action == 'check') {
	session('step_check', false);
	//环境检测
	$env = check_env();
	$dirfile = check_dirfile();
	//函数检测
	$func = check_func();

	if (!session('error')) {
		session('step_check', true);
	}

	$title = "环境检查";
}elseif ($action == 'config') {
	if ($app->request->isAjax()) {
		$data = $app->request->param();
		$info = file_get_contents(ROOT_PATH . '.example.env');
		$dbc = true;
		foreach ($data['database'] as $key => $value) {
			if ($value == '') {
				$dbc = false;
			}
			$info = str_replace("{".$key."}", $value, $info);
		}
		foreach ($data['admin'] as $key => $value) {
			if ($value == '') {
				$dbc = false;
			}
		}
		if (!$dbc) {
			echo json_encode(['code'=>0,'msg'=>'请填写完系统配置信息！']);exit();
		}
		if ($data['admin']['password'] !== $data['admin']['repassword']) {
			echo json_encode(['code'=>0,'msg'=>'管理员密码错误']);exit();
		}
		$info = str_replace("{secret}", \xin\helper\Str::random(32), $info);
		file_put_contents(ROOT_PATH . '.env', $info);
		session('database_info', $data['database']);
		session('admin_info', $data['admin']);

		try {
			$dsn = "mysql:host=".$data['database']['hostname'].";dbname=".$data['database']['database'];
			$con = new \PDO($dsn, $data['database']['username'], $data['database']['password']);

			session('step_config', true);
			$data = ['code' => 1];
			echo json_encode($data);exit();
		} catch (\PDOException $e) {
			session('step_config', false);
			$data = ['code' => 0, 'msg' => $e->getMessage()];
			echo json_encode($data);exit();
		}
	}else{
		if (!session('step_check')) {
			echo "<script>window.location.href = 'install.php?action=check';</script>";exit();
		}

		$title = "系统配置";
	}
}elseif ($action == 'database') {
	if (!session('database_info') || !session('admin_info') || !session('step_config')) {
		echo "<script>window.location.href = 'install.php?action=config';</script>";exit();
	}
	$title = "数据库安装";
}elseif ($action == 'complete') {
	if (!session('step_database')) {
		echo "<script>window.location.href = 'install.php?action=database';</script>";exit();
	}
	file_put_contents(ROOT_PATH . "install.lock", "ok");
	$title = "安装完成";
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>SentCMS系统安装</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">
<!-- zui -->
<link href="static/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="static/plugins/adminlte/css/AdminLTE.min.css" rel="stylesheet">
<link rel="stylesheet" href="static/plugins/adminlte/css/skins/_all-skins.min.css">
<script src="static/plugins/jquery/jquery.min.js"></script>
<script type="text/javascript">
function showmsg(msg, classname){
	$('div#show-list').append('<li class="list-group-item text-'+classname+'">'+msg+'</li>');
}
</script>
<style type="text/css">
caption h3{font-size: 16px; font-weight: bold; color: #333333;}
</style>
</head>

<body class="hold-transition skin-blue layout-top-nav">
<div class="wrapper">
	<header class="main-header">
		<nav class="navbar navbar-static-top">
			<div class="container">
				<div class="navbar-header">
					<a href="#" class="navbar-brand">SentCMS系统安装程序</a>
				</div>
			</div>
		</nav>
	</header>
	<div class="content-wrapper">
		<div class="container">
			<!-- Content Header (Page header) -->
			<section class="content-header">
				<h1>&nbsp;</h1>
				<ol class="breadcrumb">
					<li><a href="#"><i class="fa fa-dashboard"></i> SentCMS系统</a></li>
					<li class="active"><?php echo $title; ?></li>
				</ol>
			</section>

			<!-- Main content -->
			<section class="content">
				<div class="row">
					<div class="col-sm-2" style="background: #222d32; padding: 0;">
						<section class="sidebar">
							<ul class="sidebar-menu">
								<li class="header">安装步骤</li>
								<li <?php if($action == 'index'){?>class="active"<?php } ?>><a><i class="fa fa-book"></i> <span>系统安装</span></a></li>
								<li <?php if($action == 'check'){?>class="active"<?php } ?>><a><i class="fa fa-book"></i> <span>环境检查</span></a></li>
								<li <?php if($action == 'config'){?>class="active"<?php } ?>><a><i class="fa fa-book"></i> <span>系统配置</span></a></li>
								<li <?php if($action == 'database'){?>class="active"<?php } ?>><a><i class="fa fa-book"></i> <span>数据库安装</span></a></li>
								<li <?php if($action == 'complete'){?>class="active"<?php } ?>><a><i class="fa fa-book"></i> <span>安装完成</span></a></li>
							</ul>
						</section>
					</div>
					<div class="col-sm-10" style="padding-left: 15px;">
						<div class="box box-primary">
							<div class="box-header with-border">
								<h3 class="box-title"><?php echo $title; ?></h3>
							</div>
							<div class="box-body">
								<?php if($action == 'index'){?>
								<div class="margin-top">
									<header>
										<h2 class="text-center">SentCMS网站管理系统 安装协议</h2>

										<section class="text-center abstract">版权所有 (c) 2014-2015，南昌腾速科技有限公司保留所有权利。</section>
									</header>
									<section class="article-content" style="text-indent: 2em">
										<p>
											SentCMS网站管理系统基于
											<a target="_blank" href="http://www.thinkphp.cn">ThinkPHP</a>框架
											的开发产品。感谢顶想公司为SentCMS网站管理系统提供内核支持。
										</p>
										<p>
											感谢您选择SentCMS网站管理系统，希望我们的努力可以为您创造价值。公司网址为
											<a href="https://www.tensent.cn" target="_blank">https://www.tensent.cn</a>
											，产品官方网站网址为
											<a href="http://www.sentcms.com" target="_blank">http://www.sentcms.com</a>
											。
										</p>
										<p>
											用户须知：本协议是您于南昌腾速科技有限公司关于SentCMS网站管理系统产品使用的法律协议。无论您是个人或组织、盈利与否、用途如何（包括以学习和研究为目的），均需仔细阅读本协议，包括免除或者限制南昌腾速科技有限公司责任的免责条款及对您的权利限制。请您审阅并接受或不接受本服务条款。如您不同意本服务条款及或南昌腾速科技有限公司随时对其的修改，您应不使用或主动取消SentCMS网站管理系统产品。否则，您的任何对SentCMS网站管理系统的相关服务的注册、登陆、下载、查看等使用行为将被视为您对本服务条款全部的完全接受，包括接受南昌腾速科技有限公司对服务条款随时所做的任何修改。
										</p>
										<p>
											本服务条款一旦发生变更,南昌腾速科技有限公司将在产品官网上公布修改内容。修改后的服务条款一旦在网站公布即有效代替原来的服务条款。您可随时登陆官网查阅最新版服务条款。如果您选择接受本条款，即表示您同意接受协议各项条件的约束。如果您不同意本服务条款，则不能获得使用本服务的权利。您若有违反本条款规定，南昌腾速科技有限公司有权随时中止或终止您对SentCMS网站管理系统产品的使用资格并保留追究相关法律责任的权利。
										</p>
										<p>
											在理解、同意、并遵守本协议的全部条款后，方可开始使用SentCMS网站管理系统产品。您也可能与南昌腾速科技有限公司直接签订另一书面协议，以补充或者取代本协议的全部或者任何部分。
										</p>
										<p>
											南昌腾速科技有限公司拥有SentCMS网站管理系统的知识产权，包括商标和著作权。本软件只供许可协议，并非出售。想天只允许您在遵守本协议各项条款的情况下复制、下载、安装、使用或者以其他方式受益于本软件的功能或者知识产权。
										</p>
									</section>
								</div>
								<div class="margin-top" style="text-align: center;">
									<div class="btn-group">
										<a class="btn btn-default" href="#">上一步</a>
										<a class="btn btn-danger" href="?action=check">下一步</a>
									</div>
								</div>
								<?php }elseif($action == 'check'){?>

									<table class="table table-hover">
										<caption><h3>运行环境检查</h3></caption>
										<thead>
											<tr>
												<th>项目</th>
												<th>所需配置</th>
												<th>当前配置</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach($env as $item) { ?>
												<tr>
													<td><?php echo $item[0]; ?></td>
													<td><?php echo $item[1]; ?></td>
													<td><i class="icon icon-<?php echo $item[4]; ?>">&nbsp;</i><?php echo $item[3]; ?></td>       
												</tr>
											<?php } ?>
										</tbody>
									</table>
									<table class="table table-hover">
										<caption><h3>目录、文件权限检查</h3></caption>
										<thead>
											<tr>
												<th>目录/文件</th>
												<th>所需状态</th>
												<th>当前状态</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach($dirfile as $item) { ?>
												<tr>
													<td><?php echo $item[3]; ?></td>
													<td><i class="icon icon-ok">&nbsp;</i>可写</td>
													<td><i class="icon icon-<?php echo $item[2]; ?>">&nbsp;</i><?php echo $item[1]; ?></td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
									<table class="table table-hover">
										<caption><h3>函数依赖性检查</h3></caption>
										<thead>
											<tr>
												<th>函数名称</th>
												<th>检查结果</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach($func as $item) { ?>
												<tr>
													<td><?php echo $item[0]; ?>()</td>
													<td><i class="icon icon-<?php echo $item[2]; ?>">&nbsp;</i><?php echo $item[1]; ?></td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
									<?php if(!session('error')){ ?>
									<div class="margin-top" style="text-align: center;">
										<div class="btn-group">
											<a class="btn btn-default" href="?action=index">上一步</a>
											<a class="btn btn-danger" href="?action=config">下一步</a>
										</div>
									</div>
									<?php } ?>

								<?php }elseif($action == 'config'){?>
									<form class="form form-horizontal" method="post">
										<span style="font-size: 18px; font-weight: bold;">数据库配置</span>
										<hr style="margin: 10px auto;">
										<div class="form-group">
											<label class="col-lg-2 control-label">数据库类型</label>
											<div class="col-lg-8">
												<select class="form-control" name="database[type]" style="width: auto;">
													<option value="mysql">mysql</option>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="col-lg-2 control-label">数据库地址</label>
											<div class="col-lg-8">
												<input type="text" name="database[hostname]" class="form-control" value="localhost">
												<span class="help-block">（数据库名称不能为空）</span>
											</div>
										</div>
										<div class="form-group">
											<label class="col-lg-2 control-label">数据库名称</label>
											<div class="col-lg-8">
												<input type="text" name="database[database]" class="form-control" value="">
												<span class="help-block">（数据库名称不能为空）</span>
											</div>
										</div>
										<div class="form-group">
											<label class="col-lg-2 control-label">数据库用户名</label>
											<div class="col-lg-8">
												<input type="text" name="database[username]" class="form-control" value="">
												<span class="help-block">（数据库名称不能为空）</span>
											</div>
										</div>
										<div class="form-group">
											<label class="col-lg-2 control-label">数据库密码</label>
											<div class="col-lg-8">
												<input type="text" name="database[password]" class="form-control" value="">
												<span class="help-block">（数据库名称不能为空）</span>
											</div>
										</div>
										<div class="form-group">
											<label class="col-lg-2 control-label">数据库端口号</label>
											<div class="col-lg-8">
												<input type="text" name="database[hostport]" class="form-control" value="3306">
												<span class="help-block">（数据库名称不能为空）</span>
											</div>
										</div>
										<div class="form-group">
											<label class="col-lg-2 control-label">数据库前缀</label>
											<div class="col-lg-8">
												<input type="text" name="database[prefix]" class="form-control" value="sent_">
												<span class="help-block">（数据库名称不能为空）</span>
											</div>
										</div>
										<hr>
										<span style="font-size: 18px; font-weight: bold;">管理员配合</span>
										<hr style="margin: 10px auto;">
										<div class="form-group">
											<label class="col-lg-2 control-label">管理员账号</label>
											<div class="col-lg-8">
												<input type="text" name="admin[username]" class="form-control" value="admin">
												<span class="help-block">（数据库名称不能为空）</span>
											</div>
										</div>
										<div class="form-group">
											<label class="col-lg-2 control-label">管理员密码</label>
											<div class="col-lg-8">
												<input type="text" name="admin[password]" class="form-control" value="">
												<span class="help-block">（数据库名称不能为空）</span>
											</div>
										</div>
										<div class="form-group">
											<label class="col-lg-2 control-label">确认密码</label>
											<div class="col-lg-8">
												<input type="text" name="admin[repassword]" class="form-control" value="">
												<span class="help-block">（数据库名称不能为空）</span>
											</div>
										</div>
										<div class="form-group">
											<label class="col-lg-2 control-label">管理员邮箱</label>
											<div class="col-lg-8">
												<input type="text" name="admin[email]" class="form-control" value="admin@admin.com">
												<span class="help-block">（数据库名称不能为空）</span>
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-offset-2 col-sm-10">
												<button type="submit" class="btn btn-success submit-btn">确认提交</button>
												<button class="btn btn-danger btn-return" onclick="javascript:history.back(-1);return false;">返 回</button>
											</div>
										</div>
									</form>
									<script type="text/javascript">
									$(function(){
										//表单提交
										$(document).ajaxStart(function(){
											$("button:submit").attr("disabled", true);
										}).ajaxStop(function(){
											$("button:submit").attr("disabled", false);
										});
										$("form").submit(function(e){
											e.preventDefault();
											$.ajax({
												data: $('form').serialize(),
												type: 'post',
												success: function(res){
													if (res.code == 1) {
														window.location.href = "install.php?action=database";
													}else{
														alert(res.msg);
													}
												},
												dataType: 'json'
											});
										})
									})
									</script>
								<?php }elseif($action == 'database'){?>
									<h3 class="text-center">安装数据库</h3>
									<div id="show-list" class="install-database list-group" style="height:450px; overflow-y:auto; ">
									</div>
									<div class="text-center">
										<button class="btn btn-warning disabled">正在安装，请稍候...</button>
									</div>
									<?php
									session('error', false);
									$dbconfig = session('database_info');
									$dsn = "mysql:host=".$dbconfig['hostname'].";dbname=".$dbconfig['database'].";charset=utf8";
									$db = new \PDO($dsn, $dbconfig['username'], $dbconfig['password']);

									//创建数据表
									create_tables($db, $dbconfig['prefix']);
									//注册创始人帐号
									$admin = session('admin_info');
									register_administrator($db, $dbconfig['prefix'], $admin);

									if (session('error')) {
										show_msg('失败');
									} else {
										session('step_database', true);
										echo '<script type="text/javascript">location.href = "install.php?action=complete";</script>';
									}
									?>
								<?php }elseif($action == 'complete'){?>
									<h3 class="text-center">完成</h3>
									<div class="row">
										<div class="col-sm-6">
											<ul class="list-group">
												<li class="list-group-item"><a href="http://www.tensent.cn" target="_blank">南昌腾速科技有限公司</a></li>
												<li class="list-group-item"><a href="http://git.oschina.net/sentcms/sentcms" target="_blank">SentCMS4.0版本库</a></li>
												<li class="list-group-item"><a href="http://wpa.qq.com/msgrd?v=3&uin=707479167&site=qq&menu=yes" target="_blank">系统定制</a></li>
											</ul>
										</div>
										<div class="col-sm-6">
											<ul class="list-group">
												<li class="list-group-item"><a href="https://www.kancloud.cn/tensent/sentcms4/content" target="_blank">SentCMS4.0开发文档</a></li>
												<li class="list-group-item"><a href="http://bbs.sentcms.com" target="_blank">SentCMS讨论社区</a></li>
												<li class="list-group-item"><a href="http://jq.qq.com/?_wv=1027&k=2GgKNPQ" target="_blank">开发交流群</a></li>
											</ul>
										</div>
									</div>
									<div class="text-center">
										<a class="btn btn-primary" target="_blank" href="/admin">登录后台</a>
										<a class="btn btn-success" target="_blank" href="/">访问首页</a>
									</div>
								<?php }?>

							</div>
						</div>
						<!-- /.box -->
					</div>
				</div>
			</section>

		</div>
	</div>
</div>
</body>
</html>
<?php
// 检测环境是否支持可写
define('IS_WRITE', true);

/**
 * 系统环境检测
 * @return array 系统环境数据
 */
function check_env(){
	$items = array(
		'os'      => array('操作系统', '不限制', '类Unix', PHP_OS, 'success'),
		'php'     => array('PHP版本', '7.1.0', '7.1+', PHP_VERSION, 'success'),
		'upload'  => array('附件上传', '不限制', '2M+', '未知', 'success'),
		'gd'      => array('GD库', '2.0', '2.0+', '未知', 'success'),
		'disk'    => array('磁盘空间', '20M', '不限制', '未知', 'success'),
	);

	//PHP环境检测
	if($items['php'][3] < $items['php'][1]){
		$items['php'][4] = 'error';
		session('error', true);
	}

	//附件上传检测
	if(@ini_get('file_uploads'))
		$items['upload'][3] = ini_get('upload_max_filesize');

	//GD库检测
	$tmp = function_exists('gd_info') ? gd_info() : array();
	if(empty($tmp['GD Version'])){
		$items['gd'][3] = '未安装';
		$items['gd'][4] = 'error';
		session('error', true);
	} else {
		$items['gd'][3] = $tmp['GD Version'];
	}
	unset($tmp);

	//磁盘空间检测
	if(function_exists('disk_free_space')) {
		$items['disk'][3] = floor(disk_free_space(ROOT_PATH) / (1024*1024)).'M';
	}

	return $items;
}

/**
 * 目录，文件读写检测
 * @return array 检测数据
 */
function check_dirfile(){
	$items = array(
		array('dir',  '可写', 'success', 'uploads/file/'),
		array('dir',  '可写', 'success', 'uploads/image/'),
		array('dir',  '可写', 'success', 'uploads/media/'),
	);

	foreach ($items as &$val) {
		$item =	ROOT_PATH . 'public' . DIRECTORY_SEPARATOR . $val[3];
		if('dir' == $val[0]){
			if(!is_writable($item)) {
				if(is_dir($item)) {
					$val[1] = '可读';
					$val[2] = 'error';
					session('error', true);
				} else {
					$val[1] = '不存在';
					$val[2] = 'error';
					session('error', true);
				}
			}
		} else {
			if(file_exists($item)) {
				if(!is_writable($item)) {
					$val[1] = '不可写';
					$val[2] = 'error';
					session('error', true);
				}
			} else {
				if(!is_writable(dirname($item))) {
					$val[1] = '不存在';
					$val[2] = 'error';
					session('error', true);
				}
			}
		}
	}

	return $items;
}

/**
 * 函数检测
 * @return array 检测数据
 */
function check_func(){
	$items = array(
		array('pdo','支持','success','类'),
		array('pdo_mysql','支持','success','模块'),
		array('file_get_contents', '支持', 'success','函数'),
		array('file_put_contents', '支持', 'success','函数'),
		array('mb_strlen',		   '支持', 'success','函数'),
	);

	foreach ($items as &$val) {
		if(('类'==$val[3] && !class_exists($val[0]))
			|| ('模块'==$val[3] && !extension_loaded($val[0]))
			|| ('函数'==$val[3] && !function_exists($val[0]))
			){
			$val[1] = '不支持';
			$val[2] = 'error';
			session('error', true);
		}
	}

	return $items;
}

/**
 * 创建数据表
 * @param  resource $db 数据库连接资源
 */
function create_tables($db, $prefix = ''){
	//读取SQL文件
	$sql = file_get_contents(ROOT_PATH . 'runtime' . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR . 'install.sql');
	$sql = str_replace("\r", "\n", $sql);
	$sql = explode(";\n", $sql);

	//替换表前缀
	$orginal = 'sent_';
	$sql = str_replace(" `{$orginal}", " `{$prefix}", $sql);

	//开始安装
	show_msg('开始安装数据库...');
	foreach ($sql as $value) {
		$value = trim($value);
		if(empty($value)) continue;
		if(substr($value, 0, 12) == 'CREATE TABLE') {
			$name = preg_replace("/^CREATE TABLE `(\w+)` .*/s", "\\1", $value);
			$msg  = "创建数据表{$name}";
			if(false !== $db->query($value)){
				show_msg($msg . '...成功');
			} else {
				show_msg($msg . '...失败！', 'error');
				session('error', true);
			}
		} else {
			$db->query($value);
		}

	}
}

function register_administrator($db, $prefix, $admin){
	show_msg('开始注册创始人帐号...');

	$salt = \xin\helper\Str::random(4);
	$password = md5($admin['password'].$salt);

	$sql = "INSERT INTO `[PREFIX]member` (`uid`,`username`,`password`,`nickname`,`email`, `sex`,`birthday`,`qq`,`score`,`salt`,`login`,`reg_ip`,`reg_time`,`last_login_ip`,`last_login_time`,`status`) VALUES ".
		   "('1', '[NAME]', '[PASS]', '[NAME]', '[EMAIL]', '0', '".date('Y-m-d')."', '', '0','[SALT]', '1', '0', '[TIME]', '[IP]', '[TIME]', '1');";
	$sql = str_replace(
		array('[PREFIX]', '[NAME]','[PASS]','[EMAIL]','[SALT]', '[TIME]', '[IP]'),
		array($prefix, $admin['username'],$password, $admin['email'],$salt, time(), \xin\helper\Server::getRemoteIp()),
		$sql);
	$db->query($sql);
	show_msg('创始人帐号注册完成！');
}

/**
 * 更新数据表
 * @param  resource $db 数据库连接资源
 * @author lyq <605415184@qq.com>
 */
function update_tables($db, $prefix = ''){
	//读取SQL文件
	$sql = file_get_contents(ROOT_PATH . 'runtime' . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR . 'update.sql');
	$sql = str_replace("\r", "\n", $sql);
	$sql = explode(";\n", $sql);

	//替换表前缀
	$sql = str_replace(" `sent_", " `{$prefix}", $sql);

	//开始安装
	show_msg('开始升级数据库...');
	foreach ($sql as $value) {
		$value = trim($value);
		if(empty($value)) continue;
		if(substr($value, 0, 12) == 'CREATE TABLE') {
			$name = preg_replace("/^CREATE TABLE `(\w+)` .*/s", "\\1", $value);
			$msg  = "创建数据表{$name}";
			if(false !== $db->execute($value)){
				show_msg($msg . '...成功');
			} else {
				show_msg($msg . '...失败！', 'error');
				session('error', true);
			}
		} else {
			if(substr($value, 0, 8) == 'UPDATE `') {
				$name = preg_replace("/^UPDATE `(\w+)` .*/s", "\\1", $value);
				$msg  = "更新数据表{$name}";
			} else if(substr($value, 0, 11) == 'ALTER TABLE'){
				$name = preg_replace("/^ALTER TABLE `(\w+)` .*/s", "\\1", $value);
				$msg  = "修改数据表{$name}";
			} else if(substr($value, 0, 11) == 'INSERT INTO'){
				$name = preg_replace("/^INSERT INTO `(\w+)` .*/s", "\\1", $value);
				$msg  = "写入数据表{$name}";
			}
			if(($db->execute($value)) !== false){
				show_msg($msg . '...成功');
			} else{
				show_msg($msg . '...失败！', 'error');
				session('error', true);
			}
		}
	}
}

/**
 * 及时显示提示信息
 * @param  string $msg 提示信息
 */
function show_msg($msg, $class = 'primary'){
	echo "<script type=\"text/javascript\">showmsg(\"{$msg}\", \"{$class}\")</script>";
	flush();
	ob_flush();
}

function session($name, $value = ''){
	if ($name == '') {
		return false;
	}
	if ('' !== $value) {
		$_SESSION[$name] = $value;
	}else{
		return isset($_SESSION[$name]) ? $_SESSION[$name] : false;
	}
}
?>