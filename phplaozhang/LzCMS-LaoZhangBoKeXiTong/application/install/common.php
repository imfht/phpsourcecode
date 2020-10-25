<?php
function db_select($db,$conn){
	if(version_compare(phpversion(), '7.0.0') > -1){
		return $conn->select_db($db);
	}else{
		return mysql_select_db($db);
	}
}
function db_query($string,$conn){
	if(version_compare(phpversion(), '7.0.0') > -1){
		return $conn->query($string);
	}else{
		return mysql_query($string);
	}
}
function db_fetch_row($row){
	if(version_compare(phpversion(), '7.0.0') > -1){
		return $row->fetch_row();
	}else{
		return mysql_fetch_row($row);
	}
}
//测试链接数据库
function check_mysql()
{
	$is_connect = false;

	$db_host = $_GET['db_address'];
	$db_port = $_GET['db_port'];
	$db_name = $_GET['db_name'];
	$db_user = $_GET['db_user'];
	$db_pwd  = $_GET['db_pwd'];

	if($db_host != ''){
		if(function_exists('mysql_connect')){
			$conn = @mysql_connect($db_host.':'.$db_port,$db_user,$db_pwd);
		}
		if(version_compare(phpversion(), '7.0.0') > -1 && function_exists('mysqli_connect')){
			$conn = @mysqli_connect($db_host.':'.$db_port,$db_user,$db_pwd);
		}
	}

	if($conn)
	{
		// 严格模式
		$sql_string = "show variables like '%sql_mode%'";
		$sql_mode = @db_query($sql_string,$conn);
	 	$result =	db_fetch_row($sql_mode);
		if($result['1']==='STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'){
			$sql_status = "2";
		}else{
			$sql_status = "1";
		}

		if(!@db_select($db_name,$conn)){
			$sqlstr = "create database `{$db_name}`";
			if(!@db_query($sqlstr,$conn)){
				echo json_encode(array('status'=>0,'info'=>'创建数据库失败,请检查权限'));
				exit();
			}else{
				echo json_encode(array('status'=>1,'info'=>'创建数据库成功','sql_mode'=>$sql_status));
				exit();
			}
		}else{
			echo json_encode(array('status'=>1,'info'=>'连接数据库成功','sql_mode'=>$sql_status));
			exit();
		}
	}
	else
	{
		echo json_encode(array('status'=>0,'info'=>'数据库连接失败,请检查用户名和密码'));
	}
}

//$conn = mysql_connect("localhost","root","password") or die("无法连接数据库");
//    mysql_create_db("webjx") or die("无法创建数据库");
//    $sqlstr = "create database other_webjx";
//    mysql_query($sqlstr) or die("无法创建,一般请检查权限什么的");

//解析备份文件中的SQL
function parseSQL($fileName,$status = true){
	global $db_pre;
	$lines=file($fileName);
	$lines[0]=str_replace(chr(239).chr(187).chr(191),"",$lines[0]);//去除BOM头
	$flage=true;
	$sqls=array();
	$sql="";
	foreach($lines as $line)
	{
		$line=trim($line);
		$char=substr($line,0,1);
		if($char!='#' && strlen($line)>0)
		{
			$prefix=substr($line,0,2);
			switch($prefix)
			{
				case '/*':
				{
				$flage=(substr($line,-3)=='*/;'||substr($line,-2)=='*/')?true:false;
				break 1;
				}
				case '--': break 1;
				default :
				{
					if($flage)
					{
						$sql.=$line;
						if(substr($line,-1)==";")
						{
							$sql = str_replace('lz_',$db_pre,$sql);
							$sqls[]=$sql;
							$sql="";
						}
					}
					if(!$flage)$flage=(substr($line,-3)=='*/;'||substr($line,-2)=='*/')?true:false;
				}
			}
		}
	}
	return $sqls;
}
function execSQL($sqls,$conn){
	$flag=true;
	if(is_array($sqls))
	{
		$total = count($sqls);
		$num = 0;
		foreach($sqls as $sql)
		{
			if(substr($sql,0,7)=='REPLACE' && substr($sql,17,8)=='district'){
				//substr($sql,125);截取数据
				//str_replace('),',');',substr($sql,125));逗号改分号
				$districts = explode(';',str_replace('),',');',substr($sql,125)));//字符串转数组
				$limit = 1000;
				$page_total = (count($districts)/$limit);
				for ($page=0; $page < $page_total; $page++) {
					foreach ($districts as $key => $district) {
						if(($limit*($page+1)) > $key && ($limit*$page) <= $key){
							$district_page[] = $district;
						}
					}
					$district_page = implode(',', $district_page);
					if(substr($district_page,-1,1) == ','){
						$district_page = substr($district_page,0,strlen($district_page)-1);
					}
					if($district_page){
						$district_sqls[] = substr($sql,0,125).$district_page.';';
					}
					$district_page = '';
				}
				foreach ($district_sqls as $district_sql) {
					$result   = db_query($district_sql,$conn);
				}
			}else{
				$result   = db_query($sql,$conn);
			}
			if($flag) $num++;
			$percent = ($num/$total)*100;
			@sqlCallBack($sql,$result,$percent,$is_test);
		}
	}
	return $flag;
}

//sql回调函数
function sqlCallBack($sql,$result,$percent,$is_test = false){
	global $db_pre;
	if(preg_match_all("/(create|drop|insert|replace into)([^`]+`)(\w+)(`.*)/i",$sql,$out)){
		$sql = $out[1][0].$out[2][0].$per.$out[4][0].$out[5][0];
		$op = strtolower($out[1][0]);
		$message = '';
		//动作
		if($op=='create' && $sql)$message= "创建表 ".($out[3][0])." ";
		else if($op=='drop' && $sql)$message= "校验表 ".($out[3][0])." ";
		else if($op=='insert' && $sql)$message= "写入表 ".($out[3][0])." ";
		else if($op=='replace into' && $sql)$message= "写入表 ".($out[3][0])." ";
		//判断sql执行结果
		if($result){
			$isError  = false;
			$message .= '...成功';
		}else{
			$isError  = true;
			$message .= '...失败! '.mysql_error();
		}

		$percent = $percent == 100 ? 99 :sprintf("%.2d",$percent) ;
		$return_info = array(
			'isError' => $isError,
			'message' => $message,
			'percent' => $percent
		);
	}
	if($return_info){
		showProgress($return_info);
		usleep(1000);
	}
}

//安装mysql数据库
function install_sql(){
	global $db_pre;

	//安装配置信息
	$db_type      = 'mysql';
	$db_address   = $_GET['db_address'];
	$db_port      = $_GET['db_port'];
	$db_user      = $_GET['db_user'];
	$db_pwd       = $_GET['db_pwd'];
	$db_name      = $_GET['db_name'];
	$db_pre       = $_GET['db_pre'];

	$admin_user   = $_GET['admin_user'];
	$admin_pwd    = $_GET['admin_pwd'];
	//$admin_email  = $_GET['admin_email'];

	$site_name 	  = $_GET['site_name'];
	$site_keywords 	= $_GET['site_keywords'];
	$site_description    	= $_GET['site_description'];

	//链接mysql数据库
	$mysql_link = version_compare(phpversion(), '7.0.0') > -1 ? mysqli_connect($db_address.':'.$db_port,$db_user,$db_pwd) : @mysql_connect($db_address.':'.$db_port,$db_user,$db_pwd);
	if(!$mysql_link)
	{
		showProgress(array('isError' => true,'message' => 'mysql链接失败'.mysql_error()));
	}else{
		showProgress(array('isError' => false,'message' => '连接数据库成功'));
	}

	//检测SQL安装文件
	$sql_file = APP_PATH.'/install/sql/table.sql';
	if(!file_exists($sql_file))
	{
		showProgress(array('isError' => true,'message' => '安装的SQL文件'.basename($sql_file).'不存在'));
	}else{
		showProgress(array('isError' => false,'message' => '解析SQL文件'));
	}
	//执行SQL,创建数据库操作
	db_query("set names 'UTF8'",$mysql_link);

	if(!@db_select($db_name,$mysql_link))
	{
		$DATABASESQL = '';
		if(version_compare(mysql_get_server_info(), '4.1.0', '>='))
		{
	    	$DATABASESQL = "DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
		}
		if(!db_query('CREATE DATABASE `'.$db_name.'` '.$DATABASESQL,$mysql_link))
		{
			showProgress(array('isError' => true,'message' => '用户权限受限，创建'.$db_name.'数据库失败，请手动创建数据表'));
		}
	}

	if(!@db_select($db_name,$mysql_link))
	{
		showProgress(array('isError' => true,'message' => $db_name.'数据库不存在'.mysql_error()));
	}

	//安装SQL
	$sqls = parseSQL($sql_file);
	execSQL($sqls,$mysql_link);

	//安装地区数据
	$sql_data_file = APP_PATH.'/install/sql/data.sql';
	if(!file_exists($sql_data_file))
	{
	}else{
		showProgress(array('isError' => false,'message' => '正在加载必要数据','percent' => 90));
		$sqls = parseSQL($sql_data_file);
		execSQL($sqls,$mysql_link);
		showProgress(array('isError' => false,'message' => '更新必要数据成功','percent' => 99));
	}


	//写入数据库配置文件
	$configDefFile =  <<<EOF
<?php
return array(
	'type'   =>'{DB_TYPE}',
	'hostname'   =>'{DB_HOST}',
	'hostport'   =>'{DB_PORT}',
	'database'   =>'{DB_NAME}',
	'username'   =>'{DB_USER}',
	'password'   =>'{DB_PWD}',
	'prefix'     =>'{DB_PREFIX}',
	'charset'    => 'utf8',
	'resultset_type' => 'array',
	'fields_strict'  => true,
);
EOF;
	$configFile    = APP_PATH.'database.php';
	$updateData    = array(
		'{DB_TYPE}'		=> $db_type,
		'{DB_PREFIX}' 	=> $db_pre,
		'{DB_HOST}' 	=> $db_address,
		'{DB_PORT}' 	=> $db_port,
		'{DB_USER}'    	=> $db_user,
		'{DB_PWD}'     	=> $db_pwd,
		'{DB_NAME}'    	=> $db_name,
	);
	$is_success = create_config($configFile,$configDefFile,$updateData);
	if(!$is_success)
	{
		showProgress(array('isError' => true,'message' => '更新数据库配置文件失败','percent' => 99));
	}else{
		showProgress(array('isError' => false,'message' => '更新数据库配置文件成功','percent' => 99));
	}

	//插入管理员数据
	$adminSql = 'insert into `'.$db_pre.'admin` (`username`,`password`) values ("'.$admin_user.'","'.md5($admin_pwd).'")';
	if(!db_query($adminSql,$mysql_link))
	{
		showProgress(array('isError' => true,'message' => '创建管理员失败'.$adminSql.mysql_error(),'percent' => 99));
	}else{
		showProgress(array('isError' => false,'message' => '创建管理员成功','percent' => 99));
	}

	//插入站点数据
	$seos = array();
	$seos['title_add'] = '一个PHP程序员的个人博客系统';
	$seos['keywords'] = $site_keywords;
	$seos['description'] = $site_description;

	$adminSql = 'replace into `'.$db_pre.'setting` VALUES
	(\'site_name\',\''.$site_name.'\'),
	(\'title_add\',\''.$seos['title_add'].'\'),
	(\'keywords\',\''.$seos['keywords'].'\'),
	(\'description\',\''.$seos['description'].'\')';
	if(!db_query($adminSql,$mysql_link))
	{
		showProgress(array('isError' => true,'message' => '更新站点配置失败'.$adminSql.mysql_error(),'percent' => 99));
	}else{
		showProgress(array('isError' => false,'message' => '更新站点配置成功','percent' => 99));
	}


	//设置session cookies前缀

	/*$g_config = file_get_contents(CONF_PATH.'config.php');
	$g_config = preg_replace('/(\'SESSION_PREFIX\'.+?=>)(.+?),/', "$1'".generate_password(5)."_'," ,$g_config);
	$g_config = preg_replace('/(\'COOKIE_PREFIX\'.+?=>)(.+?),/', "$1'".generate_password(5)."_'," ,$g_config);
	$g_config = preg_replace('/(\'AUTHKEY\'.+?=>)(.+?),/', "$1'".generate_password(24)."'," ,$g_config);

	file_put_contents(CONF_PATH.'config.php',$g_config);*/

	$result = file_get_contents('http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].url('install/index/clear_cache'));

	showProgress(array('isError' => false,'message' => '更新站点缓存成功','percent' => 99));

	//执行完毕
	showProgress(array('isError' => false,'message' => '安装完成','percent' => 100,'admin_user'=>$admin_user));
	exit;

}

//输出json数据
function showProgress($return_info)
{
	echo '<script type="text/javascript">parent.update_progress('.json_encode($return_info).');</script>';
	flush();
	if($return_info['isError'] == true)
	{
		exit;
	}
}

//根据默认模板生成config文件
function create_config($config_file,$config_def_file,$updateData)
{
	$defaultData = $config_def_file;
	$configData  = str_replace(array_keys($updateData),array_values($updateData),$defaultData);
	$file = fopen($config_file,"w");
	fwrite($file,$configData);
	fclose($file);
	return true;
}

/*
 * 生成随机密码
 */
function generate_password( $length = 8 ) {
    $chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';
    $password = '';
    for ($i = 0; $i < $length; $i++){
        $password .= $chars[mt_rand(0, strlen($chars) - 1)];
    }
    return $password;
}

//查询解决方案
function configInfo($item)
{
	$data = array(
		'mysql'=> 'http://www.baidu.com/#wd=php%20mysql%E6%89%A9%E5%B1%95&rsv_spt=1&rsv_bp=1&ie=utf-8&tn=baiduhome_pg&inputT=4031&f=8&bs=php%20mysql%E7%BB%84%E4%BB%B6&rsv_sug3=16&rsv_sug4=653&rsv_sug1=22&rsv_sug2=0&rsv_sug=2',
		'gd'=> 'http://www.baidu.com/#wd=php%20%E5%BC%80%E5%90%AF%20gd&rsv_spt=1&rsv_bp=1&ie=utf-8&tn=baiduhome_pg&inputT=1513&f=8&bs=php%20gd&rsv_sug3=23&rsv_sug4=914&rsv_sug1=34&rsv_sug2=0',
		'xml'=> 'http://www.baidu.com/#wd=php%20%E5%BC%80%E5%90%AF%20xml&rsv_spt=1&rsv_bp=1&ie=utf-8&tn=baiduhome_pg&inputT=1262&f=8&bs=php%20%E5%BC%80%E5%90%AF%20gd&rsv_sug3=27&rsv_sug4=1014&rsv_sug1=36&rsv_sug2=0&rsv_sug=1',
		'session'=> 'http://www.baidu.com/#wd=php%20%E5%BC%80%E5%90%AF%20session&rsv_spt=1&rsv_bp=1&ie=utf-8&tn=baiduhome_pg&inputT=7586&f=8&bs=php%20%E5%BC%80%E5%90%AF%20xml&rsv_sug3=34&rsv_sug4=1245&rsv_sug1=47&rsv_sug2=0',
		'iconv'=> 'http://www.baidu.com/#wd=php%20%E5%BC%80%E5%90%AF%20iconv&rsv_spt=1&rsv_bp=1&ie=utf-8&tn=baiduhome_pg&inputT=878&f=8&bs=php%20%E5%BC%80%E5%90%AF%20session&rsv_sug3=36&rsv_sug4=1315&rsv_sug1=49&rsv_n=2&rsv_sug=1',
		'zip'=> 'http://www.baidu.com/#wd=php%20%E5%BC%80%E5%90%AF%20zip&rsv_spt=1&rsv_bp=1&ie=utf-8&tn=baiduhome_pg&inputT=1823&f=8&bs=php%20%E5%BC%80%E5%90%AF%20iconv&rsv_sug3=43&rsv_sug4=1506&rsv_sug1=54&rsv_sug=2&rsv_sug2=0',
		'curl'=> 'http://www.baidu.com/#wd=php%20%E5%BC%80%E5%90%AF%20curl&rsv_spt=1&rsv_bp=1&ie=utf-8&tn=baiduhome_pg&inputT=886&f=8&bs=php%20%E5%BC%80%E5%90%AF%20zip&rsv_sug3=45&rsv_sug4=1587&rsv_sug1=58&rsv_n=2',
		'OpenSSL'=> 'http://www.baidu.com/#wd=php%20%E5%BC%80%E5%90%AF%20OpenSSL&rsv_spt=1&rsv_bp=1&ie=utf-8&tn=baiduhome_pg&inputT=909&f=8&bs=php%20%E5%BC%80%E5%90%AF%20curl&rsv_sug3=47&rsv_sug4=1667&rsv_sug1=61&rsv_n=2',
		'sockets'=> 'http://www.baidu.com/#wd=php%20%E5%BC%80%E5%90%AF%20sockets&rsv_spt=1&rsv_bp=1&ie=utf-8&tn=baiduhome_pg&inputT=862&f=8&bs=php%20%E5%BC%80%E5%90%AF%20OpenSSL&rsv_sug3=50&rsv_sug4=1767&rsv_sug1=63&rsv_n=2&rsv_sug=1',
		'safe_mode'=> 'http://www.baidu.com/#wd=php%20safe_mode%20%E5%85%B3%E9%97%AD&rsv_spt=1&rsv_bp=1&ie=utf-8&tn=baiduhome_pg&inputT=885&f=8&bs=php%20safe_mode%20%E5%85%B3%E9%97%AD&rsv_sug=1&rsv_sug3=7&rsv_sug4=237&rsv_sug1=11&rsv_n=2',
		'allow_url_fopen'=> 'http://www.baidu.com/#wd=php%20%E5%BC%80%E5%90%AF%20allow_url_fopen&rsv_spt=1&rsv_bp=1&ie=utf-8&tn=baiduhome_pg&inputT=1088&f=8&bs=php%20%E5%BC%80%E5%90%AF%20sockets&rsv_sug3=52&rsv_sug4=1844&rsv_sug1=65&rsv_n=2&rsv_sug=1',
		'memory_limit'=> 'http://www.baidu.com/#wd=php%20%E5%BC%80%E5%90%AF%20memory_limit&rsv_spt=1&rsv_bp=1&ie=utf-8&tn=baiduhome_pg&inputT=2508&f=8&bs=php%20%E5%BC%80%E5%90%AF%20allow_url_fopen&rsv_sug3=54&rsv_sug4=1921&rsv_sug1=69&rsv_n=2&rsv_sug=1',
		'asp_tags'=> 'http://www.baidu.com/#wd=asp_tags%20%E5%85%B3%E9%97%AD&rsv_spt=3&rsv_bp=1&ie=utf-8&tn=baiduhome_pg&inputT=1244&f=8&bs=php%20asp_tags%20%E5%85%B3%E9%97%AD&rsv_sug3=69&rsv_sug4=2382&rsv_sug1=75&rsv_sug=1&rsv_sug2=0',
	);

	if(isset($data[$item]))
	{
		return "<a href='".$data[$item]."' target='_blank'>立即解决</a>";
	}
}