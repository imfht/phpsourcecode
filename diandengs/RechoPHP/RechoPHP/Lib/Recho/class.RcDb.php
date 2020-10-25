<?php
// +----------------------------------------------------------------------
// | RechoPHP [ WE CAN DO IT JUST Better ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2014 http://recho.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: recho <diandengs@gmail.com>
// +----------------------------------------------------------------------

/**
 * RcDb class 连惯操作类
 * $Author: Recho $license: http://www.recho.net/ $
 * $create time: 2012-08-26 14:20
 * $last update time: 2012-08-26 18:50 Recho $
 */
include_once dirname(__FILE__).'/class.Db.php';
class RcDb extends Db{

	static $_instance, $arrServers_,$_rcdata = array('field'=>'*','page'=>false,'order','limit','where','join','table');
	
	public function __construct( $arrServers){
		parent::__construct( $arrServers);
		self::$arrServers_ = $arrServers;
	}
	
	public function __destruct(){
		$this->__freeMemory();
	}
	public function __freeMemory(){
		self::$_rcdata = null;
	}
	
	public static function getInstance(){
		if( empty(self::$_rcdata['table'])) exit('error, db table do not have a null!');
		if(!(self::$_instance instanceof self)){
			self::$_instance = new self( self::$arrServers_);
        }
		return self::$_instance;
	}
	
	public function findAll( $noPrint=true){
		extract( self::$_rcdata);
		$sql = "SELECT $field FROM $table $join $where $order $limit";
		if( !$noPrint){echo $sql;exit;}
		self::$_rcdata['lastsql'] = $sql;
		$list = $this->getAll( $sql, MYSQL_ASSOC);
		if( self::$_rcdata['page'] && !empty($list)){
			$sql = "SELECT COUNT(*) AS count FROM $table $join $where $order";
			$rows = $this->getOne( $sql, MYSQL_ASSOC);$rows = $rows['count'];
			$list = array( 'aList'=>$list, 'pageInfo'=>functions::pageInfo( $rows, self::$_rcdata['limit_get']['pagesize'], self::$_rcdata['limit_get']['page']));
		}
		$this->__freeMemory();
		return $list;
	}
	
	public function delete(){
		extract( self::$_rcdata);
		if( !empty( $where)){
			$sql = "DELETE FROM $table WHERE $where";
			self::$_rcdata['lastsql'] = $sql;
			$this->query( $sql);
			return $this->affectedRows();
		}
		$this->__freeMemory();
		return false;
	}

	public function update( $info=false){
		extract( self::$_rcdata);$sets = '';
		if( !is_array( $info) || empty($info) || empty( $where)) return false;
		foreach( $info as $key=>$value){
			$value = $this->escape( $value);
			$sets .= "`$key`='$value',";
		}
		if( !empty( $sets)) $sets = substr( $sets, 0, -1);
		$sql = "UPDATE $table SET $sets $where";
		self::$_rcdata['lastsql'] = $sql;
		$this->query( $sql);
		$this->__freeMemory();
		return $this->affectedRows();
	}
	
	public function add( $info){
		if( !is_array( $info) || empty($info)) return false;
		extract( self::$_rcdata);$sets = '';
		foreach( $info as $key=>$value){
			$value = $this->escape( $value);
			$sets .= "`$key`='$value',";
		}
		if( !empty( $sets)) $sets = substr( $sets, 0, -1);
		$sql = "INSERT INTO $table SET $sets";
		self::$_rcdata['lastsql'] = $sql;
		$this->query( $sql);
		$this->__freeMemory();
		return $this->affectedRows();
	}
	
	public function table( $table){
		self::$_rcdata['table'] = $table;
		return self::getInstance();
	}

	public function page( $page=1, $pagesize=20){
		$page = ($page=functions::uint( $page)) ? $page:1;
		$pagesize = ($pagesize=functions::uint( $pagesize)) ? $pagesize:20;
		$startRow = ($page-1)*$pagesize;
		self::$_rcdata['limit_get'] = array('page'=>$page, 'pagesize'=>$pagesize);
		self::$_rcdata['limit'] = "LIMIT $startRow, $pagesize";
		self::$_rcdata['page'] = true;
		return self::getInstance();
	}
	
	public function limit( $startRow, $count){
		if( !self::$_rcdata['page']){
			$startRow = ($startRow=functions::uint( $startRow)) ? $startRow:1;
			$count = ($count=functions::uint( $count)) ? $count:1;
			self::$_rcdata['limit'] = "LIMIT $startRow, $count";
		}
		return self::getInstance();
	}
	
	public function order( $order){
		if( is_array( $order) && !empty( $order)){
			$orders = '';
			foreach( $order as $key=>$value){
				if( is_int( $key)) $orders .= "$value,";
				else $orders .= "$key $value,";
			}
			if( !empty($orders)) $order = substr( $orders, 0, -1);
		}
		if( !empty( $order)){
			if( empty(self::$_rcdata['order']))
				self::$_rcdata['order'] = "ORDER BY $order";
			else 
				self::$_rcdata['order'] .= ",$order";
		}
		return self::getInstance();
	}

	public function where( $where){
		if( empty( $where)) return self::getInstance();
		if( is_array( $where)){
			$arr = array('eq'=>'=', 'neq'=>'!=', 'gt'=>'>', 'egt'=>'>=', 'lt'=>'<', 'elt'=>'<=', 'like'=>'like', 'in'=>'in', 'not in'=>'not in', 'and'=>'and', 'or'=>'or', 'xor'=>'xor');
			foreach( $where as $key=>$value){
				if( count($value)!=2 && !in_array( $value[0], array_keys($arr))) exit("the {$value[0]} is invalid one the sql to where!");
				if( $value[0]=='in'){
					if( is_array($value[1])) $value[1] = implode( $value[1], ',');
					$value[1] = "({$value[1]})";
				}else{
					$value[1] = "'{$value[1]}'";
				}
				if( empty( self::$_rcdata['where'])){
					self::$_rcdata['where'] .= " WHERE $key {$arr[$value[0]]} {$value[1]}";
				}
				else
					self::$_rcdata['where'] .= " AND $key {$arr[$value[0]]} {$value[1]}";
			}
		}else{
			if( empty( self::$_rcdata['where']))
				self::$_rcdata['where'] = " WHERE $where";
			else 
				self::$_rcdata['where'] .= " $where";
		}
		return self::getInstance();
	}
	
	public function field( $field){
		if( !empty($field)){
			if( is_array( $field)){
				$fields = '';
				foreach( $field as $value) $fields .= "$value,";
				if( !empty($fields)) $field = substr( $fields, 0, -1);else $field = '*';
			}
			self::$_rcdata['field'] = $field;
		}
		return self::getInstance();
	}
	
	public function getLastsql(){
		return self::$_rcdata['lastsql'];
	}
	
	public function join( $join){
		if( empty($join)) return self::getInstance();
		$jopt = "LEFT JOIN";$joins = '';
		if( is_array( $join)){
			foreach( $join as $value){
				if( preg_match("/RIGHT/", $value)) $jopt = '';
				if( empty(self::$_rcdata['join']))
					$joins = " $jopt $value";
				else 
					$joins .= " $jopt $value";
			}
			self::$_rcdata['join'] = $joins;
		}else{
			if( preg_match("/RIGHT/", $join)) $jopt = '';
			if( empty(self::$_rcdata['join']))
				$joins = " $jopt $join";
			else 
				$joins .= " $jopt $join";
			self::$_rcdata['join'] .= $joins;
		}
		return self::getInstance();
	}
}