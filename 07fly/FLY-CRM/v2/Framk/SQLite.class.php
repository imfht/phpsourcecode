<?php 
/**
* SQLite PDO 操作类
*
* @require php_pdo extension
* @require php_pdo_sqlite extension
*
* @property array $table:protected 所有表
* @property array $queryList:protected 发送查询列表
* @property boolean $debug:protected debug开关
*
*
* class_db.php 
*/

class SQLite extends PDO {

	const EXEC_ON_FAIL_BREAK = 0;
	const EXEC_ON_FAIL_ROLLBACK = 1;
	const EXEC_ON_FAIL_IRGNOR = 2;

	protected $tables = array();
	protected $queryList = array();
	static protected $debug = false;

	public function __construct ($path, $extName = '') {
		/**
		* 若是$path为':memory:'将视为SQLite内存库
		*/
		
		$dsn = ':memory:' == $path ? "sqlite:{$path}" : "sqlite:{$path}{$extName}";
		try {
			parent::__construct($dsn);
		} catch (PDOException $e) {
			if (self::$debug) {
				echo $e->getMessage();
			}
			exit;
		}
		$this->init();
	}

	/**
	* 设置debug开关
	*@access public
	* @param boolean $switch 开关
	*/
	static public function setDebug ($switch) {
		self::$debug = $switch;
	}
	
	/*
	数据记录条数
	*/	
	public function countRecords($sql){	
		$result =   $this->fetchAll($sql);	 		
		return count($result);		
	}
	
	/**
	* 发送查询 获取查询结果
	*
	* @access public
	* @param string $sql SQL查询
	* @return boolean|PDOStatement 查询结果
	*/
	public function query ($sql) {
		$result = parent::query($sql);
		$this->queryList[] = $sql;
		if ('00000' != $this->errorCode() && self::$debug) {
			print_r($this->errorInfo());
		}
		return $result;
	}

	/**
	* 发送查询 获取查询结果数组
	*
	* @access public
	* @param string $sql SQL查询
	* @param string $idx 数组索引字段
	* @return boolean|array 查询结果
	*/
	public function fetchAll ($sql, $idx = '') {
		$rows = $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		if ('' == $idx) {
			return $rows;
		}
		$map = array();
		foreach ($rows as $row) {
			$map[$row[$idx]] = $row;
		}
		return $map;
	}

	/**
	* 发送查询 获取影响行数	/**
	* 发送查询 获取查询结果数组
	*
	* @access public
	* @param string $sql SQL查询
	* @param string $idx 数组索引字段
	* @return boolean|array 查询结果
	*/
	public function fetchOne ($sql) {
		$rows = $this->query($sql)->fetch(PDO::FETCH_ASSOC);
		return $rows;
	}
	
	/*
	* @access public
	* @param string $sql SQL查询
	* @return boolean|PDOStatement 查询结果
	*/
	public function exec ($sql) {
		$result = parent::exec($sql);
		$this->queryList[] = $sql;
		if ('00000' != $this->errorCode() && self::$debug) {
			print_r($this->errorInfo());
		}
		return $result;
	}

	/**
	* 发送查询 获取影响行数
	*
	* @access public
	* @param string $sql 一组SQL查询 可以为分号(;)分割的字符串或者一个数组
	* @param int $onFail 对执行查询失败的处理
	* EXEC_ON_FAIL_BREAK 为中断处理
	* EXEC_ON_FAIL_IRGNOR 为忽略失败继续执行
	* EXEC_ON_FAIL_ROLLBACK 为回滚动作
	* @param boolean $transaction 是否使用事务 在使用事务
	* @return array 查询结果
	*
	* 注意:当$onFail参数的值为EXEC_ON_FAIL_ROLLBACK的时候 $transaction参数会强制设置为true
	*/
	public function execMuti ($sql, $onFail = self::EXEC_ON_FAIL_BREAK, $transaction = false) {
		$report = array();
		$transaction= self::EXEC_ON_FAIL_ROLLBACK === $onFail ? true : $transaction;
		$sql = is_string($sql) ? explode(';', $sql) : $sql;
		if ($transaction) {
			$this->beginTransaction();
		}
		foreach ($sql as $query) {
			$affectRows = $this->exec($query);
			if (false === $affectRows && self::EXEC_ON_FAIL_BREAK === $onFail) {
				if ($transaction) {
					$this->commit();
				}
				$report[$query] = $affectRows;
				return $report;
			}
			if (false === $affectRows && self::EXEC_ON_FAIL_ROLLBACK === $onFail) {
				$this->rollBack();
				$report[$query] = $affectRows;
				return $report;
			}
			$report[$query] = $affectRows;
		}
		if ($transaction) {
			$this->commit();
		}
		return $report;
	}

	/**
	* 获取查询列表
	*
	* @access public
	* @return array 查询语句列表
	*/
	public function getQueryList () {
		return $this->queryList;
	}

	/**
	* 对数据库结构进行初始化
	*
	* @access public
	*/
	public function init () {
		$elements = $this->query("SELECT [name], [sql], [type] FROM [sqlite_master]")->fetchAll(PDO::FETCH_ASSOC);
		foreach ($elements as $element) {
			switch ($element['type']) {
				case 'table' :
					$this->tables[$element['name']] = new SQLiteTable($this, $element['name'], $element['sql']);
				break;
			}
		}
	}

	/**
	* 对数据库结构变动进行检查 如果不一致则更新操作
	*
	* @access public
	* @param array $struct 数据库结构
	* @return array 更改报告
	*/
	public function refreshStruct ($struct) {
		$report = array();
		foreach ($struct as $tableName => $tableStruct) {
			if (isset($this->tables[$tableName]) && !$this->tables[$tableName]->compareStruct($tableStruct)) {
				/**
				* 表存在且结构不一致 对表进行重建操作
				*/
				$report[$tableName] = array('action' => 'rebuild');
				$report[$tableName]['result'] = false !== $this->tables[$tableName]->rebuild($tableStruct) ? true : false;
			} elseif (!isset($this->tables[$tableName])) {
				/**
				* 表不存在 对表进行新建操作
				*/
				$report[$tableName] = array('action' => 'build');
				$this->tables[$tableName] = new SQLiteTable($this, $tableName, $tableStruct);
				$report[$tableName]['result'] = false !== $this->tables[$tableName]->build() ? true : false;
			}//end if 
		}//end foreach
		return $report;
	}
}//end class PDO


class SQLiteTable {

	protected $db; //SQLiteDB对象
	protected $sql = ''; //建表SQL
	protected $struct = array(); //表结构
	protected $name = ''; //表名

	/**
	* 构造函数
	*
	* @access public
	* @param string $name 表名
	* @param string|array $sql 建表SQL或结构
	*/
	public function __construct ($db, $name, $sql = '') {
		$this->db = $db;
		$this->name = $name;
		if (is_array($sql)) {
			$this->struct = $sql;
			$this->sql = self::buildSQL($name, $sql);
		} else {
			$this->sql = $sql;
			$this->struct = self::buildStruct($sql);
		}
	}

	/**
	* 比较结构
	*
	* @access public
	* @param array $struct 表结构
	* @return boolean 表结构是否一致
	*/
	public function compareStruct ($struct) {
		/**
		* @todo 验证规则 注意:经规则转换后的字符串有可能不是合法的建表查询
		*/
		$replaceReg = array(
			'~\s*([,\[\]\(\)])\s*~' => " $1 ", //@todo 括号'()'和方括号'[]'前后统一替换为一个空格' '
			'~[\[\]]~' => ' ', //@todo 方括号替换为一个空格' '
			'~\s+~' => ' ', //@todo 所有空白合并为一个空格' '
			'~;~' => '' //@todo 删除分号
		);
		return preg_replace(array_keys($replaceReg), $replaceReg, $this->sql)
		==
		preg_replace(array_keys($replaceReg), $replaceReg, self::buildSQL($this->name, $struct));
	}

	/**
	* 新建表
	*
	* @access public
	*/
	public function build () {
		return $this->db->exec(self::buildSQL($this->name, $this->struct));
	}

	/**
	* 重建表
	*
	* @todo 同旧结构进行比较 做出相应的修改
	* @access public
	* @param array $newStruct 新表结构
	*/
	public function rebuild ($newStruct) {
		$addColumns = array_diff(array_keys($newStruct), array_keys($this->struct));
		$commonColumns = array_intersect(array_keys($newStruct), array_keys($this->struct));
		/**
		* @todo 验证是否删除字段
		* @todo 注意:如果有需要删除的字段会开始事务 建两次表 删除两个表 导两次数据 请慎用!
		*/
		if (count(
		array_diff(
		array_keys($this->struct),
		array_keys($newStruct)
		)
		)) {
			$commonFieldSQL = '['.implode('],[', $commonColumns).']';
			$this->db->beginTransaction();
			$this->db->exec(self::buildSQL("{$this->name}_backup", $newStruct, true));
			$this->db->exec("INSERT INTO [{$this->name}_backup] ({$commonFieldSQL}) SELECT {$commonFieldSQL} FROM [{$this->name}]");
			$this->db->exec("DROP TABLE [{$this->name}]");
			$this->db->exec(self::buildSQL("{$this->name}", $newStruct));
			$this->db->exec("INSERT INTO [{$this->name}] ({$commonFieldSQL}) SELECT {$commonFieldSQL} FROM [{$this->name}_backup]");
			$this->db->exec("DROP TABLE [{$this->name}_backup]");
			$this->db->commit();
			$this->db->exec('VACUUM');
		} elseif(count($addColumns)) {
			foreach ($addColumns as $columnName) {
				$this->db->exec("ALTER TABLE [{$this->name}] ADD COLUMN [{$columnName}] ".$newStruct[$columnName]);
			}
		}
		return true;
	}

	/**
	* 构造建表SQL
	*
	* @static
	* @access public
	* @param string $tableName 表名
	* @param array $struct 表结构
	* @param bool $temporary 是否是临时表
	* @return string 建表SQL
	*/
	static public function buildSQL ($tableName, $struct, $temporary = false) {
		$buffer = array();
		foreach ($struct as $fieldName => $definition) {
			$buffer[$fieldName] = "[{$fieldName}] {$definition}";
		}
		$tempSQL = $temporary ? ' TEMPORARY' : '';
		return "CREATE{$tempSQL} TABLE [{$tableName}] (".implode(', ', $buffer).')';
	}

	/**
	* 构造表结构
	*
	* @static
	* @access public
	* @param string $sql SQL语句
	* @return array 表结构
	*/ 
	static public function buildStruct ($sql) {
		$struct = array();
		foreach(explode(',', preg_replace('~^[^(]+\((.+)\)$~s', "$1", $sql)) as $definition) {
			$definition = trim($definition);
			preg_match('~\[([^\]]+)\]\s+(.+)~', $definition, $matchClips);
			$struct[$matchClips[1]] = $matchClips[2];
		}
		return $struct;
	}
}

/***实例化数据库**************/
//include ("struct.php");
//SQLiteDB::setDebug(true);
//$db = new SQLiteDB('E:/Project\webos\webos.db');
//$db->refreshStruct($dbStruct);

//$db->query("insert into wallpaper values(1,'背景1','images/bg/7_7.jpg','0','0');");
//$db->query("insert into wallpaper values(2,'背景2','images/bg/2011.jpg','0','0');");
//$db->query("insert into wallpaper values(3,'背景3','images/bg/blue.jpg','0','0');");
//$db->query("insert into wallpaper values(4,'背景4','images/bg/blue_glow.jpg','0','0');");
//$db->query("insert into wallpaper values(5,'背景5','images/bg/blue1.jpg','0','0');");
//$db->query("insert into wallpaper values(6,'背景6','images/bg/childhood.jpg','0','0');");
//$db->query("insert into wallpaper values(7,'背景7','images/bg/christmas.jpg','0','0');");
//$db->query("insert into wallpaper values(8,'背景8','images/bg/cloud.jpg','0','0');");
//$db->query("insert into wallpaper values(9,'背景9','images/bg/dandelionDream.jpg','0','0');");
//$db->query("insert into wallpaper values(10,'背景10','images/bg/dreamSky.jpg','0','0');");
//$db->query("insert into wallpaper values(11,'背景11','images/bg/grass.jpg','0','0');");
//$db->query("insert into wallpaper values(12,'背景12','images/bg/green.jpg','0','0');");
//$db->query("insert into wallpaper values(13,'背景13','images/bg/green_glow.jpg','0','0');");
//$db->query("insert into wallpaper values(14,'背景14','images/bg/lookUpSky.jpg','0','0');");
//$db->query("insert into wallpaper values(15,'背景15','images/bg/metal.jpg','0','0');");
//$db->query("insert into wallpaper values(16,'背景16','images/bg/midAutumn.jpg','0','0');");
//$db->query("INSERT INTO 'app' values( 1 , '应用管理' , 'icon/appmarket.png' , 'sys/app/' , 'app' , 1 , 800 , 500 , '1' , '1' , '1' , '应用管理' , 1 , NULL, 1 )");
//$db->query("INSERT INTO 'app' values( 2 , '桌面1' , 'icon/1.png' , 'sys/app/' , 'app' , 1 , 800 , 500 , '1' , '1' , '1' , '桌面' , 1 , NULL ,2)");
//$db->query("INSERT INTO 'app' values( 3 , '桌面2' , 'icon/2.png' , 'sys/app/' , 'app' , 1 , 800 , 500 , '1' , '1' , '1' , '桌面' , 1 , NULL ,3)");
//$db->query("INSERT INTO 'app' values( 4 , '桌面3' , 'icon/3.png' , 'sys/app/' , 'app' , 1 , 800 , 500 , '1' , '1' , '1' , '桌面' , 1 , NULL ,4)");
//$db->query("INSERT INTO 'app' values( 5 , '桌面4' , 'icon/4.png' , 'sys/app/' , 'app' , 1 , 800 , 500 , '1' , '1' , '1' , '桌面' , 1 , NULL ,5)");
//$db->query("INSERT INTO 'app' values( 6 , '桌面5' , 'icon/5.png' , 'sys/app/' , 'app' , 1 , 800 , 500 , '1' , '1' , '1' , '桌面' , 1 , NULL ,6)");
//$db->query("INSERT INTO 'app' values( 7 , '桌面6' , 'icon/6.png' , 'sys/app/' , 'app' , 1 , 800 , 500 , '1' , '1' , '1' , '桌面' , 1 , NULL ,7)");

?>
