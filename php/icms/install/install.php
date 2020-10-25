<?php
/**
* iCMS - i Content Management System
* Copyright (c) 2007-2017 iCMSdev.com. All rights reserved.
*
* @author icmsdev <master@icmsdev.com>
* @site https://www.icmsdev.com
* @licence https://www.icmsdev.com/LICENSE.html
*/
ini_set('display_errors', 'ON');
error_reporting(E_ALL & ~E_NOTICE);

define('iPHP',TRUE);
define('iPHP_APP','iCMS'); //应用名
define('iPHP_DEBUG', true);
define('iPHP_APP_MAIL','support@iCMSdev.com');
define('iPHP_APP_SITE', iPHP_APP);

if($_POST['action']=='install'){
    $db_host     = trim($_POST['DB_HOST']);
    $db_user     = trim($_POST['DB_USER']);
    $db_password = trim($_POST['DB_PASSWORD']);
    $db_name     = trim($_POST['DB_NAME']);
    $db_prefix   = trim($_POST['DB_PREFIX']);
    $db_port     = trim($_POST['DB_PORT']);
    $db_charset  = trim($_POST['DB_CHARSET']);
    $db_engine   = trim($_POST['DB_ENGINE']);

	define('iPHP_DB_HOST',$db_host);	// 服务器名或服务器ip,一般为localhost
    define('iPHP_DB_PORT', $db_port);   //数据库端口
	define('iPHP_DB_USER',$db_user);		// 数据库用户
	define('iPHP_DB_PASSWORD',$db_password);//数据库密码
	define('iPHP_DB_NAME',$db_name);		// 数据库名
    define('iPHP_DB_PREFIX',$db_prefix);    // 表名前缀, 同一数据库安装多个请修改此处
    define('iPHP_DB_CHARSET',$db_charset);    // MYSQL编码设置
    define('iPHP_DB_ENGINE',$db_engine);      // MYSQL引擎
    define('iPHP_DB_PREFIX_TAG','#iCMS@__');

    require_once __DIR__.'/../iPHP/iPHP.php';
    require_once iPHP_CORE.'/iUI.class.php';
    require_once iPHP_APP_CORE.'/iCMS.class.php';

    iPHP::$callback['error'] = 'error_msg';
    iPHP::define_apps(array(
        'admincp' => '10','config'  => '11','files'   => '12',
        'menu'    => '13','group'   => '14','members' => '15',
        'apps'    => '17','former'  => '18','patch'   => '19',
        'cache'   => '23'
    ));

    $router_url     = iSecurity::escapeStr(trim($_POST['ROUTER_URL'],'/'));
    $admin_name     = iSecurity::escapeStr(trim($_POST['ADMIN_NAME']));
    $admin_password = iSecurity::escapeStr(trim($_POST['ADMIN_PASSWORD']));
    $setup_mode     = iSecurity::escapeStr(trim($_POST['SETUP_MODE']));

	$lock_file = iPATH.'cache/install.lock';
	file_exists($lock_file) && iUI::alert('请先删除 cache/install.lock 这个文件。','js:top.callback();');

	iPHP_DB_HOST OR iUI::alert("请填写数据库服务器地址",'js:top.callback("#DB_HOST");');
	iPHP_DB_USER OR iUI::alert("请填写数据库用户名",'js:top.callback("#DB_USER");');
	iPHP_DB_PASSWORD OR iUI::alert("请填写数据库密码",'js:top.callback("#DB_PASSWORD");');
    is_numeric(iPHP_DB_PORT) OR iUI::alert("数据库端口出错",'js:top.callback("#DB_PORT");');
	iPHP_DB_NAME OR iUI::alert("请填写数据库名",'js:top.callback("#DB_NAME");');
    preg_match('/^[a-zA-z0-9\_]+$/is', iPHP_DB_NAME) OR iUI::alert("数据库名包含非法字符，请返回修改",'js:top.callback("#DB_NAME");');
	strstr(iPHP_DB_PREFIX, '.') && iUI::alert("您指定的数据表前缀包含点字符，请返回修改",'js:top.callback("#DB_PREFIX");');
    preg_match('/^[a-zA-z0-9\_]+$/is', iPHP_DB_PREFIX) OR iUI::alert("您指定的数据表前缀包含非法字符，请返回修改",'js:top.callback("#DB_PREFIX");');
    in_array(strtolower(iPHP_DB_CHARSET), array('utf8','utf8mb4')) OR iUI::alert("非法字符集",'js:top.callback("#DB_CHARSET");');

	$admin_name OR iUI::alert("请填写超级管理员账号",'js:top.callback("#ADMIN_NAME");');
	$admin_password OR iUI::alert("请填写超级管理员密码",'js:top.callback("#ADMIN_PASSWORD");');
	strlen($admin_password)<6 && iUI::alert("超级管理员密码不能小于6位字符",'js:top.callback("#ADMIN_PASSWORD");');
    //检测数据库文件
    $sql_file      = __DIR__.'/iCMS.sql';
    $data_sql_file = __DIR__.'/iCMS-data.sql';
    is_readable($sql_file) OR iUI::alert('数据库文件[iCMS.sql]不存在或者读取失败','js:top.callback();',10);
    is_readable($data_sql_file) OR iUI::alert('数据库文件[iCMS-data.sql]不存在或者读取失败','js:top.callback();',10);

    iDB::connect('link');
	iDB::get_error() && iUI::alert("数据库连接出错[".iDB::$last_error."]",'js:top.callback();',10);

    if(isset($_POST['CREATE_DATABASE'])){
        iDB::connect('!select_db');
        // iDB::query("DROP DATABASE `".iPHP_DB_NAME."`; ");
        iDB::query("CREATE DATABASE `".iPHP_DB_NAME."`CHARACTER SET ".iPHP_DB_CHARSET." COLLATE ".iPHP_DB_CHARSET."_general_ci",'get')
        OR
        iUI::alert('数据库创建失败,请确认数据库是否已存在或该用户是否有权限创建数据库','js:top.callback();',10);
    }else{
        iDB::connect();
    }
    iDB::pre_set();
    iDB::select_db(true) OR iUI::alert("不能链接到数据库".iPHP_DB_NAME,'js:top.callback("#DB_NAME");',10);

	$config_file  = iPATH.'config.php';
	$content = iFS::read($config_file,false);
    $content = preg_replace("/define\('iPHP_DB_HOST',\s*'.*?'\)/is",        "define('iPHP_DB_HOST','".iPHP_DB_HOST."')", $content);
    $content = preg_replace("/define\('iPHP_DB_PORT',\s*'.*?'\)/is",        "define('iPHP_DB_PORT','".iPHP_DB_PORT."')", $content);
	$content = preg_replace("/define\('iPHP_DB_USER',\s*'.*?'\)/is", 		"define('iPHP_DB_USER','".iPHP_DB_USER."')", $content);
	$content = preg_replace("/define\('iPHP_DB_PASSWORD',\s*'.*?'\)/is", 	"define('iPHP_DB_PASSWORD','".iPHP_DB_PASSWORD."')", $content);
	$content = preg_replace("/define\('iPHP_DB_NAME',\s*'.*?'\)/is", 		"define('iPHP_DB_NAME','".iPHP_DB_NAME."')", $content);
    $content = preg_replace("/define\('iPHP_DB_PREFIX',\s*'.*?'\)/is",      "define('iPHP_DB_PREFIX','".iPHP_DB_PREFIX."')", $content);
    $content = preg_replace("/define\('iPHP_DB_CHARSET',\s*'.*?'\)/is",     "define('iPHP_DB_CHARSET','".iPHP_DB_CHARSET."')", $content);
	$content = preg_replace("/define\('iPHP_KEY',\s*'.*?'\)/is", 			"define('iPHP_KEY','".random(64)."')",$content);

	iFS::write($config_file,$content,false);
//开始安装 数据库 结构
    $sql = iFS::read($sql_file);
    iPHP_DB_CHARSET=="utf8mb4" && utf8mb4_sql($sql);
    iPHP_DB_ENGINE=="MyISAM" && MyISAM_sql($sql);

    iDB::$show_errors = true;

    $DROP_TABLE_IF_EXISTS = false;
    if($setup_mode=='cover'){//覆盖安装
        $DROP_TABLE_IF_EXISTS = true;
    }
	run_query($sql,$DROP_TABLE_IF_EXISTS);
//导入默认数据
    $data_sql = iFS::read($data_sql_file);
    run_query($data_sql);

//设置超级管理员
	$admin_password = md5($admin_password);
	iDB::query("
		UPDATE `#iCMS@__members`
		SET `username` = '{$admin_name}', `password` = '{$admin_password}'
		WHERE `uid` = '1';
	");

//配置程序
    define('iPHP_APP_CONFIG', iFS::path(iPHP_CONF_DIR . '/' . iPHP_APP . '/config.php')); //网站配置文件
    iCache::init(array(
        'engine'     => 'file',
        'prefix'     => iPHP_APP,
        'host'       => '',
        'time'       => '300',
        'compress'   => '1',
        'page_total' => '300',
    ));

    iPHP::callback(array("apps","cache"));

    $config = configAdmincp::get();
    $config['router']['url']    = $router_url;
    $config['router']['public'] = $router_url.'/public';
    $config['router']['user']   = $router_url.'/user';
    $config['router']['404']    = $router_url.'/public/404.htm';
    $config['FS']['url']        = $router_url.'/res/';
	$config['template']['mobile']['domain'] = $router_url;

    foreach($config AS $n=>$v){
        config_set($v,$n);
    }
    configAdmincp::cache();
    del_patch_files();
//写入数据库配置<hr />开始安装数据库<hr />数据库安装完成<hr />设置超级管理员<hr />更新网站缓存<hr />
	iFS::write($lock_file,'iCMS.'.time(),false);
	iFS::rmdir(iPATH.'install');
	iUI::success("安装完成",'js:top.install.step4();');
}
function del_patch_files() {
    $files = glob(iPATH."app/patch/files/*.php");
    if(is_array($files)) foreach ($files as $file) {
        iFS::del($file);
    }
}
function config_set($value, $name) {
    is_array($value) && $value = addslashes(json_encode($value));
    $sql = "`name` ='$name'";
    $check  = iDB::value("SELECT `name` FROM `#iCMS@__config` WHERE {$sql}");
    $fields = array('name','value');
    $data   = compact ($fields);
    if($check===null){
        iDB::insert('config',$data);
    }else{
        iDB::update('config', $data, array('name'=>$name));
    }
}

function run_query($sql,$DROP_TABLE_IF_EXISTS=false) {
	$sql      = str_replace("\r", "\n", $sql);
	$resource = array();
	$num      = 0;
	$sql_array = explode(";\n", trim($sql));
    foreach($sql_array as $query) {
        $queries = explode("\n", trim($query));
        foreach($queries as $query) {
            $resource[$num] .= $query[0] == '#' ? '' : $query;
        }
        $num++;
    }
    unset($sql);

    foreach($resource as $key=>$query) {
        $query = trim($query);
        $query = str_replace('`icms_', '`#iCMS@__', $query);
        if(strripos($query,'CREATE TABLE')!==false && $DROP_TABLE_IF_EXISTS){
            preg_match('/CREATE\sTABLE\s`(.*?)`\s\(/is', $query, $match);
            if($match[1]){
                $DROP_TABLE_SQL = 'DROP TABLE IF EXISTS `'.$match[1].'`;';
                iDB::query($DROP_TABLE_SQL);
            }
        }
        $query && iDB::query($query);
    }
}
function real_path($p = '') {
    $p = str_replace("\0", '', $p);
    $end = substr($p, -1);
    $a = explode('/', $p);
    $o = array();
    $c = count($a);
    for ($i = 0; $i < $c; $i++) {
        if ($a[$i] == '.' || $a[$i] == '') {
            continue;
        }

        if ($a[$i] == '..' && $i > 0 && end($o) != '..') {
            array_pop($o);
        } else {
            $o[] = $a[$i];
        }
    }
    $o[0] == 'http:' && $o[0] = 'http:/';
    $o[0] == 'https:' && $o[0] = 'https:/';

    return ($p[0] == '/' ? '/' : '') . implode('/', $o) . ($end == '/' ? '/' : '');
}
function MyISAM_sql(&$sql){
    $sql = str_replace('ENGINE=InnoDB', 'ENGINE='.iPHP_DB_ENGINE, $sql);
}
function utf8mb4_sql(&$sql){
    $sql = str_replace('SET NAMES utf8', 'SET NAMES '.iPHP_DB_CHARSET, $sql);
    $sql = str_replace('CHARSET=utf8;', 'CHARSET='.iPHP_DB_CHARSET.';', $sql);
    $varchar = iPHP_DB_ENGINE=="InnoDB"?128:240;
    utf8mb4_replace_varchar($sql,'config','name',$varchar);
    utf8mb4_replace_varchar($sql,'files','path');
    utf8mb4_replace_varchar($sql,'files','ofilename');
    utf8mb4_replace_varchar($sql,'files','filename');
    utf8mb4_replace_varchar($sql,'keywords','keyword',$varchar);
    utf8mb4_replace_varchar($sql,'prop_map','node',240);
    utf8mb4_replace_varchar($sql,'tag','pid');
    utf8mb4_replace_varchar($sql,'tag','name',240);
    utf8mb4_replace_varchar($sql,'tag','tkey');
    utf8mb4_replace_varchar($sql,'tag_map','field');
    utf8mb4_replace_varchar($sql,'user','username',$varchar);
}
//Specified key was too long; max key length is 1000 bytes
function utf8mb4_replace_varchar(&$sql,$table,$field,$_varchar=240,$varchar=255){
    $sql = preg_replace(
        "/CREATE TABLE `icms_".$table."`(.*?)`".$field."` varchar\(".$varchar."\)/is",
        "CREATE TABLE `icms_".$table."`$1`".$field."` varchar(".$_varchar.")",
    $sql);
}
function error_msg($msg='',$type=''){
    iUI::$dialog['modal'] = true;
    //HY000/2002
    //HY000/1045 Access denied for user
    if(strpos($msg, 'mysqli::__construct')!==false){
        $msg = '数据库连接出错<hr />'.$msg;
    }elseif(strpos($msg, 'Couldn\'t fetch mysqli')!==false){
        $msg = '数据库连接出错<hr />'.$msg;
    }elseif(strpos($msg, 'Access denied for user')!==false){
        $msg = '数据库账号或者密码错误<hr />'.$msg;
    }elseif(strpos($msg, 'database exists')!==false){
       $msg = '数据库已存在<hr />'.$msg;
    }elseif(strpos($msg, 'You have an error in your SQL syntax')!==false){
        $msg = '数据库安装出错<hr />'.$msg;
    }
    $value = str_replace("\n", '<br />', $msg);
    iUI::dialog("warning:#:warning:#:{$value}",'js:1',30000000);
    exit;
}
