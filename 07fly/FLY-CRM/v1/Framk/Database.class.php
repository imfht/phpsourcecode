<?php
/**
+------------------------------------------------------------------------------
* Framk PHP框架
+------------------------------------------------------------------------------
* @package  Framk
* @author   shawn fon <shawn.fon@gmail.com>
+------------------------------------------------------------------------------
*/

class Database {

	private $db;
	private $transTimes;
	/* 
	初始化数据库
	*/
	public

	function __construct() {

		//$this->db = _instance($GLOBALS['DB']['DBtype'],'',1);

		$host = $GLOBALS[ 'DB' ][ 'DBhost' ];
		$name = $GLOBALS[ 'DB' ][ 'DBname' ];
		$port = $GLOBALS[ 'DB' ][ 'DBport' ];
		$user = $GLOBALS[ 'DB' ][ 'DBuser' ];
		$pwd = $GLOBALS[ 'DB' ][ 'DBpsw' ];
		$this->db = new PDO( "mysql:host={$host};dbname={$name}", "{$user}", "{$pwd}" );
		$this->db->query( "SET NAMES 'UTF8'" );
		$this->db->query( "SET TIME_ZONE = '+8:00'" );

	}
	/*
	查询结果集并转换为二维数组
	*/
	public

	function findAll( $sql ) {
		$result = $this->db->query( $sql );
		if ( $result ) {
			$data = $result->fetchAll( PDO::FETCH_ASSOC );
		} else {
			_error( 'queryError', '数据表不存在 或SQL语法错误:' . $sql, true );
		}
		return $data;
	}
	/*
	查询结果集数组
	*/
	public

	function findOne( $sql ) {
		$result = $this->db->query( $sql );
		if ( $result ) {
			$data = $result->fetch();
		} else {
			_error( 'queryError', '数据表不存在 或SQL语法错误:' . $sql, true );
		}
		return $data;
	}

	/*
	数据记录条数
	*/
	public

	function countRecords( $sql ) {
		$result = $this->db->query( $sql );
		if ( $result ) {
			return $result->rowCount();
		} else {
			_error( 'queryError', '数据表不存在 或SQL语法错误:' . $sql, true );
		}
	}

	/*
	更新数据
	*/
	public

	function updt( $sql ) {
		$result = $this->db->exec( $sql );
		if ( $result ) { //如果数据操作返回为真	
			$sql = trim( $sql );
			$method = strtolower( substr( $sql, 0, 6 ) );
			if ( $method == 'insert' || $method == 'replace' ) {
				return $this->db->lastInsertId(); //如果为插入数据操作则返回新增记录的id
			} else {
				return $result; //否则返回影响的记录条数，其值有可能大于或等于零，因此在Adtion类方法中返回值大于或等于零表示操作无错误
			}
		} else {
			return false;
		}
	}
	/**
	 * Update 更新
	 *
	 * @param String $table 表名
	 * @param Array $arrayDataValue 字段与值
	 * @param String $where 条件
	 * @param Boolean $debug
	 * @return Int
	 */
	public
	function update( $table, $arrayDataValue, $where = '', $debug = false ) {
		$this->checkFields( $table, $arrayDataValue );
		if ( $where ) {
			$strSql = '';
			foreach ( $arrayDataValue as $key => $value ) {
				$strSql .= ", `$key`='$value'";
			}
			$strSql = substr( $strSql, 1 );
			$strSql = "UPDATE `$table` SET $strSql WHERE $where";
		} else {
			$strSql = "REPLACE INTO `$table` (`" . implode( '`,`', array_keys( $arrayDataValue ) ) . "`) VALUES ('" . implode( "','", $arrayDataValue ) . "')";
		}
		if ( $debug === true )$this->debug( $strSql );
		$result = $this->db->exec( $strSql );
		$this->getPDOError();
		return $result;
	}

	/**
	 * Insert 插入
	 *
	 * @param String $table 表名
	 * @param Array $arrayDataValue 字段与值
	 * @param Boolean $debug
	 * @return Int
	 */
	public
	function insert( $table, $arrayDataValue, $debug = false ) {
		$this->checkFields( $table, $arrayDataValue );
		$strSql = "INSERT INTO `$table` (`" . implode( '`,`', array_keys( $arrayDataValue ) ) . "`) VALUES ('" . implode( "','", $arrayDataValue ) . "')";
		if ( $debug === true )$this->debug( $strSql );
		$result = $this->db->exec( $strSql );
		$this->getPDOError();
		return $result;
	}

	/**
	 * Replace 覆盖方式插入
	 *
	 * @param String $table 表名
	 * @param Array $arrayDataValue 字段与值
	 * @param Boolean $debug
	 * @return Int
	 */
	public
	function replace( $table, $arrayDataValue, $debug = false ) {
		$this->checkFields( $table, $arrayDataValue );
		$strSql = "REPLACE INTO `$table`(`" . implode( '`,`', array_keys( $arrayDataValue ) ) . "`) VALUES ('" . implode( "','", $arrayDataValue ) . "')";
		if ( $debug === true )$this->debug( $strSql );
		$result = $this->db->exec( $strSql );
		$this->getPDOError();
		return $result;
	}

	/**
	 * Delete 删除
	 *
	 * @param String $table 表名
	 * @param String $where 条件
	 * @param Boolean $debug
	 * @return Int
	 */
	public
	function delete( $table, $where = '', $debug = false ) {
		if ( $where == '' ) {
			$this->outputError( "'WHERE' is Null" );
		} else {
			$strSql = "DELETE FROM `$table` WHERE $where";
			if ( $debug === true )$this->debug( $strSql );
			$result = $this->db->exec( $strSql );
			$this->getPDOError();
			return $result;
		}
	}

	/**
	 * execSql 执行SQL语句,debug=>true可打印sql调试
	 *
	 * @param String $strSql
	 * @param Boolean $debug
	 * @return Int
	 */
	public
	function execSql( $strSql, $debug = false ) {
		if ( $debug === true )$this->debug( $strSql );
		$result = $this->db->exec( $strSql );
		$this->getPDOError();
		return $result;
	}

	/**
	 * 获取字段最大值
	 * 
	 * @param string $table 表名
	 * @param string $field_name 字段名
	 * @param string $where 条件
	 */
	public
	function getMaxValue( $table, $field_name, $where = '', $debug = false ) {
		$strSql = "SELECT MAX(" . $field_name . ") AS MAX_VALUE FROM $table";
		if ( $where != '' )$strSql .= " WHERE $where";
		if ( $debug === true )$this->debug( $strSql );
		$arrTemp = $this->query( $strSql, 'Row' );
		$maxValue = $arrTemp[ "MAX_VALUE" ];
		if ( $maxValue == "" || $maxValue == null ) {
			$maxValue = 0;
		}
		return $maxValue;
	}

	/**
	 * 获取指定列的数量
	 * 
	 * @param string $table
	 * @param string $field_name
	 * @param string $where
	 * @param bool $debug
	 * @return int
	 */
	public
	function getCount( $table, $field_name, $where = '', $debug = false ) {
		$strSql = "SELECT COUNT($field_name) AS NUM FROM $table";
		if ( $where != '' )$strSql .= " WHERE $where";
		if ( $debug === true )$this->debug( $strSql );
		$arrTemp = $this->query( $strSql, 'Row' );
		return $arrTemp[ 'NUM' ];
	}

	/**
	 * 获取表引擎
	 * 
	 * @param String $dbName 库名
	 * @param String $tableName 表名
	 * @param Boolean $debug
	 * @return String
	 */
	public
	function getTableEngine( $dbName, $tableName ) {
		$strSql = "SHOW TABLE STATUS FROM $dbName WHERE Name='" . $tableName . "'";
		$arrayTableInfo = $this->query( $strSql );
		$this->getPDOError();
		return $arrayTableInfo[ 0 ][ 'Engine' ];
	}
	//预处理执行
	public
	function prepareSql( $sql = '' ) {
		return $this->db->prepare( $sql );
	}
	//执行预处理
	public
	function execute( $presql ) {
		return $this->db->execute( $presql );
	}

	/**
	 * pdo属性设置
	 */
	public
	function setAttribute( $p, $d ) {
		$this->db->setAttribute( $p, $d );
	}

	/**
	 * beginTransaction 事务开始
	 */
	public
	function begintrans() {
		$this->db->beginTransaction();
	}

	/**
	 * commit 事务提交
	 */
	public
	function commit() {
		$this->db->commit();
	}

	/**
	 * rollback 事务回滚
	 */
	public
	function rollback() {
		$this->db->rollback();
	}

	/**
	 * transaction 通过事务处理多条SQL语句
	 * 调用前需通过getTableEngine判断表引擎是否支持事务
	 *
	 * @param array $arraySql
	 * @return Boolean
	 */
	public
	function execTransaction( $arraySql ) {
		$retval = 1;
		$this->beginTransaction();
		foreach ( $arraySql as $strSql ) {
			if ( $this->execSql( $strSql ) == 0 )$retval = 0;
		}
		if ( $retval == 0 ) {
			$this->rollback();
			return false;
		} else {
			$this->commit();
			return true;
		}
	}

	/**
	 * checkFields 检查指定字段是否在指定数据表中存在
	 *
	 * @param String $table
	 * @param array $arrayField
	 */
	private
	function checkFields( $table, $arrayFields ) {
		$fields = $this->getFields( $table );
		foreach ( $arrayFields as $key => $value ) {
			if ( !in_array( $key, $fields ) ) {
				$this->outputError( "Unknown column `$key` in field list." );
			}
		}
	}

	/**
	 * getFields 获取指定数据表中的全部字段名
	 *
	 * @param String $table 表名
	 * @return array
	 */
	private
	function getFields( $table ) {
		$fields = array();
		$recordset = $this->db->query( "SHOW COLUMNS FROM $table" );
		$this->getPDOError();
		$recordset->setFetchMode( PDO::FETCH_ASSOC );
		$result = $recordset->fetchAll();
		foreach ( $result as $rows ) {
			$fields[] = $rows[ 'Field' ];
		}
		return $fields;
	}

	/**
	 * getPDOError 捕获PDO错误信息
	 */
	private
	function getPDOError() {
		if ( $this->db->errorCode() != '00000' ) {
			$arrayError = $this->db->errorInfo();
			$this->outputError( $arrayError[ 2 ] );
		}
	}

	/**
	 * debug
	 * 
	 * @param mixed $debuginfo
	 */
	private
	function debug( $debuginfo ) {
		var_dump( $debuginfo );
		exit();
	}

	/**
	 * 输出错误信息
	 * 
	 * @param String $strErrMsg
	 */
	private
	function outputError( $strErrMsg ) {
		throw new Exception( 'MySQL Error: ' . $strErrMsg );
	}

	/**
	 * destruct 关闭数据库连接
	 */
	public
	function destruct() {
		$this->db = null;
	}

	/**
	 * 数据库版本号
	 * @access function 
	 * @return boolen
	 */
	public

	function version() {
		$result = $this->db->query( 'select version()' );
		$version = $result->fetch();
		return $version;
	}

	//关闭连接
	public

	function __destruct() {
		$this->db = null;
	}


	/*  +------------------------------------------------------------------------------ */

} //

?>