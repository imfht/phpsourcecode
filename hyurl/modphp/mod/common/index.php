<?php
if(isset($_GET['action'])){
	if($_GET['action'] == 'login' && !is_logined()){
		$result = http_auth_login(); //HTTP 认证登录
		// $result = http_auth_login("HTTP Authentication", 2); //使用摘要认证登录
	}elseif($_GET['action'] == 'logout'){
		$result = user::logout(); //登出
	}
	if(isset($result)){
		echo $result['success'] ? "Success" : "Error";
		flush(); //刷出缓冲区，并发送请求头，确保清除 HTTP 认证信息
	}
	redirect(-1); //返回上一页
}else{
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>ModPHP Default Template</title>
</head>
<body>
	<h1>Welcome to ModPHP!</h1>
	<p>You're now able to explore the functionality it carries.<p>
	<?php if(!is_logined()): ?>
		<p>If you have a user account (stored in database or local file, default is "admin:12345"), please <a href="<?php echo site_url('index.php?action=login') ?>">click here</a> to sign in.</p>
	<?php else: ?>
		<p>Hi <?php echo get_me('user_nickname') ?: get_me('user_name') ?>, nice to see you!</p>
		<?php if(!config('mod.installed')): ?>
			<p>You logged in with a local account, it stores in the config file: <em><?php echo __ROOT__ ?>user/config/users.php</em>.</p>
			<p>You can change this file to update your information or add a new account.</p>
		<?php endif ?>
		<p> Bellow is your information (password will not show in any way).</p>
		<fieldset style="display: inline-block;padding-right: 40px;">
			<legend>User Info</legend>
			<span><strong>UID:</strong> <em><?php echo get_me('user_id') ?></em></span><br/>
			<span><strong>Username:</strong> <em><?php echo get_me('user_name') ?></em></span><br/>
			<span><strong>Nickname:</strong> <em><?php echo config('mod.installed') ? get_me('user_nickname') : '(No for local account)' ?></em></span><br/>
			<span><strong>User Level:</strong> <em><?php echo get_me('user_level') ?></em></span><br/>
		</fieldset>
		<p><a href="<?php echo site_url('index.php?action=logout') ?>">Sign Out</a>?</p>
	<?php endif ?>
	<?php if(!config('mod.installed')): ?>
		<p>You haven't installed ModPHP into database, you can <a href="<?php echo site_url('install.php') ?>">click here</a> to start installing proccedure.</p>
	<?php endif ?>
	<footer>&copy;<?php echo date('Y') ?> <a href="http://modphp.hyurl.com/" target="_blank">ModPHP</a> <a href="http://modphp.hyurl.com/version"><?php echo MOD_VERSION ?></a></footer>
</body>
</html>
<?php } ?>