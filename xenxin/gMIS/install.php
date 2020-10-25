<?php

/*
 * This is an all-in-one script for -gMIS live installation.
 * wadelau@{ufqi,hotmail,gmail}.com
 * Sat Apr 16 10:15:04 CST 2016
 * 13:12 Friday, November 22, 2019, updt create table 
 * v0.1
 */

## section-0, functions

date_default_timezone_set('Asia/Hong_Kong') ;

# create a form output
function xForm($nextact, $fields, $isfile=0){
	$rd = rand(100, 99999);
	$form_header = "\n<br/><div id='formdiv".$rd."'><form name=\"myform$rd\" id=\"myform$rd\" action=\"".$nextact."\" method=\"post\" ";
	if($isfile == 1){
		$form_header .= " enctype=\"multipart/form-data\"";	
	}
	else{
		$form_header .= " enctype=\"application/x-www-form-urlencoded\"";	
	}
	$form_header .= ">";
	$form_footer = "<br/><br/><input type='submit' name='mysubmit' value='确定&下一步' />";
	$form_footer .= "&nbsp;&nbsp;<input name='myback' type='button' value='取消&返回' onclick='javascript:window.history.back(-1);' />";
	$form_footer .= "</form></div>";
	
	$form_field = '';
	$xform = '';
	if(is_array($fields)){
		foreach($fields as $k=>$v){
			$dispname = '';
			$form_field = '<input name="'.$k.'" id="'.$k.'"';
			if(is_array($v)){
				foreach($v as $k1=>$v1){
					if($k1 == 'dispname'){ $dispname = $v1; }
					else{ $form_field .= " $k1='$v1'"; }
				}
			}
			else{
				print __FILE__.": illegal setting. 1604161129.";	
			}
			$form_field = "\n<br/><br/>".$dispname.": ".$form_field." />";
			$xform .= $form_field;
		}		
	}
	else if($fields == null){
		# no extra field.	
	}
	else{
		print __FILE__.": illegal setting. 1604161130.";	
	}

	#return $form_header.$form_field.$form_footer;  
	return $form_header.$xform.$form_footer;  

}

/** 
 * URL redirect, remedy by wadelau@ufqi.com 09:52 Tuesday, November 24, 2015
 */
function redirect($url, $time=0, $msg='') {
    $url = str_replace(array("\n", "\r"), '', $url);
	if(!inString('://', $url)){ # relative to absolute path
		$url = "//".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].$url;
	}
	if($time < 100){ $time = $time * 1000; } # in case of milliseconds
	$hideMsg = "<!DOCTYPE html><html><head><meta http-equiv=\"content-type\" content=\"text/html;charset=utf-8\" />";
	$hideMsg .= "<meta http-equiv=\"refresh\" content=\"{$time};URL='{$url}'\" />";
	$hideMsg .= "</head><body>";  # remedy Mon Nov 23 22:03:24 CST 2015
    if(empty($msg)){
		$hideMsg = $hideMsg." <a href=\"".$url."\">系统将在{$time}秒之后自动跳转</a> <!-- {$url}！--> ...";
	}
	else{
		$hideMsg = $hideMsg . $msg;
	}
	$hideMsg .= "<script type='text/javascript'>window.setTimeout(function(){window.location.href='".$url."';}, ".$time.");</script>";
	$hideMsg .= "</body></html>";
    if(!headers_sent()) {
        // redirect
        if (0 === $time) {
            header("Location: " . $url);
			print $hideMsg;
        } 
        else{
        	print $hideMsg;
        }
        exit();
    } 
    else{
        print $hideMsg;
        exit();
    }

}

# check a needle in a haystack
function inString($needle, $haystack){

    $pos = stripos($haystack, $needle);
    return ($pos === false ? false : true);

}

# get position by value
function getSerialByVal($needle, $haystack){

	$pos = 0;
	foreach($haystack as $k=>$v){
		if($v == $needle){
			break;	
		}
		else{
			$pos++;	
		}
	}
	return $pos;

}

# check dir read & writeable
function dirWriteable($dir) {

	$writeable = 0;
	if(!is_dir($dir)) {
		@mkdir($dir, 0777);
	}
	if(is_dir($dir)) {
		if($fp = @fopen("$dir/test.txt", 'w')) {
			@fclose($fp);
			@unlink("$dir/test.txt");
			$writeable = 1;
		} 
		else {
			$writeable = 0;
		}
	}

	return $writeable;

}

# exec in bg
function execInBackground($cmd) { 
	
	#print "cmd:[$cmd]";

	if (substr(php_uname(), 0, 7) == "Windows"){ 
		pclose(popen("start /B ". $cmd, "r"));  
	} 
	else { 
		exec($cmd . " > /dev/null &");   
	} 

	sleep(1);

	return 0;

} 

# replace string in file
function replaceInFile($myFile, $oldStr, $newStr){
	
	$cmd = "perl -i.orig.php -p -e 's/$oldStr/$newStr/gi' '$myFile'";
	#print $cmd;
	execInBackground($cmd);

	return 0;

}


## section-1, pre-actions

$out = '';
$sid = $_REQUEST['sid'] == '' ? rand(100, 999999) :  $_REQUEST['sid'] ;
$reqProto = $_SERVER['HTTPS'] == '' ? "http" : "https";
$srvName = ((strlen($_SERVER['SERVER_NAME']) > 3 && $_SERVER['SERVER_NAME'] != '127.0.0.1') ? $_SERVER['SERVER_NAME'] : $_SERVER['SERVER_ADDR']); # shortest domain, a.bc, localhost => 1.2.3.4
$file = $reqProto."://".$srvName.":".$_SERVER['SERVER_PORT'].$_SERVER['PHP_SELF']."?sid=".$sid;


$docroot = $_SERVER['DOCUMENT_ROOT'];
$rtvdir = dirname(__FILE__); # relative dir
$rtvdir = str_replace($docroot,"", $rtvdir);
$appdir = $docroot.$rtvdir;
if($rtvdir == ''){
	$rtvdir = "./";
}

#print "docroot:[$docroot] rtvdir:[$rtvdir] appdir:[$appdir]";

$steps = array('', 'dolicense', 'env', 'getsrc', 'db', 'init', 'finalize');
$step = $_REQUEST['step'];
$istep = $_REQUEST['istep']; # sub step in a step
$percent = ((getSerialByVal($step, $steps)+1)/count($steps)) * 100;

$header_html = "<!DOCTYPE html><html><head><meta http-equiv=\"content-type\" content=\"text/html;charset=utf-8\" /><title>-gMIS Installation</title></head><body><p></p>";
$header = "<h1>-gMIS <a href='$file&step='>installation/安装</a></h1><hr/>";
$header .= "<div id='processbardiv' style='width:100%;height:5px;background-color:silver;'><div id='processspan' style='width:$percent%;height:5px;background-color:blue;'></div></div>";
$footer = "<p>&nbsp;</p><hr/>&copy;2010-".date("Y", time()).". <a href='http://ufqi.com/naturedns/search.php?q=-gmis' target='_blank'>-gMIS</a> .";
$footer_html = "<p>&nbsp;</p><p>&nbsp;</p></body></html>";
$config_file = './inc/config.class.php';
$has_installed = 0;
$f = "gMIS-master.zip";
$d = dirname(__FILE__); # substr($f, 0, strlen($f)-4);
if(true){
	$dArr = explode('/', $d);
	$d = $dArr[count($dArr)-1];
}

if(!in_array($step, $steps)){ $step = ''; }

header("Content-type: text/html;charset=utf-8");

## section-2, actions

#
# part-0, welcome
if($step == ''){
	$makeStart = 1;
	if(is_file($config_file)){
		include_once($config_file);		
		if($_CONFIG['auto_install'] == 'INSTALL_DONE'){
			$has_installed = 1;	
			$makeStart = 0;	
		}
		else{
			$makeStart = 1;	
		}
	}
	else{
		$makeStart = 1;	
	}
	if($makeStart == 1){
		$out .= "<br/>感谢选择 -gMIS ! 即将开始在 ".$rtvdir."  安装 -gMIS . 开始之前请阅读使用协议.";
		$out .= xForm($file.'&step=dolicense', array('hasagree'=>array('type'=>'checkbox', 'dispname'=>'我已阅读使用协议并同意')));
	}
	else{
		$out .= "<br/>感谢选择 -gMIS ! 在 ".$rtvdir."  已经安装有 -gMIS . 重新安装将覆盖之前所有的程序和数据. 请先备份或者切换目录.";
		$out .= xForm($file.'&step=dolicense', array('hasagree'=>array('type'=>'checkbox', 'dispname'=>'我已备份数据, 确认重新安装')));
	}
}
else if($step == 'dolicense'){
	if($_REQUEST['hasagree'] == 'on'){
		redirect($file."&step=env");	
	}
	else{
		redirect($file."&step=");	
	}
}
# part-1, evn detection
else if($step == 'env'){
	# checks
	$dir_w = dirWriteable($appdir);
	$out .= "安装目录[$rtvdir]: ";
	if($dir_w){
		$out .= "可写";	
		# rm inc/config
		$cmd = "rm -f ./inc/config.class.php";
		execInBackground($cmd);
	}
	else{
		$out .= "不可写";		
	}
	$out .= ".";

	$out .= "<br/><br/>Checked complete.";

	$out .= xForm($file."&step=getsrc", null);


}
# part-2, main source
else if($step == 'getsrc'){
	# retrieve source
	#$f = "gMIS-master.zip";
	#$d = substr($f, 0, strlen($f)-4);
	$extractDir = str_replace('.zip', '', $f);
	$testf = "$extractDir/xml/info_usertbl.xml";

	if($istep == ''){
		$cmd = "rm -f ./$f; rm -rf ./$d";
		execInBackground($cmd);
		$cmd = "wget 'https://github.com/wadelau/gMIS/archive/master.zip' -O '$f'";
		execInBackground($cmd);
	}

	if(is_file($f)){
		$out .= "<br/>Source retrieved.";
		#print "d:$d f:$f testf:$testf\n";
		if(1 || $istep != 'waitdir'){
			$cmd = "unzip -u -o $f";
			execInBackground($cmd);
		}
		if(!is_dir($extractDir) || !is_file($testf)){
			$out .= "<br/><br/>Srcfile not ready..., waiting ".date("H:i:s", time());
			redirect($file."&step=getsrc&istep=waitdir", 6 , $header.$out.$footer);
		}
		else{
			$cmd = "mv -fu $extractDir/* ./";
			execInBackground($cmd);
			$cmd = "rsync -a -v --remove-source-files $extractDir/* ./";
			execInBackground($cmd);

			if(is_file($testf)){
				$cmd = "rm -rf $extractDir";
				execInBackground($cmd);
				$cmd = "rm -f $f";
				execInBackground($cmd);
				$out .= "<br/>Source extracted successfully.";
			}
			else{
				$out .= "<br/>Source extracted failed.";	
				$out .= "<br/>Srcfile not ready..., waiting ".date("H:i:s", time());
				$out .= "<br/><a href='".$file."&step=getsrc'>终止当前进程, 重新开始获取源程序</a> .";
				redirect($file."&step=getsrc&istep=waitfile", 6 , $header.$out.$footer);
			}
		}
	}
	else{
		$out .= "<br/>srcfile not ready..., waiting ".date("H:i:s", time());
		redirect($file."&step=getsrc&istep=wait", 6, $header.$out.$footer);
	}

	$out .= xForm($file."&step=db", null);

}
# part-3, db and tables
else if($step == 'db'){
	if($istep == ''){
		# connect db
		$out .= xForm($file."&step=db&istep=testdb", array('db_host'=>array('dispname'=>'数据库主机', 'type'=>'text', 'value'=>'127.0.0.1'),
			'db_port'=>array('dispname'=>'数据库端口', 'value'=>'3306'),
			'db_name'=>array('dispname'=>'数据库名称'),
			'db_user'=>array('dispname'=>'数据库访问用户名'),
			'db_pwd'=>array('dispname'=>'数据库访问密码')));
	}
	else if($istep == 'testdb'){
		if(!function_exists('mysql_connect') && !function_exists('mysqli_connect')){
			$out .= "<br/>MySQL 数据库功能未安装, 请联系系统管理员. 1604251035.";
			redirect($file."&step=db&istep=testdb", 10, $header.$out.$footer);
		}
		else{
			$dbhost=$_REQUEST['db_host']; $dbport=$_REQUEST['db_port']; $dbname=$_REQUEST['db_name']; 
			$dbuser=$_REQUEST['db_user']; $dbpwd=$_REQUEST['db_pwd'];
			$mysqlmode = function_exists('mysql_connect') ? 'mysql' : 'mysqli';
			$link = ($mysqlmode == 'mysql') 
				? @mysql_connect($dbhost.":".$dbport, $dbuser, $dbpwd) 
				: new mysqli($dbhost.":".$dbport, $dbuser, $dbpwd);
			$err = '';
			if(!$link) {
				$errno = ($mysqlmode == 'mysql') ? mysql_errno() : mysqli_errno();
				$error = ($mysqlmode == 'mysql') ? mysql_error() : mysqli_error();
				if($errno == 1045) {
					$err = 'database_errno_1045';
				}
				else if($errno == 2003){
					$err = 'database_errno_2003';
				}
				else{
					$err = 'database_connect_error';
				}
			}
			else{
				if($query = (($mysqlmode == 'mysql') 
					? @mysql_query("SHOW TABLES FROM $dbname") 
					: $link->query("SHOW TABLES FROM $dbname"))) {
					if(!$query){
						$err = 'database_query_fail';
					}
					else{
						while($row = (($mysqlmode == 'mysql') ? mysql_fetch_row($query) : $query->fetch_row())) {
							$out .= "<br/>".$row[0];	
						}
					}
				}
			}
			if($err != ''){
				$out .= "<br/>MySQL 数据库功能出错[$err], 请联系系统管理员. 1604251045.";
				redirect($file."&step=db&istep=testdb", 10, $header.$out.$footer);
			}
			else{
				replaceInFile($config_file, 'DB_HOST', $dbhost);
				replaceInFile($config_file, 'DB_PORT', $dbport);
				replaceInFile($config_file, 'DB_NAME', $dbname);
				replaceInFile($config_file, 'DB_USER', $dbuser);
				replaceInFile($config_file, 'DB_PASSWORD', $dbpwd);

				$out .= "<br/>MySQL 数据库功能正常, 继续进行下一步.";
				$out .= xForm($file."&step=db&istep=createtable", array('tablepre'=>array('dispname'=>'数据表前缀', 'value'=>'gmis')));
				#redirect($file."&step=db&istep=createtable", 10, $header.$out.$footer);
			}
		}
		#print_r($_REQUEST);	
	}
	else if($istep == 'createtable'){
		# create tables
		include_once($config_file);
		$tblpre = $_REQUEST['tablepre'];
		if(substr($tblpre, -1) != '_'){ $tblpre .= "_"; }
		#print $tblpre;
		replaceInFile($config_file, 'TABLE_PRE', $tblpre);
		if($tblpre != '' && $tblpre != 'gmis_'){
			replaceInFile('./gmis.tables.sql', 'gmis_', $tblpre);
			sleep(2);
		}
 
 		$sql = 'use '.$_CONFIG['dbname'].''; #source '.$appdir.'/gmis-tables.sql;';
		$mysqlmode = function_exists('mysql_connect') ? 'mysql' : 'mysqli';
		$link = ($mysqlmode == 'mysql') 
			? @mysql_connect($_CONFIG['dbhost'].":".$_CONFIG['dbport'], $_CONFIG['dbuser'], $_CONFIG['dbpassword']) 
			: new mysqli($_CONFIG['dbhost'].":".$_CONFIG['dbport'], $_CONFIG['dbuser'], $_CONFIG['dbpassword']);
		$query = (($mysqlmode == 'mysql') ? @mysql_query($sql) : $link->query($sql));
		
		$sqlstr = file_get_contents("./gmis.tables.sql");;
		#print "<br/>sqlstr:[$sqlstr]";
		$sqlarr = explode(';', $sqlstr);
		foreach($sqlarr as $k=>$v){
			#print "<br/>sql-".($i++).": ".$v;	
			$sql = trim($v);
			if($sql != ''){
				 if(strpos($sql, '--') === 0){
                    print "<br/> skip comments.... [$sql]\n";
                }
                else{
					$query = (($mysqlmode == 'mysql') ? @mysql_query($sql) : $link->query($sql));
				}
			}
		}
		
		$sql = 'show tables'; # from '.$_CONFIG['dbname'];
		$query = (($mysqlmode == 'mysql') ? @mysql_query($sql) : $link->query($sql));
		
		if($query){
			while($row = (($mysqlmode == 'mysql') ? mysql_fetch_row($query) : $query->fetch_row())) {
				$out .= "<br/>".$row[0];	
			}
			$out .= "<br/><br/>Tables created.";
			$out .= xForm($file."&step=init", null);
		}
		else{
			error_log("sql:$sql result:[".$query."]");
			$out .= "<br/><br/>Tables created fail. <br/>Need to see logs and check db info in inc/config.class again.";
			$out .= xForm($file."&step=db", null);
		}
	}
}
# part-4, init and settings
else if($step == 'init'){
	if($istep == ''){
		# init  
		$out .= xForm($file."&step=init&istep=save", array(
					'agentname'=>array('dispname'=>'应用名称', 'value'=>'-gMIS'),
					'frontpage'=>array('dispname'=>'前端应用地址', 'value'=>'-gMIS'),
					'rootemail'=>array('dispname'=>'超级管理员邮箱', 'value'=>'username@example.com'),
					'rootpwd'=>array('dispname'=>'超级管理员密码', 'value'=>'pwd123456', 'type'=>'password')
					)) ;
	}
	else if($istep == 'save'){
		include_once($config_file);
		# set init
		replaceInFile($config_file, 'AGENT_NAME', $_REQUEST['agentname']);
		replaceInFile($config_file, 'FRONT_PAGE', $_REQUEST['frontpage']);

		# save super admin 
 		$sql = 'use '.$_CONFIG['dbname'].''; #source '.$appdir.'/gmis.tables.sql;';
		$mysqlmode = function_exists('mysql_connect') ? 'mysql' : 'mysqli';
		#print "<br/>mysqlmode:[$mysqlmode]";
		$link = ($mysqlmode == 'mysql') 
			? @mysql_connect($_CONFIG['dbhost'].":".$_CONFIG['dbport'], $_CONFIG['dbuser'], $_CONFIG['dbpassword']) 
			: new mysqli($_CONFIG['dbhost'].":".$_CONFIG['dbport'], $_CONFIG['dbuser'], $_CONFIG['dbpassword']);
		$query = (($mysqlmode == 'mysql') ? @mysql_query($sql) : $link->query($sql));
		if(true){
			$sql = "insert into ".$_CONFIG['usertbl']." set email='".trim($_REQUEST['rootemail'])
				."', password='".sha1(trim($_REQUEST['rootpwd']))
				."', updatetime=NOW(), inserttime=NOW(), branchoffice=''";
			#print $sql;
			$query = (($mysqlmode == 'mysql') ? @mysql_query($sql) : $link->query($sql));
		}
		$out .= "<br/>Settings have been saved. ";

		$out .= xForm($file."&step=finalize", null);

	}
	else{
		
		$out .= "Init and settings done.";
		$out .= xForm($file."&step=finalize", null);
		
	}
}
# part-5, finilized and enter
else if($step == 'finalize'){
	# clearance
	$extractDir = str_replace('.zip', '', $f);
	if($istep == ''){
		$cmd = "rm -f ./$f";
		execInBackground($cmd);
		$cmd = "rm -rf ./$extractDir";
		execInBackground($cmd);
		
		$rd = rand(1000, 9999999);
		$cmd = "mv ./install.php ./install.".date("YmdHis", time()).$rd.".php";
		#print $cmd;
		execInBackground($cmd);
		
		# mark installed
		replaceInFile($config_file, 'INSTALL_AUTO', 'INSTALL_DONE');

		#
		$out .= "Finalize and clearance done. 恭喜安装完成!";
		$out .= "<br/> 继续体验 <a href='".$rtvdir."' target='_blank'>$rtvdir</a> .";
	}
	else{
		$out .= "Finalize and clearance done. 恭喜安装完成!";
		$out .= "<br/> 继续体验 <a href='".$rtvdir."' target='_blank'>$rtvdir</a> .";

	}

}

## section-3, outputs

print $header_html.$header."<br/>".$out."<br/><br/>".$footer.$footer_html;

print "<!--\n";
print_r($_REQUEST);
print "timestamp:".date("Y-m-d-H:i:s", time());
print "\n-->";

?>
