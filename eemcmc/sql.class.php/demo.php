<?php
require_once __DIR__ .'/sql.class.php';

class JptestApp
{

	/**
	 * @var SqlDataSource
	 */
	static $ds = null;

	static function sql_monitor($sql, $dsn_id)
	{
		if (PHP_SAPI === 'cli')
		{
			fwrite(STDOUT, "[sql]: " . print_r($sql,true) . PHP_EOL);
		}
		else
		{
			echo "<BR />[sql]: " . print_r($sql,true);
		}
	}

}

function jptest_init()
{
	error_reporting(E_ALL | E_STRICT);
	date_default_timezone_set('Asia/Shanghai');
	session_start();
	header("Content-Type: text/html;charset=utf-8");

	$dsn = array(
			'type' => 'mysql',

			'dbpath'  => 'mysql:host=127.0.0.1;port=3306;dbname=jptest',
			'login'	=> 'root',
			'password' => '123456',

			'initcmd' => array(
					"SET NAMES 'utf8'",
				),

			'attr'	=> array(
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
					PDO::ATTR_PERSISTENT => false,
				),

			'monitor'	=> 'JptestApp::sql_monitor',
		);
	JptestApp::$ds = Sql::ds($dsn);
	var_dump(JptestApp::$ds);
	
	$result = null;
	// $result = JptestApp::$ds->all('show tables');
	// 
	// $result = Sql::assistant( JptestApp::$ds )->select_row('jp_citys',array('island'=>array(1,'>=')),'id,name,image');
	// 
	// $result = Sql::assistant( JptestApp::$ds )->select('jp_citys',array('id'=>array(1,'>=')),'id,name,image');
	
	var_dump( $result );

	assertDemo();
}

function assertEqual($var1,$var2){
	if ($var1 !== $var2)
		throw new Exception('Not Equal .');
}

function assertDemo()
{
	$ds = JptestApp::$ds;
	/* @var $ds SqlDataSource */

	$cond = "author_id=123 AND bookname='色色'";
	$result = SqlHelper::parse_cond($ds,$cond,FALSE);
	assertEqual($result,"author_id=123 AND bookname='色色'");

	// ? 为数组
	$cond = array(
		'author_id' => 123,
		'bookname' => '色色',
	);
	$result = SqlHelper::parse_cond($ds,$cond,FALSE);
	assertEqual($result,"author_id = 123 AND bookname = '色色'");

	// > < != 
	$cond = array(
		'author_id' => array(123, '>'),
		'bookname' => '色色',
	);
	$result = SqlHelper::parse_cond($ds,$cond,FALSE);
	assertEqual($result,"author_id > 123 AND bookname = '色色'");

	$cond = array(
		'author_id' => array(123, '<'),
		'bookname' => '色色',
	);
	$result = SqlHelper::parse_cond($ds,$cond,FALSE);
	assertEqual($result,"author_id < 123 AND bookname = '色色'");

	$cond = array(
		'author_id' => array(123, '!='),
		'bookname' => '色色',
	);
	$result = SqlHelper::parse_cond($ds,$cond,FALSE);
	assertEqual($result,"author_id != 123 AND bookname = '色色'");

	// 模糊查询 
	$cond = array(
		'bookname' => array('%色色%','LIKE'),
	);
	$result = SqlHelper::parse_cond($ds,$cond,FALSE);
	assertEqual($result,"bookname LIKE '%色色%'");

	// 'IN','NOT IN'
	$cond = array(
		'author_id' => array( array(123,124,125) ),
	);
	$result = SqlHelper::parse_cond($ds,$cond,FALSE);
	assertEqual($result,"author_id IN (123,124,125)");

	$cond = array(
		'author_id' => array( array(123,124,125), 'IN'),
	);
	$result = SqlHelper::parse_cond($ds,$cond,FALSE);
	assertEqual($result,"author_id IN (123,124,125)");

	$cond = array(
		'author_id' => array( array(123,124,125), 'NOT IN'),
	);
	$result = SqlHelper::parse_cond($ds,$cond,FALSE);
	assertEqual($result,"author_id NOT IN (123,124,125)");

	// BETWEEN AND , NOT BETWEEN AND
	$cond = array(
		'author_id' => array( array(10,25), 'BETWEEN_AND'),
	);
	$result = SqlHelper::parse_cond($ds,$cond,FALSE);
	assertEqual($result,"author_id  BETWEEN 10 AND 25");

	$cond = array(
		'author_id' => array( array(10,25), 'NOT_BETWEEN_AND'),
	);
	$result = SqlHelper::parse_cond($ds,$cond,FALSE);
	assertEqual($result,"author_id NOT BETWEEN 10 AND 25");

	// author_id > 15 OR author_id < 5 AND author_id != 32
	$cond = array(
		'author_id' => array(  
			array( array(15,'>','OR'),array(5,'<','AND'), array(32,'!=') ) ,
			'FIELD_GROUP'
		),
	);
	$result = SqlHelper::parse_cond($ds,$cond,FALSE);
	assertEqual($result,"  (author_id > 15 OR author_id < 5 AND author_id != 32)");

	// OR AND 连接符
	$cond = array(
		'author_id' => array(123, '!=' ,'AND'),
		'bookname' => array('色色', '=' ,'OR'),
		'book_price' => array(45, '<=' ,'AND'),
	);
	$result = SqlHelper::parse_cond($ds,$cond,FALSE);
	assertEqual($result,"author_id != 123 AND bookname = '色色' OR book_price <= 45");

	// 传入的条件的值中的特殊字符会自动进行 qstr 转义
	$cond = array(
		'bookname' => array("%色'色%",'LIKE'),
	);
	$result = SqlHelper::parse_cond($ds,$cond,FALSE);
	assertEqual($result,"bookname LIKE '%色\'色%'");

	// 数据表字段名比较
	$cond = array(
		'author_id' => array(123, '!=' ,'AND'),
		'book_price' => array("market_parce",'>','AND',true),
	);
	$result = SqlHelper::parse_cond($ds,$cond,FALSE);
	assertEqual($result,"author_id != 123 AND book_price > market_parce");
}

jptest_init();
