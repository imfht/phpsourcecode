<?php

	function get_info(){
		return  @file_get_contents('http://localhost/get_info.php');
	}
	
	$db_config_files = "App/Conf/db.php";	
	$config=include $db_config_files;
	$new_info=get_info();

if (isset($_POST["update"])){
	$db_host = $config["DB_HOST"];
	$db_user = $config["DB_USER"];
	$db_pass = $config["DB_PWD"];
	$db_dbname=$config["DB_NAME"];
	$db_tag = $config["DB_PREFIX"];
	$url_model=$config["URL_MODEL"];
	$version=$config['VERSION'];
	
	$config_str = "<?php\n";
	$config_str .= "return array(\n";
	$config_str .= "        'URL_MODEL'=>" . $url_model . ", // 运行环境不支持PATHINFO 请设置为0,\n";
	$config_str .= "        'DB_TYPE'=>'mysql',\n";
	$config_str .= "        'DB_HOST'=>'" . $db_host . "',\n";
	$config_str .= "        'DB_NAME'=>'" . $db_dbname . "',\n";
	$config_str .= "        'DB_USER'=>'" . $db_user . "',\n";
	$config_str .= "        'DB_PWD'=>'" . $db_pass . "',\n";
	$config_str .= "        'DB_PORT'=>'3306',\n";
	$config_str .= "        'DB_PREFIX'=>'" . $db_tag . "',\n";
	$config_str .= "        'VERSION'=>'" . $new_info . "',\n";
	$config_str .= "    );\n";
	echo($config_str);
	if (!@$link = mysql_connect($db_host, $db_user, $db_pass)) {//检查数据库连接情况
		echo "<meta charset='utf-8' />";
		echo "<script>\n
					window.onload=function(){
					alert('数据库连接失败! 请返回上一页检查连接参数');
					location.href='install.php';
				}
				</script>";
		die;
	} else {
		if(!mysql_select_db($db_dbname)){
			echo "<meta charset='utf-8' />";
			echo "<script>\n
						window.onload=function(){
						alert('请确认数据库是否存在? 请返回上一页检查连接参数');
						location.href='install.php';
					}
					</script>";
			die;
		}else{
			mysql_select_db($db_dbname);
			mysql_query("set names 'utf8'");
			$lines = file("Path/Sql/".$version."-".$new_info.".sql");
			$sqlstr = "";
			foreach ($lines as $line) {
				$line = trim($line);
				if ($line != "") {
					if (!($line{0} == "#" || $line{0} . $line{1} == "--")) {
						$sqlstr .= $line;
					}
				}
			}
			$sqlstr = rtrim($sqlstr, ";");
			$sqls = explode(";", $sqlstr);
			foreach ($sqls as $val) {
				$val = str_replace("`think_", "`" . $db_tag, $val);
				mysql_query($val);
			}

			$ff = fopen($db_config_files, "w ");
			fwrite($ff, $config_str);

			rename("update.php", "update.lock");

			echo "<meta charset='utf-8' />";
			echo "<script>\n
						window.onload=function(){
						alert('升级成功');
						location.href='index.php';
					}
					</script>";
			die;
		}
	}
}
?>
<!DOCTYPE html>
<html lang='zh-cn'>
	<head>
		<meta charset='utf-8' />
		<title>smeoa</title>
		<meta content='' name='description' />
		<meta content='' name='author' />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="Public/css/bootstrap.min.css" rel="stylesheet" >
		<link href="Public/css/style.css" rel="stylesheet">
	</head>
	<body>
		<div class="container">
			<div class="row">
				<div class="col-md-offset-2 col-md-8">
					<div class="page-header">
						<h1>小微OA系统升级程序</h1>
					</div>
					<form   method="POST" class="well form-horizontal">
						<div class="form-group">
							<p><h4 class="text-danger">* 升级之前请备份好数据</h4></p>
						</div>
						<div class="form-group">
							<label class="control-label col-md-4" for="name" >最新版本：</label>
							<div class="col-md-8">
								<p class="form-control-static"><?php	echo $new_info	?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-4" for="name" >当前版本：</label>
							<div class="col-md-8">
								<p class="form-control-static"><?php	echo $config['VERSION'];	?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-4" for="name" >1.下载升级包：</label>
							<div class="col-md-8">
								<p class="form-control-static">
									<a href="\Patch.zip" target="_blank">下载</a>
								</p>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-4" for="name" >2.解压升级包：</label>
							<div class="col-md-8">
								<p class="form-control-static">
									解压升级包到网站根目录
								</p>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-4" for="name" >3.在线升级：</label>
							<div class="col-md-8">
								<?php			
								if ($new_info!==$config['VERSION']) {
									echo "<button type=\"submit\" name=\"update\" class='btn btn-danger form-con'>升级</button>";
								} else {
									echo "<p class=\"form-control-static\">当前是最新版本</p>";
								}
								?>
							</div>
						</div>
						<hr></hr>
						<div class="form-group">
							<label class="control-label col-md-4" for="name" >主机：</label>
							<div class="col-md-8">
								<?php	echo $config['DB_HOST'];	?>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label  col-md-4" for="name">用户名：</label>
							<div class="col-md-8">
								<?php	echo $config['DB_USER'];	?>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-4" for="name">密码：</label>
							<div class="col-md-8">
								<?php	echo $config['DB_PWD'];	?>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-4" for="name">数据库名：</label>
							<div class="col-md-8">
								<?php	echo $config['DB_NAME'];	?>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-4" for="name">前缀：</label>
							<div class="col-md-8">
								<?php	echo $config['DB_PREFIX'];	?>
							</div>
						</div>
					</form>
				</div>
			</div>
	</body>
</html>
