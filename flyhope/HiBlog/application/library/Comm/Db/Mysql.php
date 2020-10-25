<?php
/**
 * 数据库操作类
 *
 * @author chengxuan <i@chengxuan.li>
 */
namespace Comm\Db;
class Mysql {

	static protected $_dsn = 'mysql:dbname=%s;host=%s;port=%u;charset=%s';

	/**
	 * 自动选择
	 * 
	 * @var int
	 */
	const MODE_AUTO = 0;
	
	/**
	 * 使用读库
	 * 
	 * @var int
	 */
	const MODE_READ = 1;
	
	/**
	 * 使用写库
	 * 
	 * @var int
	 */
	const MODE_WRITE = 2;
	
	/**
	 * 配置文件操作类(必需继承于Yaf_Config_Abstract)
	 * 
	 * @var string
	 */
	static public $config_class = 'Yaf_Config_Ini';
	
	/**
	 * 配置文件路径
	 * 
	 * @var string
	 */
	static public $config_path = 'env.ini';
	
	protected $_read_config = array();
	protected $_write_config = array();
	protected $_read_inst;
	protected $_write_inst;
	protected $_last_inst;
	protected $_mode = 0;
	protected $_alias = 'Undefined';
	
	/**
	 * 构造方法，配置数据库
	 * 
	 * @param string $alias 配置文件别名
	 */
	public function __construct($alias = 'database_main') {
		$this->configure($alias);
	}
	
	/**
	 * 通过别名配置数据库
	 * 
	 * @param string $alias 配置文件别名
	 * 
	 * @return \Comm\Db\Mysql
	 */
	public function configure($alias) {
	    $this->_alias = $alias;
		$config = new self::$config_class(CONF_PATH . self::$config_path);
		/* @var $config \Yaf_Config_Abstract */
		$this->_read_config = $config[$alias]->read;
		$this->_write_config = $config[$alias]->write;
		
		return $this;
	}

	/**
	 * 强制使用写库
	 * 
	 * @return Mysql
	 */
	public function setWrite() {
		$this->_mode = self::MODE_WRITE;
		return $this;
	}
	
	/**
	 * 强制使用写库
	 *
	 * @return Mysql
	 */
	public function setRead() {
	    $this->_mode = self::MODE_READ;
	    return $this;
	}

	/**
	 * 根据sql语句自行判断
	 * 
	 * @return Mysql
	 */
	public function setAuto() {
		$this->_mode = self::MODE_AUTO;
		return $this;
	}
	
	/**
	 * 使用主库执行一个回调方法，执行完后切回原来的模式
	 * 
	 * @param callable $callback
	 * 
	 * @return mixed
	 */
	public function useWrite(callable $callback) {
	    $mode = $this->_mode;
	    $this->_mode = self::MODE_WRITE;
	    $result = call_user_func($callback, $this);
	    $this->_mode = $mode;
	    
	    return $result;
	}

	/**
	 * 执行一个sql语句并返回影响行数。
	 *
	 * 如果在insert或者replace语句后需要获取 last insert id 请使用last_insert_id()方法
	 *
	 * @param string $sql	sql语句。不能为select语句
	 * 
	 * @param \int
	 */
	public function exec($sql, array $data = NULL) {
		$verb = self::_extractSqlVerb($sql);
		if ($verb === 'select') {
			throw new \Exception\Program('Can not execute a select sql');
		}

		$statement = $this->executeSql($sql, $data);
		return $statement->rowCount();
	}

	/**
	 * 执行提供的select语句并返回结果集。
	 *
	 * @param string $sql	sql语句。只能为select语句
	 * @param array $data
	 * 
	 * @return array
	 */
	public function fetchAll($sql, array $data = NULL) {
		$this->_validateSelect($sql);
		$statement = $this->executeSql($sql, $data);
		return $statement->fetchAll(\PDO::FETCH_ASSOC);
	}

	/**
	 * 执行提供的select语句并返回结果集(一行数据)。
	 *
	 * @param string $sql	sql语句。只能为select语句
	 * @param array $data
	 * @return array
	 */
	public function fetchRow($sql, array $data = NULL) {
		$this->_validateSelect($sql);
		$statement = $this->executeSql($sql, $data);
		return $statement->fetch(\PDO::FETCH_ASSOC);
	}

	/**
	 * 获取一列数据
	 *
	 * @param string $sql	sql语句。只能为select语句
	 * @param array  $data  PDO占位
	 * @param number $index 列位置
	 *
	 * @throws Comm_Exception_Program
	 * @return string
	 */
	public function fetchCol($sql, array $data = null, $index = 0) {
		$this->_validateSelect($sql);
		$statement = $this->executeSql($sql, $data);
		return $statement->fetchColumn($index);
	}
	
	/**
	 * 取得一个数据
	 * @param string $sql			SQL语句
	 * @param array $param			变量参数
	 * @param null/string $column	指定返回字段
	 * @param boolean $is_master		是否强制使用主库
	 * @return string/boolean
	 */
	public function fetchOne($sql, array $data = null, $index = null) {
	    $this->_validateSelect($sql);
	    $statement = $this->executeSql($sql, $data);
	    if (!$index || is_numeric($index)) {
	        return $statement ? $statement->fetchColumn((int)$index) : $statement;
	    } else {
	        $result = $this->fetchRow($sql, $data);
	        return isset($result[$index]) ? $result[$index] : false;
	    }
	}
	
	/**
	 * 取得插入的最后一条的ID
	 * 
	 * @return \int
	 */
	public function lastId() {
	    return $this->useWrite(function(Mysql $db) {
	        return $db->fetchOne('SELECT LAST_INSERT_ID()');
	    });
	}
	
	/**
	 * 判断一条SQL是否是SELECT语句，不是则抛出异常
	 * 
	 * @param string $sql
	 * 
	 * @return void
	 */
	protected function _validateSelect($sql) {
		$verb = self::_extractSqlVerb($sql);
		if ($verb !== 'select') {
			throw new \Exception\Program('Can not fetch on a non-select sql');
		}
	}

	/**
	 * PDO同名方法封装
	 *
	 * @see PDO::prepare()
	 * @param string $sql
	 * @return PDOStatement
	 */
	public function prepare($sql) {
		$pdo = $this->getInst($this->_detectSqlType($sql));
		$args = func_get_args();
		return call_user_func_array(array($pdo, 'prepare'), $args);
	}

	/**
	 * PDO同名方法封装
	 *
	 * @param string $sql
	 * 
	 * @return PDOStatement
	 */
	public function query($sql) {
		$pdo = $this->getInst($this->_detectSqlType($sql));
		$args = func_get_args();
		return call_user_func_array(array($pdo, 'query'), $args);
	}

	/**
	 * 魔术方法
	 * 
	 * @param string $func
	 * @param array  $args
	 */
	public function __call($func, $args) {
		//Convert do_something() style to dosomething()
		$func = str_replace('_', '', strtolower($func));
		//Because of class method name is case insensitive in PHP, so, this simple
		//    process is enough and fast.

		$mode = self::MODE_AUTO;
		if (in_array($func, array('lastinsertid', 'begintransaction', 'intransaction', 'commit', 'rollback'))) {
			$mode = self::MODE_WRITE;
		}
		return call_user_func_array(array($this->getInst($mode), $func), $args);
	}

	/**
	 * 执行一个sql并返回PDOStatement对象和执行结果。
	 *
	 * @param string $sql
	 * @param array $data
	 *
	 * @return PDOStatement
	 */
	public function executeSql($sql, array $data = NULL) {
		$statement = $this->prepare($sql);
		/* @var $statement \PDOStatement */

		if ($data) {
			$result = $statement->execute($data);
		} else {
			$result = $statement->execute();
		}
		if (!$result) {
			$error = $statement->errorInfo();
			if (is_array($error)) {
				$error = implode(',', $error);
				$code = (isset($error[2]) && is_numeric($error[2])) ? $error[2] : 0;
				
			} else {
				$error = strval($error);
			}
			throw new \Exception\Database($error, $code, [
			    'SQL'  => $statement->queryString,
			    'data' => $data,
			    'statement_error_code' => $statement->errorCode(),
			]);
		}

		return $statement;
	}
	
	/**
	 * 获取数据库别名
	 * 
	 * @return string
	 */
	public function showAlias() {
	    return $this->_alias;
	}
	
	/**
	 * 获取当前数据库配置
	 * 
	 * @return \Yaf_Config_Abstract
	 */
	public function showConfig() {
	    static $result = null;
	    if($result === null) {
	        $config = new self::$config_class(CONF_PATH . self::$config_path);
	        $result = $config[$this->_alias];
	    }
	    return $result;
	}

	/**
	 * 根据指定的类型获取pdo实例。
	 *
	 * @param int $mode
	 * @return PDO
	 */
	protected function getInst($mode) {
		if ($mode === self::MODE_AUTO) {
			if (null === $this->_last_inst) {
				//default read, unless set write mode
				$this->_last_inst = $this->getInst(
						$this->_mode === self::MODE_WRITE ? self::MODE_WRITE : self::MODE_READ
						);
			}
			return $this->_last_inst;
		}
		if ($mode === self::MODE_READ) {
			if (null === $this->_read_inst) {
				if (!$this->_read_config) {
					return $this->getInst(self::MODE_WRITE);
				}
				$this->_read_inst = $this->getPdo($this->_read_config);
			}
			$this->_last_inst = $this->_read_inst;
			return $this->_read_inst;
		}
		if ($mode === self::MODE_WRITE) {
			if (null === $this->_write_inst) {
				if (!$this->_write_config) {
					throw new \Exception\Program('Writable db must be defined');
				}
				$this->_write_inst = $this->getPdo($this->_write_config);
			}
			$this->_last_inst = $this->_write_inst;
			return $this->_write_inst;
		}
	}

	/**
	 * 获取PDO操作对象
	 * 
	 * @param array $config 配置
	 * 
	 * @throws \Exception\Database
	 */
	protected function getPdo($config) {
		try {
			$inst = new \PDO(sprintf(self::$_dsn, $config['name'], $config['host'], $config['port'], $config['charset']), $config['user'], $config['pass']);
		} catch (\Exception $ex) {
			throw new \Exception\Database($ex->getMessage());
		}
		return $inst;
	}

	/**
	 * 提取sql语句的动词
	 *
	 * @param string $sql
	 * @return string 动词
	 */
	static protected function _extractSqlVerb($sql) {
		$sql_components = explode(' ', ltrim($sql), 2);
		$verb = strtolower($sql_components[0]);
		return $verb;
	}

	/**
	 * 检测sql所需的数据库类型
	 * 
	 * @param string $sql
	 * @return ENUM
	 */
	protected function _detectSqlType($sql) {
	    $result = $this->_mode;
	    if($result === self::MODE_AUTO) {
	        $result = self::_extractSqlVerb($sql) === 'select' ? self::MODE_READ : self::MODE_WRITE;
	    }
		return $result;
	}
}

