<?php

ini_set('date.timezone','Asia/Chongqing');

error_reporting(E_STRICT);
define('INSTALL_STATUS', 1);
header('Content-Type:text/html;charset=UTF-8');

include 'inc/install.lang.php';
if (file_exists('install.lock')) {
	exit($lang['install_is_lock']);
}
$step = isset($_GET['step'])? intval($_GET['step']) : 1;


if (empty($step)) {
	$step = 1;
} 
switch ($step ) {
	case 1:
		$license = file_get_contents("./license.txt");
		require 'tpl/step_1.php';
		break;
	case 2:
		/* 环境 */
		$software = explode('/',$_SERVER["SERVER_SOFTWARE"]);
		$os_software = '<span class="ok">'.PHP_OS.'<br />'.$software[0].'/'.str_replace('PHP', '', $software[1]).'</span>';
		/* phpversion */
		$phpversion = phpversion();
		$lowest = '5.2.5';
		if (intval($phpversion)-intval($lowest) >=0) {
			$environment_phpversion = '<span class="ok">'.$phpversion.'</span>';
		} else {
			exit('系统安装要求：PHP版本最低不能低于'.$lowest);
			$environment_phpversion = '<span class="no red">&nbsp;</span>';
		}
		/* mysql */
		if (function_exists('mysql_connect')) {
			$environment_mysql = '<span class="ok">开启</span>';
		} else {
			$environment_mysql = '<span class="no red">&nbsp;</span>';
		}

		/* session_start */
		if (function_exists('session_start')) {
			$environment_session = '<span class="ok">开启</span>';
		} else {
			$environment_session = '<span class="no red">'. $lang['unsupport'] .'</span>';
		}
		/* uploads */
		$environment_upload = ini_get('file_uploads') ? '<span class="ok">'.ini_get('upload_max_filesize').'</span>' : '<span class="no red">&nbsp;</span>';
		
		/* iconv */
		if(function_exists('iconv')){
            $environment_iconv = '<span class="ok">'.$lang['support'].'</span>';
        }else{
            $environment_iconv = '<span class="no red">'. $lang['unsupport'] .'</span>';
        }
        /* GD */
        if(extension_loaded('gd')) {
            $environment_gd = '<span class="ok">'.$lang['support'].'</span>';
        }else{
            $environment_gd = '<span class="no red">'. $lang['unsupport'] .'</span>';
        }

        /* mbstring */
        if(extension_loaded('mbstring')) {
            $environment_mb = '<span class="ok">'.$lang['support'].'</span>';
        }else{
            $environment_mb = '<span class="no red">'. $lang['unsupport'] .'</span>';
        }



		/* file chmod */
		$file = array(
			'/',
			'/Install',
			'/uploads',
			'/App/Conf',
			'/Config',
		);
		require 'tpl/step_2.php';
		break;
	case 3:
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (empty($_POST['DB_HOST'])) {
				exit(json_encode(array('status'=>'error','info'=>'请填写数据库服务器！','input'=>'DB_HOST')));
			}
			if (empty($_POST['DB_PORT'])) {
				exit(json_encode(array('status'=>'error','info'=>'请填写数据库端口！','input'=>'DB_PORT')));
			}
			if (empty($_POST['DB_USER'])) {
				exit(json_encode(array('status'=>'error','info'=>'请填写数据库用户名！','input'=>'DB_USER')));
			}
			if (empty($_POST['DB_NAME'])) {
				exit(json_encode(array('status'=>'error','info'=>'请填写数据库名！','input'=>'DB_NAME')));
			}
			if (empty($_POST['DB_PREFIX'])) {
				exit(json_encode(array('status'=>'error','info'=>'请填写数据库服表前缀！','input'=>'DB_PREFIX')));
			}
			if (empty($_POST['HD_weburl'])) {
				exit(json_encode(array('status'=>'error','info'=>'请填写网站网址！','input'=>'HD_weburl')));
			}
			if (empty($_POST['username'])) {
				exit(json_encode(array('status'=>'error','info'=>$lang['install_founder_name_empty'],'input'=>'username')));
			}else $_POST['username'] = 'admin';
			if (empty($_POST['password'])) {
				exit(json_encode(array('status'=>'error','info'=>$lang['founder_invalid_password'],'input'=>'password')));
			}
			if (strlen($_POST['password']) < 6) {
				exit(json_encode(array('status'=>'error','info'=>$lang['founder_invalid_password'],'input'=>'password')));
			}
			if (!filter_var($_POST['HD_email'], FILTER_VALIDATE_EMAIL)) {
				exit(json_encode(array('status'=>'error','info'=>'E-mail格式不正确！','input'=>'HD_email')));
			}
			$connect = mysql_connect($_POST['DB_HOST'],$_POST['DB_USER'],$_POST['DB_PWD']);
			if (!$connect) {
				exit(json_encode(array('status'=>'error','info'=>'数据库连接失败，错误信息'.mysql_error($connect))));
			}
			if (!mysql_select_db($_POST['DB_NAME'])) {
				$result = mysql_query("CREATE DATABASE `".$_POST['DB_NAME']."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;");
				if (!$result) {
					exit(json_encode(array('status'=>'error','info'=>'数据库创建失败，错误信息'.mysql_error($connect),'input'=>'DB_NAME')));
				}
			}
// 				右边的/一定要去除
			$_POST['HD_weburl'] = rtrim($_POST['HD_weburl'],'/');
			$_POST['add_test'] = isset($_POST['add_test']) ? intval($_POST['add_test']) : 0;
			$content = var_export($_POST,true);
			if (file_put_contents('temp.php',"<?php\r\nreturn " .$content."\r\n?>")) {
				exit(json_encode(array('status'=>'success')));
			} else {
				exit(json_encode(array('status'=>'error','info'=>'写入临时文件失败！')));
			}
		} else {
			 if(!empty($_SERVER['REQUEST_URI']))
		    	$scriptName = $_SERVER['REQUEST_URI'];
		    else
		    	$scriptName = $_SERVER['PHP_SELF'];

		    $basepath = preg_replace("#\/install(.*)$#i", '', $scriptName);

		    if(!empty($_SERVER['HTTP_HOST']))
		        $baseurl = 'http://'.$_SERVER['HTTP_HOST'];
		    else
		        $baseurl = "http://".$_SERVER['SERVER_NAME'];
		    $weburl = rtrim("http://".$_SERVER['SERVER_NAME'].$basepath,'/');
		
			require 'tpl/step_3.php';
			}
			break;
	case 4:
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {			
			$setting = include './temp.php';
			$datafile = $setting['add_test'] == 1 ? './inc/webdata.sql' : './inc/web.sql';

			if (empty($setting)) {				
				exit(json_encode(array('status'=>'error','info'=>'加载文件失败，请重新安装！')));
			}

			if ($setting['add_test'] == 1) {
				//判断是否是网站根目录
				if(!empty($_SERVER['REQUEST_URI']))
			    	$scriptName = $_SERVER['REQUEST_URI'];
			    else
			    	$scriptName = $_SERVER['PHP_SELF'];

			    $basepath = preg_replace("#\/install(.*)$#i", '', $scriptName);

			} else {
				//删除临时图片
				delDirAndFile('../uploads/article', false);
				delDirAndFile('../uploads/product', false);
			}

			$connect = mysql_connect($setting['DB_HOST'],$setting['DB_USER'],$setting['DB_PWD']);
			if (!$connect) {
				exit(json_encode(array('status'=>'error','info'=>'数据库连接失败，错误信息'.mysql_error($connect))));
			} 
			if (!mysql_select_db($setting['DB_NAME'],$connect)) {
				exit(json_encode(array('status'=>'error','info'=>'选择数据库失败，错误信息'.mysql_error($connect))));
			}

			mysql_query("SET NAMES UTF8");

			$info = '';

			$sql = "";

			$file = @fopen($datafile, "r");

			if ($file) {

				while(!feof($file)){

					$tem = trim(fgets($file));

					//过滤,去掉空行、注释行(#,--)
					if(empty($tem) || $tem[0] == '#' || ($tem[0] == '-' && $tem[1] == '-')) continue;

					//统计一行字符串的长度
					$end = strlen($tem) - 1;
					
					$sql .= $tem;

					//检测一行字符串最后有个字符是否是分号，是分号则一条sql语句结束，否则sql还有一部分在下一行中
					if($tem[$end] == ";"){

						$sql = str_replace('hd_', $setting['DB_PREFIX'], $sql);

						if(!mysql_query($sql)) {
							$info .="下面语句执行错误：<br>".$sql."<br>";
							$status = 'error';
							exit(json_encode(array('status'=>$status,'info'=>$info)));
						}

						$sql = "";

					}

				}
				
				fclose($file);

				$status = 'success';
				$info .= '成功安装<br/>';

			}else {
				$status = 'error';
				$info .= '安装失败，错误信息'.mysql_error().'<br/>';
				//错误直接返回
				exit(json_encode(array('status'=>$status,'info'=>$info)));
			}

			//添加管理员
			$time = date('Y-m-d H:i:s');
			$ip = getip();

			$password = get_password($setting['password']);

			$result = mysql_query("INSERT INTO `{$setting['DB_PREFIX']}user` (`name`,`email`,`password`,`addtime`,`lastlogin`,`lastip`,`status`) VALUES ('{$setting['username']}','{$setting['HD_email']}','$password','$time','$time','$ip',1);");
			$insertId = mysql_insert_id();
			if (!$result || !$insertId) {
				exit(json_encode(array('status'=>'error','info'=>'创建管理员失败，错误信息'.mysql_error().'，请重新刷新安装！')));
			}

			/* 保存install记录,如果删除则得不到最新的更新提示 */
			//@file_get_contents('http://www.hidoger.com/CWS/install/HD_email/'.base64_encode($setting['HD_email']));
			
			$status = 'success_all';
			$info .='HDCWS已成功安装！';

			exit(json_encode(array('status'=>$status,'info'=>$info,'num'=>$forNum)));
		} 
		require 'tpl/step_4.php';
		break;
	case 5:
		$setting = require './temp.php';
		/* 修改配置文件 */
		//定义数组
		$db = array('DB_TYPE' => 'mysql',
			'DB_HOST' => $setting['DB_HOST'],
			'DB_USER' => $setting['DB_USER'],
			'DB_PWD' => $setting['DB_PWD'],
			'DB_NAME' => $setting['DB_NAME'],
			'DB_PREFIX' => $setting['DB_PREFIX'],
			);

		$cookie_code = get_randomstr(9);

		$dbStr="<?php return " . var_export($db,true) . ";?>";			
		file_put_contents('../Config/config.db.php',$dbStr);//写文件

		$web = include_once('../Config/config.web.php');
		$web['HD_webname'] = $setting['HD_webname'];
		$web['HD_weburl'] = $setting['HD_weburl'];
		$web['HD_email'] = $setting['HD_email'];	
		$webStr="<?php return " . var_export($web,true) . ";?>";
		file_put_contents('../Config/config.web.php', $webStr);
		
		
		//删除临时文件
		@unlink('temp.php');
		//删除缓存
		delDirAndFile('../App/Runtime',false);
		/* 设置安装完成文件 */
		file_put_contents('install.lock', $time);
		require 'tpl/step_5.php';
		break;
		default:
		require 'tpl/step_1.php';
}


function getip(){
	if(isset ($_SERVER['HTTP_X_FORWARDED_FOR'])){
		$onlineip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}elseif(isset ($_SERVER['HTTP_CLIENT_IP'])){
		$onlineip = $_SERVER['HTTP_CLIENT_IP'];
	}else{
		$onlineip = $_SERVER['REMOTE_ADDR'];
	}
	$onlineip = preg_match('/[\d\.]{7,15}/', addslashes($onlineip), $onlineipmatches);
	return $onlineipmatches[0] ? $onlineipmatches[0] : 'unknown';
}

function format_textarea($string) {
	$chars = 'utf-8';
	return nl2br(str_replace(' ', '&nbsp;', htmlspecialchars($string,ENT_COMPAT,$chars)));
}



/**
 * 对用户的密码进行加密
 * @param $password
 * @return password
 */
function get_password($password, $encrypt='') {
    return md5(trim($password));
}

/**
 * 生成随机字符串
 */
function get_randomstr($length = 6) {
	$chars = '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ';
	$hash = '';
    $max = strlen($chars) - 1;
    for($i = 0; $i < $length; $i++) {
        $hash .= $chars[mt_rand(0, $max)];
    }
    return $hash;
}



//循环删除目录和文件函数
function delDirAndFile($dirName, $bFlag = true ) {
	if ( $handle = opendir( "$dirName" ) ) {
		while ( false !== ( $item = readdir( $handle ) ) ) {
			if ( $item != "." && $item != ".." ) {
				if ( is_dir( "$dirName/$item" ) ) {
					delDirAndFile( "$dirName/$item" );
				} else {
					@unlink( "$dirName/$item" );
				}
			}
		}
		closedir( $handle );
		if($bFlag) rmdir($dirName);
	}
}

?>