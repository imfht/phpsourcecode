<?php
require_once dirname(__DIR__) .'/sql.class.php';
require_once __DIR__ . '/tpl.class.php';

/**
 * 迁移操作入口
 */
class SqlMigration {

	private static $migrationTable = 'sql_migration';
	
	/**
	 * @return TplEngine
	 */
	private static function getTplEngine(){
		static $tplEngine = null;
		if (!$tplEngine){

			$tplConfig = array(
				'templateDir' => __DIR__,
				'enableCache' => false,
			);
			$tplEngine = new TplEngine($tplConfig);
		}
		return $tplEngine;
	}

	private static function getMigrations($migrationDir,$tableClassPrefix){
		static $migrations = null;
		if ($migrations) return $migrations;

		$migrations = array();
		$index = 1;
		// 获取迁移类对象
		foreach (glob("{$migrationDir}/*.php") as $filename) {
			$id = basename($filename,'.php');
			require_once "{$migrationDir}/{$id}.php";

			$className = "{$tableClassPrefix}{$id}";

			$obj = new $className();
			// 校验迁移类是否实现了SqlMigrationElement接口
			if ( !($obj instanceof SqlMigrationElement) ){
				throw new Exception('迁移类对象','SqlMigrationElement',$className);
			}

			$migrations[$index] = array('id' =>$id,'class' => $className ,'instance' => $obj);
			$index ++;
		}

		return $migrations;
	}

	static function ls(SqlDataSource $dbo,$migrationDir,$tableClassPrefix,$saveUrl){

		if ( !(is_readable($migrationDir) && is_dir($migrationDir)) )
			throw new Exception("无效的迁移类文件存放路径: {$migrationDir}");

		$migrations = self::getMigrations($migrationDir,$tableClassPrefix);

		// 得到当前版本号,缺省为0
		$curversion = (int) $dbo->one(sprintf('select version from %s',self::$migrationTable));

		self::getTplEngine()->assign('migrations',$migrations);
		self::getTplEngine()->assign('version',$curversion);
		self::getTplEngine()->assign('saveurl',$saveUrl);
		self::getTplEngine()->display('view.php');
	}

	static function change(SqlDataSource $dbo,$migrationDir,$tableClassPrefix,$newversion,$lastversion){

		if ( !(is_readable($migrationDir) && is_dir($migrationDir)) )
			throw new Exception("无效的迁移类文件存放路径: {$migrationDir}");

		$migrations = self::getMigrations($migrationDir,$tableClassPrefix);

		// 得到当前版本号,缺省为0
		$curversion = (int) $dbo->one(sprintf('select version from %s',self::$migrationTable));

		if ($curversion != $lastversion) throw new Exception("无效的参数 lastversion: {$lastversion}");

		if ($curversion == $newversion) throw new Exception("版本无需迁移操作");
		
		if ($newversion > 0){
			if (!isset($migrations[$newversion])) throw new Exception("无效的参数 newversion: {$newversion}");
		}
		
		// 开始进行版本迁移操作
		if ($curversion > $newversion){
			// 反向
			for($start=$curversion,$end = $newversion; $start > $end; $start --){
				$instance = $migrations[$start]['instance'];
				/* @var $instance SqlMigrationElement */
				try {
					$instance->down();
				} catch( Exception $ex){
					throw new Exception("反向迁移: {$curversion}到{$newversion}失败,请修正后再操作... 可参考迁移日志");
				}
				$dbo->begin();
				$is = Sql::assistant(G::$ds)->incr_field(self::$migrationTable,'version',-1);
				$dbo->commit();
				
				if (!$is) throw new Exception("反向迁移: {$curversion}到{$newversion}失败,请修正后再操作... 可参考迁移日志");
			}
			
		}else {
			// 正向
			for($start=$curversion + 1,$end = $newversion + 1; $start < $end; $start ++){
				$instance = $migrations[$start]['instance'];
				/* @var $instance SqlMigrationElement */
				
				try {
					$instance->up();
				} catch( Exception $ex){
					throw new Exception("正向迁移: {$curversion}到{$newversion}失败,请修正后再操作... 可参考迁移日志");
				}
				
				$dbo->begin();
				$is = Sql::assistant(G::$ds)->incr_field(self::$migrationTable,'version',1);
				$dbo->commit();
				
				if (!$is) throw new Exception("正向迁移: {$curversion}到{$newversion}失败,请修正后再操作... 可参考迁移日志");
			}
			
		}
	
		
	}

}

/**
 * 迁移元素接口
 */
interface SqlMigrationElement {

	/**
	 * 正向迁移操作
	 *
	 * @return bool
	 */
	function up();

	/**
	 * 逆向此次迁移操作
	 *
	 * @return bool
	 */
	function down();

	/**
	 * 迁移操作的说明
	 *
	 * @return string
	 */
	function description();
}

// -----------------------
// 
class G
{

	/**
	 * @var SqlDataSource
	 */
	static $ds = null;

	static function normalize($input, $delimiter = ',')
	{
		if (!is_array($input))
		{
			$input = explode($delimiter, $input);
		}
		$input = array_map('trim', $input);
		return array_filter($input, 'strlen');
	}

	static function js_alert($message = '', $after_action = '', $url = '')
	{
	    $out = "<script type=\"text/javascript\">\n";
	    if (!empty($message)) {
	        $out .= "alert(\"";
	        $out .= str_replace("\\\\n", "\\n", self::t2js(addslashes($message)));
	        $out .= "\");\n";
	    }
	    if (!empty($after_action)) {
	        $out .= $after_action . "\n";
	    }
	    if (!empty($url)) {
	        $out .= "document.location.href=\"";
	        $out .= $url;
	        $out .= "\";\n";
	    }
	    $out .= "</script>";
	    echo $out;
	    exit;
	}

	static function t2js($content)
	{
	    return str_replace(array("\r", "\n"), array('', '\n'), addslashes($content));
	}
}	


function app_init()
{
	error_reporting(E_ALL | E_STRICT);
	date_default_timezone_set('Asia/Shanghai');
	session_start();
	header("Content-Type: text/html;charset=utf-8");

	# 兼容测试环境
	if ( defined('SAE_MYSQL_HOST_M') )
	{
		define('MYSQL_HOST',	SAE_MYSQL_HOST_M);
		define('MYSQL_PORT',	SAE_MYSQL_PORT);
		define('MYSQL_DB',		SAE_MYSQL_DB);
		define('MYSQL_USER',	SAE_MYSQL_USER);
		define('MYSQL_PASS',	SAE_MYSQL_PASS);
	}
	else if ( $_SERVER["HTTP_HOST"] == 'sql.oschina.mopaas.com' )
	{
		define('MYSQL_HOST',	'10.4.26.93');
		define('MYSQL_PORT',	'3306');
		define('MYSQL_DB',		'dc3dca8b59f4c4eeab85ebecbfb54f2d9');
		define('MYSQL_USER',	'uLbMTQ3Mw0ZHV');
		define('MYSQL_PASS',	'pBYPxXFNdDxN2');
	}
	else
	{
		define('MYSQL_HOST','localhost');
		define('MYSQL_PORT','3306');
		define('MYSQL_DB','wxjssdk');
		define('MYSQL_USER','root');
		define('MYSQL_PASS','root');
	}

	$dsn = array(
			'type' => 'mysql',

			'dbpath'  => sprintf('mysql:host=%s;port=%d;dbname=%s', 
					MYSQL_HOST,
					MYSQL_PORT,
					MYSQL_DB
				),
			'login'	=> MYSQL_USER,
			'password' => MYSQL_PASS,

			'initcmd' => array(
					"SET NAMES 'utf8'",
				),

			'attr'	=> array(
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
					PDO::ATTR_PERSISTENT => false,
				),
		);

	G::$ds = Sql::ds($dsn);

	$q = 'index';
	if ( !empty($_GET['q']) )
	{
		$q = trim( $_GET['q'], "+ \t\r\n\0\x0B" );
		unset( $_GET['q'] );
		$q = preg_replace('/[^a-z0-9\.]/', '', $q);
	}
	if ( empty($q) ) $q = 'index';
	if ( strtolower($q) == 'init' ){
		echo '去死吧,2货';exit;
	}
	$action = 'app_' . $q;
	if ( !is_callable($action) ) $action = 'app_index';

	$action();
}

app_init();

function app_index(){
	$dir = __DIR__ . '/element';
	$classPrefix = 'M_';
	$url = '?q=save';
	SqlMigration::ls(G::$ds,$dir,$classPrefix,$url);
}

function app_save(){
	$dir = __DIR__ . '/element';
	$classPrefix = 'M_';
	
	if ( !isset($_POST['newversion']) || !isset($_POST['lastversion']) )
	{
		echo '无效的参数';exit;
	}

	$newversion = (int)$_POST['newversion'];
	$lastversion = (int)$_POST['lastversion'];
	
	try {
		$result = SqlMigration::change(G::$ds,$dir,$classPrefix,$newversion,$lastversion);
	} catch (Exception $ex){
		$result = $ex->getMessage();
	}
	G::js_alert($result, '', '?q=index');
}