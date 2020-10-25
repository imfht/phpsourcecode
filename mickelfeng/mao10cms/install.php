<?php
error_reporting(0);
header("Content-Type: text/html; charset=utf-8");
$site_url = "http://".$_SERVER["HTTP_HOST"].$_SERVER['PHP_SELF'];
$site_url = preg_replace("/\/[a-z0-9]+\.php.*/is", "", $site_url);
if($_POST['db_host'] && $_POST['db_name'] && $_POST['db_user'] && $_POST['db_prefix']) {
	$file = "Application/Common/Conf/db.php";
	$data = "<?php
return array(
	'DB_TYPE'   => 'mysql', // 数据库类型
	'DB_HOST'   => '".$_POST['db_host']."', // 服务器地址
	'DB_NAME'   => '".$_POST['db_name']."', // 数据库名
	'DB_USER'   => '".$_POST['db_user']."', // 用户名
	'DB_PWD'    => '".$_POST['db_pwd']."', // 密码
	'DB_PORT'   => 3306, // 端口
	'DB_PREFIX' => '".$_POST['db_prefix']."', // 数据库表前缀
	'DB_CHARSET' => 'utf8', //数据库编码
	'ADMIN_LOGIN' => '".$_POST['admin_login']."', //创始人账号
	'ADMIN_PASS' => '".$_POST['admin_pass']."', //创始人密码
); ?>";
	$db_info = file_put_contents ($file, $data);
	if($db_info) {
		$callback = 1;
	} else {
		$callback = 2;
	};
	$con = mysql_connect($_POST['db_host'],$_POST['db_user'],$_POST['db_pwd']);
	if (!$con) {
		$callback = 2;
	} else {
			mysql_query("CREATE DATABASE ".$_POST['db_name'],$con);
			mysql_select_db($_POST['db_name'],$con);
	        $db_prefix = $_POST['db_prefix'];
	        $table[] = "CREATE TABLE IF NOT EXISTS ".$db_prefix."page (
	        	id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	        	PRIMARY KEY(id),
	        	title text,
	        	content longtext,
	        	type varchar(20),
	        	date int
				) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			$table[] = "CREATE TABLE IF NOT EXISTS ".$db_prefix."meta (
	        	id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	        	PRIMARY KEY(id),
	        	page_id bigint(20) UNSIGNED,
	        	meta_key varchar(20),
	        	meta_value text,
	        	type varchar(20)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			$table[] = "CREATE TABLE IF NOT EXISTS ".$db_prefix."action (
	        	id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	        	PRIMARY KEY(id),
	        	page_id bigint(20) UNSIGNED,
	        	user_id bigint(20) UNSIGNED,
	        	action_key varchar(20),
	        	action_value text,
	        	date int
				) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			$table[] = "CREATE TABLE IF NOT EXISTS ".$db_prefix."option (
	        	id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	        	PRIMARY KEY(id),
	        	meta_key varchar(20),
	        	meta_value text,
	        	type varchar(20)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			$table[] = "CREATE TABLE IF NOT EXISTS ".$db_prefix."attached (
	        	id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	        	PRIMARY KEY(id),
	        	src varchar(255),
	        	type varchar(20)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	        foreach($table as $query) {
				mysql_query($query,$con);
			};
			mysql_close($con);
	};
} else {
	$callback = 0;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>安装Mao10CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="keywords" content="PHP建站系统" />
<meta name="description" content="新概念社交网络商城" />
<!-- Bootstrap -->
<link rel="stylesheet" href="<?php echo $site_url; ?>/Theme/default/css/bootstrap.css">
<script src="<?php echo $site_url; ?>/Theme/default/js/jquery.min.js"></script>
<!--[if lt IE 9]>
<script src="<?php echo $site_url; ?>/Theme/default/js/html5shiv.min.js"></script>
<script src="<?php echo $site_url; ?>/Theme/default/js/respond.min.js"></script>
<![endif]-->
<meta name='robots' content='noindex,follow' />
<style>
html {text-rendering: optimizeLegibility;-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale;height: 101%;}
body {font-size: 16px;-ms-word-break: break-all;word-break: break-all;word-break: break-word;word-wrap: break-word;overflow-wrap: break-word;background: #fff;color: #747f8c;font-family: "Open Sans",Arial,"Hiragino Sans GB","Microsoft YaHei","微软雅黑","STHeiti","WenQuanYi Micro Hei",SimSun,sans-serif; padding-bottom: 50px;}
h1 {margin-top: 40px;}
.bg {background: #ff4a00; padding: 40px 0; margin:15px 0 40px;}
h2,h4 {color: #fff;}
.form-control {border-color: #e3e3e3; color: #ff4a00;}
.btn-warning {background-color: #ff4a00; border-color: #ff4a00;}
.btn-warning:hover,.btn-warning:focus,.btn-warning:active {background-color: #fff; color: #ff4a00; border-color: #ff4a00;}
</style>
</head>
<body>
<h1 class="text-center"><a href="http://www.mao10.com/"><img src="<?php echo $site_url; ?>/Theme/default/img/logo.png"></a></h1>
<?php if($callback==0) : ?>
<div class="bg text-center">
	<h2>Mao10CMS</h2>
	<h4>开始安装，请预先建立好数据库，并填写正确的数据库信息</h4>
</div>
<div class="container">
	<div class="row">
		<div class="col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
			<form role="form" method="post">
				<div class="form-group">
					<label>数据库地址</label>
					<input type="text" class="form-control input-lg" name="db_host" value="localhost">
				</div>
				<div class="form-group">
					<label>数据库名称</label>
					<input type="text" class="form-control input-lg" name="db_name" value="mao10cms">
				</div>
				<div class="form-group">
					<label>数据库用户名</label>
					<input type="text" class="form-control input-lg" name="db_user" value="root">
				</div>
				<div class="form-group">
					<label>数据库密码</label>
					<input type="text" class="form-control input-lg" name="db_pwd">
				</div>
				<div class="form-group">
					<label>数据库前缀</label>
					<input type="text" class="form-control input-lg" name="db_prefix" value="mc_">
				</div>
				<div class="form-group">
					<label>网站管理员账号</label>
					<input type="text" class="form-control input-lg" name="admin_login" value="">
				</div>
				<div class="form-group">
					<label>网站管理员密码</label>
					<input type="password" class="form-control input-lg" name="admin_pass" value="">
				</div>
				<button type="submit" class="btn btn-warning btn-block btn-lg">
					确认无误
				</button>
			</form>
		</div>
	</div>
</div>
<?php elseif($callback==1) : ?>
<div class="bg text-center">
	<h2>Mao10CMS</h2>
	<h4>数据库写入成功，请继续下一步</h4>
</div>
<div class="container">
	<div class="row">
		<div class="col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
			<a class="btn btn-warning btn-block btn-lg" href="<?php echo $site_url.'?m=home&c=install&a=index'; ?>">立即安装</a>
		</div>
	</div>
</div>
<?php else : ?>
<div class="bg text-center">
	<h2>Mao10CMS</h2>
	<h4>数据库写入失败，请检查数据库信息是否填写正确，以及“Application/Common/Conf/db.php”是否可写</h4>
</div>
<div class="container">
	<div class="row">
		<div class="col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
			<a class="btn btn-warning btn-block btn-lg" href="<?php echo $site_url.'/install.php'; ?>">重新填写数据库信息</a>
		</div>
	</div>
</div>
<?php endif; ?>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="<?php echo $site_url; ?>/Theme/default/js/bootstrap.min.js"></script>
<script src="<?php echo $site_url; ?>/Theme/default/js/placeholder.js"></script>
<script type="text/javascript">
	$(function() {
		$('input, textarea').placeholder();
	});
</script>
</body>
</html>