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
 * 需tokyo_tyrant扩展支持 仅支持Table型数据库
 * 每个Database相当于一个表.每列是一个简单元素
 */
class TyrantTable{
	private $oTyrantTable = null; //连接对象
	private $arrServer = array(); //地址配置
	private $connected = false; //是否已经连接过
	private $persistent = false; //是否长连接
	/**
	 * @var TyrantQuery
	 */
	private $oTyrantQuery = null; //查询对象
	/**
	 * 删除索引
	 */
	const RDBIT_VOID = TokyoTyrant::RDBIT_VOID;
	/**
	 * 十六进制
	 */
	const RDBIT_LEXICAL = TokyoTyrant::RDBIT_LEXICAL;
	/**
	 * 十进制
	 */
	const RDBIT_DECIMAL = TokyoTyrant::RDBIT_DECIMAL;
	/**
	 * 令牌倒序
	 */
	const RDBIT_TOKEN = TokyoTyrant::RDBIT_TOKEN;
	/**
	 * QGRAM排序
	 */
	const RDBIT_QGRAM = TokyoTyrant::RDBIT_QGRAM;
	/**
	 * 优化索引
	 */
	const RDBIT_OPT = TokyoTyrant::RDBIT_OPT;
	/**
	 * 保持索引
	 */
	const RDBIT_KEEP = TokyoTyrant::RDBIT_KEEP;
	/**
	 * 覆盖式写入
	 */
	const WRITEOVER = 0;
	/**
	 * 添加式写入
	 */
	const WRITEADD = 1;
	/**
	 * 连接式写入.向$key指定的行追加列(形如Mysql加一列).$key为null则添加一行,如果有相同的列则该列不会被覆盖
	 */
	const WRITECAT = 2;
	
	public function __construct( $arrServer, $persistent=false){ //构造函数	
		$this->arrServer = $arrServer;
		$this->persistent = $persistent;
		if( ! class_exists( 'TokyoTyrantTable')){ //强制使用
			die('This Lib Requires The TokyoTyrant Extention!');
		}
	}
	
	private function connect(){
		if ( ! $this->connected){
			$this->connected = true; //标志已经连接过一次
			try {
				$this->oTyrantTable = new TokyoTyrantTable($this->arrServer[0], $this->arrServer[1], array('timeout'=>5, ' reconnect'=>true, $this->persistent?true:false));
			}catch (TokyoTyrantException $e){ //连接失败,记录
				$this->errorlog('Connect', $e->getCode(), $e->getMessage());
			}
		}
		return is_object( $this->oTyrantTable);
	}
	
	/**
	 * 添加或修改一行. 成功返回唯一标识,否则为false
	 * @param $key 行标志为整型值,相当于表结构中的自增长ID,如果设置为null,则新加一行
	 * @param $columns 一个键值对的数组,相当于数据库的 mysql_fetch_array($result, MYSQL_ASSOC)获取过来的值,注意键名一定要是字符串型,是字符串或数字,否则不能搜索更不能正确取回
	 * @param $op 写入方式,参考 self::WRITE*
	 */
	public function put($key, $columns, $op=0){
		if( !is_array($columns) || ! $this->connect()){
			return false;
		}
		try {
			$flag = true;
			switch ( $op){
				case TyrantTable::WRITECAT:  //向$key指定的行追加列(形如Mysql加一列).$key为null则添加一行,如果有相同的列则该列不会被覆盖
					$id = $this->oTyrantTable->putCat($key, $columns);
				break;
				case TyrantTable::WRITEADD: //添加一行.如果存在该ID则返回false,否则返回ID.$key为null则加入
					$id = $this->oTyrantTable->putKeep($key, $columns);
				break;
				case TyrantTable::WRITEOVER:
					$id = $this->oTyrantTable->put($key, $columns);
				default:
				break;
			}
			
		}catch (TokyoTyrantException $e){
			$flag = false;
			$this->errorlog($key, $e->getCode(), $e->getMessage());
		}
		return $flag ? $id : $flag;
	}
	/**
	 * 根据唯一ID获取一个值. *此处有缺陷,只能一次获取一个
	 */
	public function get( $key){
		if( ! $this->connect()){
			return false;
		}
		try {
			$flag = true;
			$result = $this->oTyrantTable->get( $key);
		}catch (TokyoTyrantException $e){
			$flag = false;
			$this->errorlog($key, $e->getCode(), $e->getMessage());
		}
		
		return $flag ? $result : $flag;
	}
	/**
	 * 获取所有键值对
	 */
	public function getIterator(){
		if( ! $this->connect()){
			return array();
		}
		try {
			$flag = true;
			$result = $this->oTyrantTable->getIterator();
		}catch (TokyoTyrantException $e) {
		    $flag = false;
			$this->errorlog('getIterator', $e->getCode(), $e->getMessage());
		}
		
		return $flag ? $result : array();
	}
	/**
	 * 根据唯一ID删除一行或多行值 $keys 为单个ID或数组ID
	 */
	public function out( $keys){
		if( ! $this->connect()){
			return false;
		}
		try {
			$flag = true;
			$this->oTyrantTable->out( $keys);
		}catch (TokyoTyrantException $e){
			$flag = false;
			$this->errorlog($keys, $e->getCode(), $e->getMessage());
		}
		return $flag;
	}
	/**
	 * 返回一个查询对象
	 * @return TyrantQuery
	 */
	public function getQuery(){
		if( ! $this->connect()){
			return false;
		}
		try {
			$flag = true;
			is_a($this->oTyrantQuery, 'TyrantQuery') ? '' : ($this->oTyrantQuery = new TyrantQuery());
			$this->oTyrantQuery->oTyrantQuery = $this->oTyrantTable->getQuery();
		}catch (TokyoTyrantException $e){
			$flag = false;
			$this->errorlog($keys, $e->getCode(), $e->getMessage());
		}
		return $flag ? $this->oTyrantQuery : $flag;
	}
	/**
	 * 在某一列上设置索引 列名, 索引类型:参考self::RDBIT_*
	 */
	public function setIndex($column, $type){
		if( ! $this->connect()){
			return false;
		}
		try {
			$flag = true;
			$this->oTyrantTable->setIndex( $column, $type);
		}catch (TokyoTyrantException $e){
			$flag = false;
			$this->errorlog($column, $e->getCode(), $e->getMessage());
		}
		return $flag;
	}
	/**
	 * 获取一个唯一值.每行记录都有个唯一值
	 */
	public function genUid(){
		if( ! $this->connect()){
			return false;
		}
		try {
			$flag = true;
			$result = $this->oTyrantTable->genUid();
		}catch (TokyoTyrantException $e){
			$flag = false;
			$this->errorlog('genUid', $e->getCode(), $e->getMessage());
		}
		return $flag ? $result : $flag;
	}
	private function errorlog($keys, $code, $msg){
		$error = date('H:i:s').":\n".$code.";\nkeys:".var_export($keys, true).";\nmsg:{$msg}\n";
		$file = RECHO_PHP . 'Runtime/Log/tyranttable.txt';
		@file_put_contents($file, $error, @filesize($file)<1024*1024 ? FILE_APPEND : null);
	}
}
class TyrantQuery{
	public $oTyrantQuery = null; //查询对象
	/**
	 * 字符串相等;相当于Mysql的 WHERE mnick LIKE 'mnick'
	 */
	const RDBQC_STREQ = TokyoTyrant::RDBQC_STREQ;
	/**
	 * 字符串包含; WHERE mnick LIKE '%mnick%'
	 */
	const RDBQC_STRINC = TokyoTyrant::RDBQC_STRINC;
	/**
	 * 以**开头; WHERE mnick LIKE 'mnick%'
	 */
	const RDBQC_STRBW = TokyoTyrant::RDBQC_STRBW;
	/**
	 * 以**结尾; WHERE mnick LIKE '%mnick'
	 */
	const RDBQC_STREW = TokyoTyrant::RDBQC_STREW;
	/**
	 * 包含所有列出的字符串; WHERE mnick IN ('mnick0'; 'mnick1'...)
	 */
	const RDBQC_STRAND = TokyoTyrant::RDBQC_STRAND;
	/**
	 * 类似其中一个字符串; WHERE mnick LIKE '%mnick0%' OR mnick LIKE '%mnick1%'...
	 */
	const RDBQC_STROR = TokyoTyrant::RDBQC_STROR;
	/**
	 * 等于其中一个字符串; WHERE mnick='mnick0' OR mnick='mnick1'...
	 */
	const RDBQC_STROREQ = TokyoTyrant::RDBQC_STROREQ;
	/**
	 * 匹配正则
	 */
	const RDBQC_STRRX = TokyoTyrant::RDBQC_STRRX;
	/**
	 * 数字 =
	 */
	const RDBQC_NUMEQ = TokyoTyrant::RDBQC_NUMEQ;
	/**
	 * 数字  >
	 */
	const RDBQC_NUMGT = TokyoTyrant::RDBQC_NUMGT;
	/**
	 * 数字 >=
	 */
	const RDBQC_NUMGE = TokyoTyrant::RDBQC_NUMGE;
	/**
	 * 数字 <
	 */
	const RDBQC_NUMLT = TokyoTyrant::RDBQC_NUMLT;
	/**
	 * 数字 <=
	 */
	const RDBQC_NUMLE = TokyoTyrant::RDBQC_NUMLE;
	/**
	 * 数字 > a AND < b
	 */
	const RDBQC_NUMBT = TokyoTyrant::RDBQC_NUMBT;
	/**
	 * 数字 =a OR =b OR ...
	 */
	const RDBQC_NUMOREQ = TokyoTyrant::RDBQC_NUMOREQ;
	/**
	 * 非1
	 */
	const RDBQC_NEGATE = TokyoTyrant::RDBQC_NEGATE;
	/**
	 * 没有索引
	 */
	const RDBQC_NOIDX = TokyoTyrant::RDBQC_NOIDX;
	/**
	 * 返回所有符合条件的结果
	 */
	const RDBMS_UNION = TokyoTyrant::RDBMS_UNION;
	/**
	 * 返回除后面结果的
	 */
	const RDBMS_ISECT = TokyoTyrant::RDBMS_ISECT;
	/**
	 * 返回剩余结果的
	 */
	const RDBMS_DIFF = TokyoTyrant::RDBMS_DIFF;
	/**
	 * 字符串升序排列
	 */
	const RDBQO_STRASC = TokyoTyrant::RDBQO_STRASC;
	/**
	 * 字符串降序排列
	 */
	const RDBQO_STRDESC = TokyoTyrant::RDBQO_STRDESC;
	/**
	 * 数字升序排列
	 */
	const RDBQO_NUMASC = TokyoTyrant::RDBQO_NUMASC;
	/**
	 * 数字降序排列
	 */
	const RDBQO_NUMDESC = TokyoTyrant::RDBQO_NUMDESC;
	
	/**
	 * 增加查询条件: 在$name列上找符合$op和$expr计算后的条件的所有key $op:参考self::RDBQC_*
	 */
	public function addCond( $name, $op, $expr){
		if( ! is_a($this->oTyrantQuery, 'TokyoTyrantQuery')){
			return false;
		}
		try{
			$flag = true;
			$this->oTyrantQuery->addCond($name, $op, $expr);
		}catch (TokyoTyrantException $e){
			$flag = false;
			$this->errorlog($name, $e->getCode(), $e->getMessage());
		}
		return $flag;
	}
	/**
	 * 删除所有符合搜索条件的行
	 */
	public function out(){
		if( ! is_a($this->oTyrantQuery, 'TokyoTyrantQuery')){
			return false;
		}
		try{
			$flag = true;
			$this->oTyrantQuery->out();
		}catch (TokyoTyrantException $e){
			$flag = false;
			$this->errorlog('out', $e->getCode(), $e->getMessage());
		}		
		return $flag;
	}
	/**
	 * 返回当前指针指向行的键
	 */
	public function key(){
		if( ! is_a($this->oTyrantQuery, 'TokyoTyrantQuery')){
			return false;
		}
		return $this->oTyrantQuery->key();
	}
	/**
	 * 返回当前指针指向行的值
	 */
	public function current(){
		if( ! is_a($this->oTyrantQuery, 'TokyoTyrantQuery')){
			return false;
		}
		return $this->oTyrantQuery->current();
	}
	/**
	 * 返回当前指针的下一行
	 */
	public function next(){
		if( ! is_a($this->oTyrantQuery, 'TokyoTyrantQuery')){
			return false;
		}
		return $this->oTyrantQuery->next();
	}
	/**
	 * 返回当前指针的有效性,用于while循环,典型用例:
	 * 	$o->rewind();
		while ($o->valid()){
			echo $o->key() . ':';
			print_r($o->current());
			echo '<br />';
			$o->next();
		}
	 */
	public function valid(){
		if( ! is_a($this->oTyrantQuery, 'TokyoTyrantQuery')){
			return false;
		}
		return $this->oTyrantQuery->valid();
	}
	/**
	 * 重置搜索结果指针,如果没有则执行搜索
	 */
	public function rewind(){
		if( ! is_a($this->oTyrantQuery, 'TokyoTyrantQuery')){
			return false;
		}
		return $this->oTyrantQuery->rewind();
	}
	/**
	 * 获取查询的描述文本,相当于Mysql的EXPLAIN SELECT ....
	 */
	public function hint(){
		if( ! is_a($this->oTyrantQuery, 'TokyoTyrantQuery')){
			return false;
		}
		return $this->oTyrantQuery->hint();
	}
	/**
	 * 进行联合查询$queries:查询句柄数组 $type:参考self::RDBMS_*
	 */
	public function metaSearch($queries, $type){
		if( ! is_a($this->oTyrantQuery, 'TokyoTyrantQuery')){
			return false;
		}
		try{
			$flag = true;
			$result = $this->oTyrantQuery->metaSearch((array)$queries, $type);
		}catch (TokyoTyrantException $e){
			$flag = false;
			$this->errorlog($name, $e->getCode(), $e->getMessage());
		}		
		return $flag ? $result : $flag;
	}
	/**
	 * 执行查询,返回符合条件的array(主键 => 值...)数组
	 */
	public function search(){
		if( ! is_a($this->oTyrantQuery, 'TokyoTyrantQuery')){
			return false;
		}
		try{
			$flag = true;
			$result = $this->oTyrantQuery->search();
		}catch (TokyoTyrantException $e){
			$flag = false;
			$this->errorlog('search', $e->getCode(), $e->getMessage());
		}
		return $flag ? $result : $flag;
	}
	/**
	 * 指定获取结果的行数和开始指针,形如Mysql的 LIMIT m,n -1为不限制
	 */
	public function setLimit($max, $skip){
		if( ! is_a($this->oTyrantQuery, 'TokyoTyrantQuery')){
			return false;
		}
		try{
			$flag = true;
			$result = $this->oTyrantQuery->setLimit($max, $skip);
		}catch (TokyoTyrantException $e){
			$flag = false;
			$this->errorlog('setLimit', $e->getCode(), $e->getMessage());
		}
		return $flag;
	}
	/**
	 * 在结果中对$name进行$op排序 $op参考self::RDBQO_*
	 */
	public function setOrder($name, $op){
		if( ! is_a($this->oTyrantQuery, 'TokyoTyrantQuery')){
			return false;
		}
		try{
			$flag = true;
			$result = $this->oTyrantQuery->setOrder($name, $op);
		}catch (TokyoTyrantException $e){
			$flag = false;
			$this->errorlog('setOrder', $e->getCode(), $e->getMessage());
		}
		return $flag;
	}
	/**
	 * 返回查询记录条数
	 */
	public function count(){
		if( ! is_a($this->oTyrantQuery, 'TokyoTyrantQuery')){
			return false;
		}
		try{
			$flag = true;
			$result = $this->oTyrantQuery->count();
		}catch (TokyoTyrantException $e){
			$flag = false;
			$this->errorlog($name, $e->getCode(), $e->getMessage());
		}
		return $flag ? $result : $flag;
	}

	private function errorlog($keys, $code, $msg){
		$error = date('H:i:s').":\n".$code.";\nkeys:".var_export($keys, true).";\nmsg:{$msg}\n";
		$file = RECHO_PHP . 'Runtime/Log/tyranttablequery.txt';
		@file_put_contents($file, $error, @filesize($file)<512*1024 ? FILE_APPEND : null);
	}
}