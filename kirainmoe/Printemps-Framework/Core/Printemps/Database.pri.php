<?php
/**
 * Printemps Framework 数据库操作方法封装文件
 * Printemps Framwork Database Function File
 * (C)2015 Printemps Framework DevTeam
 * 目前支持MySQL/MySQLi
 * 请修改 config.inc.php 中的数据库信息，否则将会抛出异常！
 */
class Printemps_Database{
	/**
	 * 引入私有变量：设定config
	 * @var array
	 */
	private $config;
	/**
	 * 数据库连接
	 * @var object
	 */
	public $db;
	/**
	 * 设定操作使用的表
	 * @var string
	 */
	public $table;	
	/**
	 * 数据库查询方法
	 * @var string
	 */
	protected $method;
	/**
	 * 设置PDO的全局准备函数
	 * @var object
	 */
	public $prepare;
	/**
	 * __construct 构造函数
	 */
	function __construct(){
		global $config;
		$this->config = $config;
		$this->method = $this->config['method'];
		/**
		 * Using MySQLi method
		 */
		if($this->config['method'] == 'mysqli'){
			if(!class_exists('mysqli'))
				throw new Exception("您的PHP不支持MySQLi拓展，这可能是由于您没有编译或者PHP版本太低导致的，请使用 MoeFramework 的MySQL库，或者升级您的PHP版本。", 10000);
			@$this->db = new mysqli($this->config['dbHost'], 
				$this->config['dbUser'], 
				$this->config['dbPwd'], 
				$this->config['dbName'],
				$this->config['dbPort']
				);
			if($this->db->connect_errno){
				$errorCode = mysqli_connect_errno();
				switch($errorCode){

					case 1044:
					case 1045:
					throw new Exception("在与数据库连接中发生异常辣……无法连接到数据库，请检查一下 config.inc.php 中的数据库用户名和密码呐……<br/><br/>MySQL返回值：".$errorCode, 10001);
					break;

					case 1049:
					throw new Exception("在与数据库连接中发生异常辣……无法连接到数据库，请检查一下 config.inc.php 中填写的数据库是否存在呐……<br/><br/>MySQL返回值：".$errorCode, 10001);
					break;

					case 2002:	
					case 2003:
					throw new Exception("在与数据库连接中发生异常辣……无法连接到数据库，请检查一下 config.inc.php 中填写的端口是否正确呐……<br/><br/>MySQL返回值：".$errorCode,10001);
					break;

					case 2005:
					throw new Exception("在与数据库连接中发生异常辣……无法连接到数据库，请检查一下数据库地址是否正确，数据库服务器是否可用呐……<br/><br/>MySQL返回值：".$errorCode,10001);
					break;

					case 2006:
					throw new Exception("在与数据库连接中发生异常辣……无法连接到数据库，请检查一下数据库服务器是否可用呐……<br/><br/>MySQL返回值：".$errorCode,10001);
					break;

					default :
					throw new Exception("在与数据库连接中发生异常辣……无法连接到数据库，捕获到了未知错误……<br/><br/>MySQL返回值：".$errorCode,10001);
					break;
				}
			}
			$this->db->set_charset($this->config['dbEncode']);
			return $this->db;
		}
		/**
		 * Using MySQL method
		 */
		elseif($this->config['method']=='mysql'){
			@$this->db = mysql_connect($this->config['dbHost'].":".$this->config['dbPort'], $this->config['dbUser'], $this->config['dbPwd']);
			if(mysql_errno()){
				$errorCode = mysql_errno();
				switch($errorCode){
					case 1044:
					case 1045:
					throw new Exception("在与数据库连接中发生异常辣……无法连接到数据库，请检查一下 config.inc.php 中的数据库用户名和密码呐……<br/><br/>MySQL返回值：".$errorCode, 10001);
					break;

					case 1049:
					throw new Exception("在与数据库连接中发生异常辣……无法连接到数据库，请检查一下 config.inc.php 中填写的数据库是否存在呐……<br/><br/>MySQL返回值：".$errorCode, 10001);
					break;

					case 2002:	
					case 2003:
					throw new Exception("在与数据库连接中发生异常辣……无法连接到数据库，请检查一下 config.inc.php 中填写的端口是否正确呐……<br/><br/>MySQL返回值：".$errorCode,10001);
					break;

					case 2005:
					throw new Exception("在与数据库连接中发生异常辣……无法连接到数据库，请检查一下数据库地址是否正确，数据库服务器是否可用呐……<br/><br/>MySQL返回值：".$errorCode,10001);
					break;

					case 2006:
					throw new Exception("在与数据库连接中发生异常辣……无法连接到数据库，请检查一下数据库服务器是否可用呐……<br/><br/>MySQL返回值：".$errorCode,10001);
					break;

					default :
					throw new Exception("在与数据库连接中发生异常辣……无法连接到数据库，捕获到了未知错误……<br/><br/>MySQL返回值：".$errorCode,10001);
					break;
				}
			}
			mysql_query("SET NAMES UTF8");
			mysql_select_db($this->config['dbName']);
			return $this->db;
		}
		/**
		 * Using PDO Extendstion (We suggest you using PDO to operate database so that can protect SQL insert.)
		 * Before Using PDO Extendstion, you should make PHP with PDO Extendtions(should have a class 'PDO' exist)
		 */
		elseif($this->config['method']=='pdo'){
			if(!class_exists('PDO'))
				throw new Exception("Printemps 检测到当前环境不支持PDO拓展，请修改 config.inc.php 为其他数据库连接方式", 10009);

			$this->db = new PDO("mysql:host=".$this->config['dbHost'].":".$this->config['dbPort'].";dbname={$this->config['dbName']}",
				$this->config['dbUser'],$this->config['dbPwd']);
			if(!$this->db)
				throw new Exception("Printemps 在与数据库连接中发生异常，无法通过PDO与数据库建立连接。", 10010);
			$this->db->exec("SET NAMES UTF8");
			return $this->db;
		}
		/**
		 * Else, throw new Exception.
		 */
		else{
			throw new Exception("不支持的数据库查询方法，请修改MoeFramework配置文件 config.inc.php 的 method 为 mysql 或 mysqli。",10002);
		}

	}

	/**
	 * 通过类接口自由执行SQL查询
	 * @param  string $query SQL查询内容
	 * @return mysqli_query        返回SQL查询对象
	 */
	public function query($query,$pdoSelect = false){
		if($this->method == 'mysqli'){
			$res = $this->db->query($query) or die(mysqli_error($this->db));
			return $res;
		}
		elseif($this->method == 'mysql'){
			$res = mysql_query($query);
			return $res;
		}
		elseif($this->method == 'pdo'){
			$prep = $this->db->prepare($query);
			$res = $prep->execute();
			$this->prepare = $prep;
			return $res;
		}
		else{
			throw new Exception("不支持的数据库查询方法，请修改Prinetmps Framework配置文件 config.inc.php 的 method 为 pdo 或 mysql / mysqli。",10002);
		}
	}

	/**
	 * select 执行MySQL查询：select
	 * @param  string $selector	要选择的对象
	 * @param  string $table 		要查询的表
	 * @param  array  $condition 	查询条件
	 * @param  int      $andor 指定查询的时候WHERE是AND还是OR
	 * @return mysqli_query
	 */
	public function select($selector, $table, $condition = '',$andor = 1){
		//将传入内容转义，预防SQL注入
		$selector = addslashes($selector);	
		$table = addslashes($table);
		$where = $this->where($condition,$andor);
		if($this->config['method'] == 'pdo')
			$query = $this->query("SELECT {$selector} FROM {$table} ".$where,true);
		else
			$query = $this->query("SELECT {$selector} FROM {$table} ".$where);
		return $query;
	}

	/**
	 * fetch 通过传入数据库查询来 fetch 数据
	 * @param  mysqli_query  	$query		传入的MySQL查询
	 * @param  string  		$method 		获取的方法，默认assoc，有array/assoc/row/object
	 * @return array/boolean/object 		返回fetch后的结果
	 */
	public function fetch($query , $met = 'assoc'){
		/**
		 * 开发笔记：mysqli_fetch_assoc() 效果与 mysqli_fetch_array()加上MYSQLI_ASSOC效果相同
		 */
		if($this->method == 'mysqli'){
			switch($met){
				case 'array':
				return mysqli_fetch_array($query);
				break;

				default:
				case 'assoc':
				return mysqli_fetch_array($query,MYSQLI_ASSOC);
				break;

				case 'row':
				return mysqli_fetch_row($query);
				break;

				case 'object':
				return mysqli_fetch_object($query);
				break;

			}
		}
		elseif($this->method == 'mysql'){
			switch($met){
				case 'array':
				return mysql_fetch_array($query);
				break;

				default:
				case 'assoc':
				return mysql_fetch_array($query,MYSQLI_ASSOC);
				break;

				case 'row':
				return mysql_fetch_row($query);
				break;

				case 'object':
				return mysql_fetch_object($query);
				break;
			}
		}
		elseif($this->method == 'pdo'){
			switch($met){
				default:
				return $this->prepare->fetch(PDO::FETCH_LAZY);
				break;

				case 'assoc':
				case 'array':
				return $this->prepare->fetch(PDO::FETCH_ASSOC);
				break;	
			}


		}
		else{
			return false;
		}
	}

	/**
	 * insert  执行 MySQL 查询：insert
	 * @param  string   $table          要操作的表
	 * @param  array    $content      要插入的内容，通过数组形式展现[$key=>$value]
	 * @return  boolean			若查询成功，返回true；否则返回false
	 */
	public function insert($table, $content){

		if(empty($content) || !is_array($content))
			return false;

		$table = addslashes($table);
		$index = "";
		$val = "";
		/**
		 * 开发笔记：current($array) 用于返回数组中的当前元素的值 // end($array) 用于返回数组最后一个值
		 * array_pop($array) 删除数组中的最后一个元素
		 */
		$time = count($content);
		foreach($content as $key=>$value){
			if($value == end($content)){
				$index = $index.'`'.$key.'`';
				$val = $val.'"'.$value.'"';
			}
			else{
				$index = $index.'`'.$key.'`,';
				$val = $val.'"'.$value.'", ';
			}
		}
		$sql = "INSERT INTO $table ({$index}) VALUES ($val)";
		$query = $this->query($sql);
		if($query)	return true;
		else 		return false;

	}

	/**
	 * update: 执行MySQL查询 Update
	 * @param  string $table     指定要操作的表
	 * @param  array $set       指定要UPDATE的内容和值
	 * @param  string/array $condition 指定执行UPDATE的条件
	 * @param  int      $andor 指定查询的时候WHERE是AND还是OR
	 * @return boolean            返回布尔值，如果成功返回true反之返回false
	 */
	public function update($table , $set , $condition = '',$andor = 1){

		if(empty($set) || !is_array($set))
			return false;

		$table = addslashes($table);

		$setQuery = "SET";
		$setTime = 1;
		/** 处理请求 */
		foreach($set as $key=>$value){
			if($setTime == 1){
				$setQuery = $setQuery." `".addslashes($key)."` = '".addslashes($value)."'";
				$setTime = $setTime+1;
			}
			else{
				$setQuery = $setQuery." AND `".addslashes($key)."` = '".addslashes($value)."'";
				$setTime = $setTime+1;
			}
		}

		/** 处理条件 */
		$where = $this->where($condition,$andor,true);

		$query = $this->query("UPDATE {$table} {$setQuery}  ".$where);
		if($query)		return true;
		else 			return false;

	}


	/**
	 * count：执行MySQL查询SELECT COUNT
	 * @param  string $name      要count的字段，默认为*
	 * @param  string $table     要count的表
	 * @param  string/array $condition 查询条件，传入必须为数组
	 * @param  int      $andor 指定查询的时候WHERE是AND还是OR
	 * @return int            返回查询的结果
	 */
	public function count($name = '*', $table , $condition = '',$andor = 1){

		$name = addslashes($name);
		$table = addslashes($table);
		$where = $this->where($condition,$andor);
		$sql = "SELECT COUNT({$name}) AS total FROM $table ".$where;
		$query = $this->query($sql);
		$data = $this->fetch($query);
		return $data['total'];

	}

	/**
	 * have：检查某个表是否有某条记录
	 * @param  string $name  字段名称
	 * @param  string $value 字段的值
	 * @param  string $table 表名称
	 * @return boolean        如果存在记录返回true，否则返回false
	 */
	public function have($name,$value,$table){
		$name = addslashes($name);
		$value = addslashes($value);
		$table = addslashes($table);
		$condition = array($name=>$value);
		$data = $this->count("*",$table,$condition);
		if($data != 0)
			return true;
		else
			return false;
	}

	/**
	 * delete：执行MySQL delete 查询
	 * @param  string  $table     要操作的表
	 * @param  null/array  $condition 条件
	 * @param  integer $andor     指定WHERE连接符
	 * @return boolean             成功返回，否则返回false
	 */
	public function delete($table,$condition, $andor = 1){
		$where = $this->where($condition,$andor);
		$query = $this->query("DELETE FROM $table ".$where);
		if($query)	return true;
		else 		return false;
	}

	/**
	 * create：支持创建数据库或数据表
	 * @param  string $create DATABASE或者TABLE，指定要创建的是数据库还是表，注意数据库需要权限！
	 * @param  string $name   指定要创建的数据库或者表名
	 * @param  null/array $field  只有在创建分表时才激活，指定创建的字段和类型，格式：array("name"=>"VARCHAR(1000) NOT NULL");
	 * @return boolean         成功返回true，否则返回false
	 */
	public function create($create , $name, $field=''){

		$create = strtoupper($create);		//先做大小写转义
		$name = addslashes($name);

		if($create == 'DATABASE'){
			$query = $this->query("CREATE DATABASE ".$name);
			if($query)	return true;
			else 		return false;
		}
		elseif($create == 'TABLE'){
			if(empty($field) || !is_array($field))
				return false;
			else{
				$condition = '';
				$plus = '';
				if(isset($field['PRIMARY KEY'])){
					$plus = 'PRIMARY KEY(`'.$field['PRIMARY KEY'].'`)';
					unset($field['PRIMARY KEY']);
				}
				foreach($field as $key=>$value){
					if(!$value == end($field))
						$condition = $condition." `".addslashes($key)."` ".addslashes($value)." ,";
					else{
						if(!empty($plus))
							$condition = $condition." `".addslashes($key)."` ".addslashes($value)." ,";
						else
							$condition = $condition." `".addslashes($key)."` ".addslashes($value)."";
					}
				}
				$query = $this->query("CREATE TABLE ".$name." ( ".$condition.$plus." )");
				if($query)		return true;
				else 			return false;
			}
		}
		else
			return false;
	}

	/**
	 * 慎用函数：执行TRUNCATE查询
	 * @param  string $name     truncate的对象名称
	 * @return boolean           成功返回true否则返回false
	 * 注意：这是一个慎用函数！此函数会清空你的数据表并且回复自增值到0
	 */
	public function truncate($name){
		$name = addslashes($name);
		$query = $this->query("TRUNCATE TABLE ".addslashes($name));
		if($query)		return true;
		else 			return false;
	}

	/**
	 * 慎用函数：执行DROP查询
	 * @param  string $type 要drop数据库或者表(database/table)
	 * @param  string $name 操作对象的名称
	 * @return boolean       成功返回true否则返回false
	 * 注意：这是一个慎用函数！此函数会让你的数据库or数据表灰(zha)飞(dou)烟(bu)灭(sheng)！
	 */
	public function drop($type,$name){
		$type = strtoupper($type);
		switch($type){
			case 'DATABASE':
			$query = $this->query("DROP DATABASE ".addslashes($name));
			break;

			case 'TABLE':
			$query = $this->query('DROP TABLE '.addslashes($name));
			break;

			default:
			return false;
			break;
		}
		if($query)		return true;
		else 		return false;
	}

	/**
	 * 处理传入的条件，生成WHERE语句
	 * @param  array $condition 传入的条件
	 * @param  int      $andor 指定查询的时候WHERE是AND还是OR
	 * @param  boolean $update 布尔值，如果是从update传入，为true，决定是否注销ORDER变量
	 * @return string 	返回处理后的结果
	 */

	private function where($condition,$andor,$update = false){
		if(empty($condition)){
			$where = "";
			$order = "";
			$limit = "";
		}
		else{
			$where = "";
			$time = 1;

			if(isset($condition['LIMIT'])){
				$limit = " LIMIT ".$condition['LIMIT'];
				unset($condition['LIMIT']);
			}
			else{
				$limit = " ";
				unset($condition['LIMIT']);
			}
			if(!$update){
				if(isset($condition['ORDER'])){
					$order = " ORDER BY ".$condition['ORDER'];
					unset($condition['ORDER']);
				}
				else{
					$order =" ";
					unset($condition['ORDER']);
				}
			}
			else{
				$order = "";
			}

			if($andor == 1){
				$link = "AND ";
			}
			else{
				$link = "OR";
			}

			foreach($condition as $key=>$value){
				if($time == 1){
					$temp ="WHERE  `".addslashes($key)."` = '".addslashes($value)."' ";
				}
				else{
					$temp = $link." `".addslashes($key)."` = '".addslashes($value)."'' ";
				}
				$where = $where.$temp;
				$time = $time++;
			}
			return $where.$order.$limit;
		}
	}
}
