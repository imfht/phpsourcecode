#sql.class.php

## 做最好的PHP数据库操作类

 * 简单易懂的源代码
 * 配置简单,方便易用
 * 基于PDO 支持多种数据库,不耦合,不依赖第三方类库
 * 功能适中,便于集成
 * 支持PHP5.2+
 * 安全防sql注入

## 基于最小接口原则

开发者基本只需要使用Sql 类的2个便捷函数就能操作所有的功能:
```php
Sql::ds 		# 得到 数据源对象
Sql::assistant 	# 得到 Sql辅助类对象
```

## 使用手册

```php
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
	// $result = Sql::assistant( JptestApp::$ds )->select_row('ixr_citys',array('island'=>array(1,'>=')),'id,name,image');
	// 
	// $result = Sql::assistant( JptestApp::$ds )->select('ixr_citys',array('id'=>array(1,'>=')),'id,name,image');
	
	prety_printr( $result );
}

```

## 强劲的检索条件生成

```php

function assertEqual($var1,$var2){
	if ($var1 !== $var2)
		throw new Exception('Not Equal .');
}

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
# note osc ' 符号解析存在问题,所以使用了 2个 \
assertEqual($result,"bookname LIKE '%色\\'色%'");

// 数据表字段名比较
$cond = array(
	'author_id' => array(123, '!=' ,'AND'),
	'book_price' => array("market_parce",'>','AND',true),
);
$result = SqlHelper::parse_cond($ds,$cond,FALSE);
assertEqual($result,"author_id != 123 AND book_price > market_parce");

```

## 简易配置和多数据库支持

```php
配置信息说明
1. type = mysql/mariadb 
{
		dbpath: mysql:host=${host};port=${port};dbname=${database}
		initcmd: [
			SET NAMES '${charset}',
		]
}

2. type = pgsql 
{
		dbpath: pgsql:host=${host};port=${port};dbname=${database}
		initcmd: [
			SET NAMES '${charset}',
		]
}

3. type = sybase 
{
		dbpath: sybase:host=${host};port=${port};dbname=${database}
		initcmd: [
			SET NAMES '${charset}',
		]
}

4. type = sqlite 
{
		dbpath: sqlite:${file}
		initcmd: [
			
		]
}

5. type = mssql 
{
		Windows:
		dbpath: sqlsrv:server=${host};port=${port};database=${database}

		Linux:
		dbpath: dblib:host=${host};port=${port};dbname=${database}
		
		initcmd: [
			SET QUOTED_IDENTIFIER ON,
			SET NAMES '${charset}',
		]
}

如果要使用持久连接,可以配置 attr 参数

attr: [
		PDO::ATTR_PERSISTENT => TRUE,
]

类内置使用的 PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC 不能被改变
```

## 开发计划

1. ### 2014年12月

任务 | 状态
--- | ---
db操作类初版完成 | 最后调校期


2. ### 2015年04月

任务 | 状态
--- | ---
读写分离支持 | 最后调校期

## 求评测,拍砖和灌水

本人的QQ|微信 均是 449211678
有需要探讨的可以私下交流

## 友情连接

 1 [开发者博客](https://vb2005xu.iteye.com)
 2 [Markdown 语法指南](https://help.github.com/articles/markdown-basics/)

##