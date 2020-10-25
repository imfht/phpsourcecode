<?php
@header ( "Content-type: text/html; charset=utf-8" );

if ($_POST) {
	$host = $_POST ['host'];
	$port = "3306";
	$username = $_POST ['username'];
	$dbname = $_POST ['dbname'];
	$passwd = $_POST ['password'];

	try {
		$pdo = new PDO ( "mysql:host=$host;port=$port", $username, $passwd );
	} catch ( PDOException $e ) {
		echo $e->getMessage ();
		return;

	}
	$sql1 = "SELECT * FROM information_schema.SCHEMATA where SCHEMA_NAME='" . $dbname . "'";

	$isExist = $pdo->query ( $sql1 )->fetch ();

	if ($isExist) {
	  //echo "dbname is aready exsits! 数据库已存在!";
	 // exit();
	}


	$sql = "CREATE DATABASE IF NOT EXISTS $dbname DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
	$pdo->exec ( $sql );
	$pdo = null;
	$pdo = new PDO ( "mysql:host=$host;dbname=$dbname", $username, $passwd, array (PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'' ) );

	createTables ( $pdo );

	//修改自己
	writeConfig ( $_POST );
	rename ( "./install.php", "./install.php.bak" );

	echo "恭喜你,安装成功,祝你使用愉快<br>";
	echo "请删除根目录下的install.php.bak和数据库文件<br>";
	echo "<a href='./index.php?anonymous/admin'>后台登录</a>";
	exit ();
}

function writeConfig($config) {
	$stringConfig = "<?php return array(
        'type' => 'mysql',
        'host' => '$config[host]',
        'username' => '$config[username]',
        'password' => '$config[password]',
        'dbname' => '$config[dbname]',
        'port' => 3306,
        'charset' => 'UTF8',
        'presistent' => false,
        'debug' => false); ?>";

	$file = "./config/database.local.php";
	$handle = fopen ( $file, "w" );
	fwrite ( $handle, $stringConfig );
	fclose ( $handle );

}

/**
 * 创建数据表
 * @param  resource $db 数据库连接资源
 */
function createTables($db) {

	$file = getcwd () . "/wcms.sql";

	$handle = fopen ( $file, "r" );
	$sql = fread ( $handle, filesize ( $file ) );
	fclose ( $handle );

	if (! $sql) {
		echo "读取失败";
		exit ();
	}

	//读取SQL文件
	$sql = str_replace ( "\r", "\n", $sql );
	$sql = explode ( ";\n", $sql );

	//开始安装
	foreach ( $sql as $value ) {
		$value = trim ( $value );
		if (empty ( $value ))
			continue;
		if (substr ( $value, 0, 12 ) == 'CREATE TABLE') {
			$name = preg_replace ( "/^CREATE TABLE `(\w+)` .*/s", "\\1", $value );
			//echo "创建数据表{$name}";
			if (false !== $db->exec ( $value )) {
				//	echo "成功<br>";
			} else {
				//	echo "失败<br>";
			}
		} else {
			$db->exec ( $value );
		}

	}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>WCMS安装程序</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">

<!-- Le styles -->
<link href="./static/bootstrap2/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
	padding-top: 60px;
	/* 60px to make the container go all the way to the bottom of the topbar */
}
</style>
<link href="./static/bootstrap2/css/bootstrap-responsive.css"
	rel="stylesheet">

<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
      <script src="../assets/js/html5shiv.js"></script>
    <![endif]-->

<!-- Fav and touch icons -->
<link rel="apple-touch-icon-precomposed" sizes="144x144"
	href="../assets/ico/apple-touch-icon-144-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114"
	href="../assets/ico/apple-touch-icon-114-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="72x72"
	href="../assets/ico/apple-touch-icon-72-precomposed.png">
<link rel="apple-touch-icon-precomposed"
	href="../assets/ico/apple-touch-icon-57-precomposed.png">
<link rel="shortcut icon" href="../assets/ico/favicon.png">
<style type="text/css">
body {
	padding-top: 40px;
	padding-bottom: 40px;
	background-color: #f5f5f5;
}

.form-signin {
	max-width: 300px;
	padding: 19px 29px 29px;
	margin: 50px auto 20px;
	background-color: #fff;
	border: 1px solid #e5e5e5;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
	-webkit-box-shadow: 0 1px 2px rgba(0, 0, 0, .05);
	-moz-box-shadow: 0 1px 2px rgba(0, 0, 0, .05);
	box-shadow: 0 1px 2px rgba(0, 0, 0, .05);
}

.form-signin .form-signin-heading,.form-signin .checkbox {
	margin-bottom: 10px;
}

.form-signin input[type="text"],.form-signin input[type="password"] {
	font-size: 16px;
	height: auto;
	margin-bottom: 15px;
	padding: 7px 9px;
}
</style>
</head>

<body>

<div class="navbar navbar-inverse navbar-fixed-top">
<div class="navbar-inner">
<div class="container">
<button type="button" class="btn btn-navbar" data-toggle="collapse"
	data-target=".nav-collapse"><span class="icon-bar"></span> <span
	class="icon-bar"></span> <span class="icon-bar"></span></button>
<a class="brand" href="#">WCMS</a>
<div class="nav-collapse collapse"></div>
<!--/.nav-collapse --></div>
</div>
</div>







<div class="container">

<form class="form-signin" name="install" method="post"
	action="./install.php">
<h2 class="form-signin-heading">开始安装</h2>
主机名<input type="text" name="host" class="input-block-level"
	value="localhost"> 数据库名<input type="text" name="dbname"
	class="input-block-level" value="wcms"> 用户名<input type="text"
	name="username" class="input-block-level" value="root"> 数据库密码<input
	type="text" name="password" class="input-block-level" placeholder=""> <input
	class="btn btn-large btn-primary" type="submit" value="安装"></form>

</div>
<!-- /container -->

<!-- Le javascript
    ================================================== -->


</body>
</html>
