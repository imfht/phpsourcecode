<?php
/** 
 * database 数据库扩展基于 PDO，提供一个统一的数据连接管理对象。
 * 该类使用单例多连模式，也就是说，你可以用它来同时连接多个数据库，只需要在进行操作时进行切换。
 * 类中提供了一些基本数据库操作的方法，可以方便地对数据进行增删查改等操作。
 * 你可以通过一个类似 URL 地址地资源名称来建立数据库连接，例如 mysql:://localhost:3306/modphp/?username=root&password=12345。
 * 也可以使用数组来组合查询语句的 where 添加，如 array('username'=>'root')。
 */
final class database{
	/* 默认的数据库信息配置 */
	public  static $error = ''; //错误信息
	private static $link = array(null); //连接列表
	private static $name = 0; //当前连接名
	private static $info = array(array()); //连接信息
	private static $set = array(); //连接设置
	private static $dsnSet = array(); //dsn 设置

	/** select_db() 选择数据库 */
	private static function select_db($name){
		if(!empty(self::$link[self::$name])){
			$link = self::$link[self::$name];
			$link->query("USE $name");
		}
	}

	/** generateDSN() 生成 DSN */
	private static function generateDSN($url, &$scheme='', &$host='', &$port=0, &$path='', &$query=''){
		if($url){
			extract(parse_url($url)); //解析 URL
		}
		$path = $host ? trim($path, '/') : rtrim($path, '/');
		if(!$scheme) return array();
		$set = array(
			'type'=>$scheme, //数据库类型
			'host'=>$host, //主机地址
			'port'=>$port, //端口
			'dbname'=>$path, //默认打开的数据库
			);
		switch(strtolower($scheme)){
			case 'file':
			case 'dsn':
				$set['dsn'] = 'uri:file:///'.$path;
				break;
			case 'sqlite': //sqlite 是单文件数据库
				if($host){
					$path = $host;
					$host = '';
				}
				$set['dsn'] = 'sqlite:'.$path;
				$set['dbname'] = $path;
				$set['host'] = '';
				if(!pathinfo($set['dsn'], PATHINFO_EXTENSION)) $set['dsn'] .= '.db'; //默认使用 .db 后缀
				break;
			case 'firebird':
				$set['dsn'] = 'firebird:dbname='.$host.($port ? '/'.$port : '').':/'.$path;
				break;
			case 'informix':
				$set['dsn'] = 'informix:host='.$host.($port ? ';service='.$port : '').($path ? ';database='.$path : '');
				break;
			case 'sqlsrv':
				$set['dsn'] = 'sqlsrv:Server='.$host.($port ? ','.$port : '').($path ? ';Database='.$path : '');
				break;
			case 'oci':
				$set['dsn'] = 'oci:dbname='.($host ? '//'.$host.($port ? ':'.$port : '').'/' : '').$path;
				break;
			default:
				$set['dsn'] = $scheme.':host='.$host.($port ? ';port='.$port : '').($path ? ';dbname='.$path : '');
				break;
		}
		return $set;
	}

	/**
	 * set() 设置连接选项
	 * @static
	 * @param  string $opt  [可选]选项名
	 * @param  mixed  $val  [可选]选项值
	 * @return mixed        如果进行设置，则返回当前对象，否则返回设置(项)
	 */
	static function set($opt = null, $val = null){
		$set = &self::$set; //所有设置
		$name = self::$name; //当前连接名
		$_set = array( //设置
			'type'    => 'mysql', //数据库类型
			'host'    => '', //主机地址
			'port'    => 0, //端口
			'dbname'  => '', //默认数据库
			'username'=> '', //用户名
			'password'=> '', //密码
			'charset' => 'utf8', //默认编码
			'dsn'     => '', //自定义连接标识
			'prefix'  => '', //默认表前缀
			'debug'   => false, //调试模式
			'timeout' => 5, //超时秒数
			'options' => array(), //其他选项
			'queries' => 0, //记录查询次数
		   );
		if(!isset($set[$name])) $set[$name] = $_set;
		$_set = &$set[$name]; //引用当前连接的设置选项
		if($opt === null){
			return $_set; //返回全部设置
		}elseif(is_string($opt) && $val === null){
			return isset($_set[$opt]) ? $_set[$opt] : false; //返回指定设置项
		}else{
			if(is_string($opt)) $opt = array($opt => $val);
			foreach ($opt as $k => $v) {
				if($k == 'dbname') self::select_db($v); //切换数据库
				elseif($k == 'queries') continue;
				elseif($k == 'dsn'){ //手动设置 dsn
					$_set['type'] = strstr($v, ':', true);
					self::$dsnSet[$name] = true; //固定 dsn，不再自动生成
				}
				$_set[$k] = $v;
			}
		}
		if(empty(self::$dsnSet[$name])) $_set = array_merge($_set, self::generateDSN('', $_set['type'], $_set['host'], $_set['port'], $_set['dbname']));
		return new self;
	}

	/** host() 设置或获取主机 */
	static function host($host = null){
		return self::set('host', $host);
	}

	/** port() 设置或获取端口 */
	static function port($port = null){
		return self::set('port', $port);
	}

	/** dbname() 设置或切换数据库 */
	static function dbname($name = null){
		return self::set('dbname', $name);
	}

	/**
	 * debug() 设置调试模式或调试信息
	 * @static
	 * @param  mixed  $msg 调试信息或 true|false 来开启或关闭调试
	 * @return object      当前对象
	 */
	static function debug($msg){
		if(is_bool($msg) || $msg === 1 || $msg === 0){
			self::set('debug', $msg); //设置调试状态
		}elseif($msg !== null){
			if(self::set('debug')){
				print_r($msg); //输出调试信息
				echo "\n";
			}
		}
		return new self;
	}

	/**
	 * info() 获取连接的相关信息
	 * @static
	 * @param  string $key [可选]获指定项
	 * @return mixed       连接相关信息
	 */
	static function info($key = ''){
		$name = self::$name;
		$link = self::$link[$name]; //引用当前连接
		$info = array(
			'clientVersion'=>PDO::ATTR_CLIENT_VERSION, //客户端版本
			'serverVersion'=>PDO::ATTR_SERVER_VERSION, //服务器版本
			'serverInfo'=>PDO::ATTR_SERVER_INFO, //服务器信息
			'driverName'=>PDO::ATTR_DRIVER_NAME, //驱动名称
			);
		foreach($info as $k => $v) {
			try{
				$info[$k] = $link->getAttribute($v); //尝试获取属性
			}catch(PDOException $e){
				$info[$k] = '';
			}
		}
		$info['connection'] = $link; //当前连接的引用
		return !$key ? $info : (isset($info[$key]) ? $info[$key] : false);
	}

	/**
	 * open() 打开新连接或切换连接
	 * @static
	 * @param  string|int $name 连接名称或者用于建立连接的 URL 描述地址
	 * @return object           当前对象
	 */
	static function open($name){
		if(!array_key_exists($name, self::$link)){ //建立新的连接
			$_set = self::generateDSN($name, $scheme, $host, $port, $path, $query); //生成 DSN
		}else{
			$host = $port = $path = $_set = null;
		}
		$name = $host ?: $path ?: $name;
		if($name == $host && $port) $name .= ':'.$port;
		self::$name = $name; //切换连接
		$set = &self::$set[$name];
		if(!$set) $set = self::set();
		$set = array_merge($set, $_set ?: array());
		if(!array_key_exists($name, self::$link)){
			self::$link[$name] = null; //新连接
		}
		if(!empty($query)){
			parse_str($query, $query); //解析 URL 查询字符串
			foreach ($query as $k => $v) {
				if($v === '0' || $v === 'false') $v = false;
				if(isset($set[$k])) $set[$k] = $v;
				else $set['options'][$k] = $v; //设置额外的连接选项
			}
		}
		return new self;
	}

	/**
	 * close() 关闭当前连接
	 * @static
	 * @return object 当前对象
	 */
	static function close(){
		unset(self::$link[self::$name]);
		self::$name = 0; //重置为默认连接
		return new self;
	}

	/** login() connect() 方法的别名 */
	static function login($user = '', $pass = ''){
		return self::connect($user, $pass);
	}

	/**
	 * connect() 连接数据库
	 * @static
	 * @param  string $user [可选]用户名
	 * @param  string $pass 密码
	 * @return object       当前对象
	 */
	static function connect($user = '', $pass = ''){
		$link = null;
		$name = self::$name;
		$set = &self::$set[$name];
		if($user) $set['username'] = $user;
		if($pass) $set['password'] = $pass;
		$dbname = $set['host'] ? $set['host'].'/'.$set['dbname'] : $set['dbname'];
		self::debug("Trying to connect $dbname...");
		try{
			$dsn = $set['dsn'];
			if($set['host'] && $set['charset']) $dsn .= ';charset='.$set['charset']; //设置字符集
			if($set['timeout']) $set['options'][PDO::ATTR_TIMEOUT] = $set['timeout']; //设置超时
			$link = new PDO($dsn, $set['username'], $set['password'], $set['options']); //创建 PDO 实例
			$error = $link->errorInfo();
		}catch(PDOException $e){
			$error = array('Error', $e->getCode() ?: 255, 'Error: '.$e->getMessage());
		}
		if($link){
			$link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //异常模式
			$link->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); //PDOStatement::fetch() 方法获取关联数组
		}
		self::$error = $error[1] ? $error[2] : '';
		self::debug(self::$error ?: null);
		self::$link[$name] = $link; //将 PDO 对象保存到连接列表中
		return new self;
	}

	/**
	 * quote() 转义字符串并添加引号
	 * @static
	 * @param  string $str 原字符串
	 * @return string      转义后字符串
	 */
	static function quote($str){
		return self::$link[self::$name]->quote($str);
	}

	/**
	 * query() 执行查询
	 * @static
	 * @param  string $str 查询语句
	 * @return mixed       执行结果
	 */
	static function query($str){
		self::debug('> '.$str);
		$link = self::$link[self::$name]; //引用当前连接
		$result = null;
		try{
			$result = @$link->query($str); //尝试执行查询语句
			$error = @$link->errorInfo();
			self::$set[self::$name]['queries'] += 1; //更新总查询次数
		}catch(PDOException $e){ //处理异常
			$error = array('Error', $e->getCode() ?: 255, 'Error: '.$e->getMessage());
			if(stripos($error[2], 'server has gone away')){
				return self::connect()->query($str); //短线重连并再次执行(递归地)
			}
		}
		self::debug($result ? 'Success!' : 'Failure!');
		self::debug($error[1] ? $error[2] : null);
		return $result;
	}

	/**
	 * insert() 插入记录
	 * @static
	 * @param  string $table 表名(不含前缀)，多个表用 , 分隔
	 * @param  array  $input 记录信息，关联数组
	 * @param  int    &$id   [可选]填充插入 id
	 * @return boolean
	 */
	static function insert($table, array $input, &$id = 0){
		if(!$input) return false;
		$prefix = self::set('prefix');
		$tables = explode(',', str_replace(' ', '', $table));
		foreach ($tables as &$table) {
			$table = "`{$prefix}{$table}`"; //自动添加数据表前缀(如果有)
		}
		$tables = implode(',', $tables);
		$ks = $vs = '';
		foreach ($input as $k => $v) {
			$ks .= "`$k`,"; //组合字段
			$vs .= self::quote($v).","; //组合值
		}
		$sql = "INSERT INTO $tables (".rtrim($ks, ',').') VALUES ('.rtrim($vs, ',').')'; //组合查询语句
		$result = self::query($sql); //执行查询
		$id = self::$link[self::$name]->lastInsertId(); //获取插入 ID
		return $result != false;
	}

	/**
	 * update() 更新记录
	 * @static
	 * @param  string           $table   表名(不含前缀)，多个表用 , 分隔
	 * @param  array|string     $input   记录信息，关联数组或字符串
	 * @param  int|string|array $where   where 条件
	 * @param  int|string       $limit   [可选]限制记录条数，默认 0(无限制)
	 * @param  string           $orderby [可选]排序规则
	 * @return boolean
	 */
	static function update($table, $input, $where, $limit = 0, $orderby = ''){
		if(!$input) return false;
		$prefix = self::set('prefix');
		$where = self::parseWhere($where);
		$tables = explode(',', str_replace(' ', '', $table));
		foreach ($tables as &$table) { //自动添加数据表前缀(如果有)
			if($prefix) $where = preg_replace('/\b'.$table.'\./', $prefix.$table, $where);
			$table = "`{$prefix}{$table}`";
		}
		$tables = implode(',', $tables);
		$kvs = '';
		if(!is_array($input)){
			$kvs = $input;
		}else{
			foreach ($input as $k => $v) {
				$kvs .= "`$k` = ".self::quote(trim($v)).","; //组合键值对
			}
		}
		$sql = "UPDATE $tables SET ".rtrim($kvs, ',')." WHERE $where".($orderby ? " ORDER BY $orderby" : '').($limit ? " LIMIT $limit" : '');
		return self::query($sql) != false;
	}
	/**
	 * select() 查询记录
	 * @static
	 * @param  string           $table   表名(不含前缀)，多个表用 , 分隔
	 * @param  string           $key     [可选]指定字段， * (默认)表示所有字段
	 * @param  int|string|array $where   [可选]where 条件
	 * @param  int|string       $limit   [可选]限制记录条数或区间，默认 0(无限制)
	 * @param  string           $orderby [可选]排序规则
	 * @return object                    PDOStatement 对象
	 */
	static function select($table, $key = '*', $where = 1, $limit = 0, $orderby = ''){
		$prefix = self::set('prefix');
		$where = self::parseWhere($where); //解析 where 条件
		$tables = explode(',', str_replace(' ', '', $table));
		foreach ($tables as &$table) { //自动添加数据表前缀(如果有)
			if($prefix){
				$where = preg_replace('/\b'.$table.'\./', $prefix.$table.'.', $where);
				$key = preg_replace('/\b'.$table.'\./', $prefix.$table.'.', $key);
			}
			$table = "`{$prefix}{$table}`";
		}
		$tables = implode(',', $tables);
		$keys = explode(',', $key);
		foreach ($keys as &$v) { //自动决定 select 的对象是否需要加反引号
			$v = trim($v);
			$v = (strpos($v, '.') || strpos($v, '(') || stripos($v, 'as') || $v[0] == '`' || $v == '*') ? $v : "`$v`";
		}
		$keys = implode(',', $keys);
		$sql = "SELECT $keys".($tables ? " FROM $tables WHERE $where".($orderby ? " ORDER BY $orderby" : '').($limit ? " LIMIT $limit" : '') : '');
		return self::query($sql); //返回查询结果 PDOStatement
	}

	/**
	 * delete() 删除记录
	 * @static
	 * @param  string           $table   表名(不含前缀)，多个表用 , 分隔
	 * @param  int|string|array $where   where 条件
	 * @param  int|string       $limit   [可选]限制记录条数，默认 0(不限制)
	 * @param  string           $orderby [可选]排序规则
	 * @return boolean
	 */
	static function delete($table, $where, $limit = 0, $orderby = ''){
		$prefix = self::set('prefix');
		$where = self::parseWhere($where); //解析 where 条件
		$tables = explode(',', str_replace(' ', '', $table));
		foreach ($tables as &$table) { //自动添加数据表前缀(如果有)
			if($prefix) $where = preg_replace('/\b'.$table.'\./', $prefix.$table, $where);
			$table = "`{$prefix}{$table}`";
		}
		$tables = implode(',', $tables);
		$sql = "DELETE FROM $tables WHERE $where".($orderby ? " ORDER BY $orderby" : '').($limit ? " LIMIT $limit" : '');
		return self::query($sql) != false;
	}

	/**
	 * parseWhere() 解析 where 条件数组
	 * @param  array  $input where 关联数组，有以下规则(a,b,d 表示字段名，c 表示值，e,f 表示表名)：
	 *                       [a] == c   表示 `a` = 'c'
	 *                       [a|b] == c 表示 `a` = 'c' OR `b` = 'c'
	 *                       [a&b] == c 表示 `a` = 'c' AND `b` = 'c'
	 *                       [a] == {e.d} 表示 `a` = e.d，AND 和 OR 向上参考
	 *                       [{f.a}] == {e.d} 表示 f.a = e.d
	 * @return string        where 条件语句
	 */
	static function parseWhere($input){
		if(!is_array($input)) return $input;
		if(!$input) return 1;
		$where = '';
		$regex = '/\*|\.|`|^-?[1-9]\d*$/';
		$keys = array_keys($input);
		foreach ($input as $k => $v) {
			$k = str_replace(array('{', '}'), '', $k); //取出键中的 {}
			$and = strpos($k, '&'); //AND 语句
			$or = strpos($k, '|'); //OR 语句
			$field = strpos($v, '{') === 0 && strpos($v, '.');
			$v = $field ? trim($v, '{}') : self::quote($v);
			if($and || $or){ //OR 语句，多键共用值
				$ks = explode(($and ? '&' : '|'), $k);
				$aWhere = array();
				foreach ($ks as $_k) {
					$aWhere[] = (preg_match($regex, $_k) ? $_k : "`{$_k}`")." = ".$v;
				}
				$where .= '('.implode(($and ? ' AND ' : ' OR '), $aWhere).') AND '; //组合条件
			}else{
				$where .= (preg_match($regex, $k) ? $k : "`$k`")." = ".$v.' AND ';
			}
		}
		return substr($where, 0, strlen($where)-5);
	}

	/**
	 * getPDO() 获取底层 PDO 实例
	 * @return PDO Object
	 */
	static function getPDO(){
		return self::$link[self::$name];
	}
}