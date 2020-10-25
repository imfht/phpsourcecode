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
MongoDB操作 !!!注意:如果连不上DB,则整个脚本会被errlog方法强制退出
$config['Mongo'] = "mongodb://127.0.0.1:27017,127.0.0.1:27018"; //mongodb://[username:password@]host1[:port1][,host2[:port2],...[,hostN[:portN]]][/database]多服务端以,隔开,注意其中一个正常就不会报错
$oo = new munongo( $config['Mongo'], false);
$odb = $oo->selectDB('manyou'); //选择数据库相当于select_db(),没有则会自动创建
$ocol = $odb->selectCollection('members'); //选择一个表,没有则自动创建
/*$aUser = array(
			'mid' => 11,
			'mnick' => 'zhoujian',
			'mgender' => 0,
			'mpage' => 'http://site.baidu.com/',
			'mmoney' => 5000,
				);
$result = $ocol->insert( $aUser, array()); //插入一行记录
*/
/*
for($mid=100; $mid<200; $mid++){
	$aUsers[] = array(
			'mid' => $mid,
			'mnick' => 'zhoujian'.$mid,
			'mgender' => 0,
			'mpage' => 'http://site.baidu.com/',
			'mmoney' => $mid,
				);
}
$result = $ocol->batchInsert( $aUsers, array()); //插入多行记录
*/
/*
$x = $ocol->findOne( array('mid' => array('$gt' => 5, '$lt' => 20))); //找出mid>5 && mid<20的记录,仅返回一行
print_r($x);
*/
/*
$oCursor = $ocol->find( array('mid' => array('$gt' => 5, '$lt' => 120))); //找出mid>5 && mid<120的记录,返回一个结果指针
$oCursor->sort(array('mid' => -1)); //结果按照mid降序排列
while ($oCursor->getNext()) {
	$aUsers[] = $oCursor->current();
}
print_r($aUsers);
**/
class Mumongo{
	private $oMongo = null; //暂存对象
	/**
	 * @var mumongodb
	 */
	private $oMuMongoDB = null; //暂存对象
	private $sServers = ''; //服务组
	private $connected = false; //是否已经连接过
	private $persist = false; //是否长连接
	
	/**
	 * @param String $sServers
	 * @param Boolean $persist
	 */
	public function __construct( $sServers, $persist=false){ //构造函数	
		$this->sServers = $sServers;
		$this->persist = $persist;
		if( ! class_exists( 'Mongo' )){ //强制使用
			die('This Lib Requires The Mongo Extention!');
		}
		//ini_set('mongo.cmd', ':');
	}
	/**
	 * @return Boolean 是否成功实例化
	 */
	private function connect(){
		if( ! $this->connected){
			$this->connected = true; //标志已经连接过一次
			try{
				$this->oMongo = $this->persist ? new Mongo($this->sServers, array('connect'=>true, 'persist'=>$this->genPool())) : new Mongo($this->sServers, array('connect'=>true));
			}catch (MongoConnectionException $e){
				$this->errorlog('Connect', $e->getCode(), $e->getMessage());
			}
		}
		return is_object( $this->oMongo);
	}

	/**
	 * 返回数据库列表
	 * @return array
	 */
	public function listDBs(){
		return $this->connect() ? $this->oMongo->listDBs() : array();
	}
	/**
	 * 返回某个库对象
	 * @param String/MongoDB $db
	 * @param String $collection
	 * @return muMongoCollection
	 */
	public function selectCollection( $db, $collection){
		return $this->selectDB( $db)->selectCollection( $collection);
	}
	
	/**
	 * 返回服务器连接端口的描述,不成功的端口会被标识出
	 * @return String
	 */
	public function __toString(){
		return $this->connect() ? $this->oMongo->__toString() : '';
	}
	
	/**
	 * 获取长连接名前缀,保证相同的端口配置用同一个长连接
	 */
	private function genPool(){
		return md5( $this->sServers);
	}
	/**
	 * 获取一个mongodb对象
	 * @param String $dbname 数据库名
	 * @return mumongodb
	 */
	public function __get( $dbname ){
		return $this->selectDB( $dbname);
	}
	/**
	 * 获取一个mongodb对象
	 * @param String $dbname 库名
	 * @return mumongodb
	 */
	public function selectDB( $dbname ){
		return is_object( $this->oMuMongoDB[$dbname]) ? $this->oMuMongoDB[$dbname] : ($this->connect() ? $this->oMuMongoDB[$dbname] = new mumongodb( $this->oMongo, $dbname) : null);
	}
	/**
	 * 删除表.注意不管库存在否都返回1
	 * @param Strint/mongodb $db 表名或表连接对象mumongodb
	 * @return array
	 */
	public function dropDB( $db){
		return $this->connect() ? $this->oMongo->dropDB( $db) : array();
	}
	
	/**
	 * 关闭连接.一般不需调用
	 * @return Boolean
	 */
	public function close(){
		return is_object( $this->oMongo) && $this->oMongo->close() && ($this->connected = false);
	}
	
	/**
	 * 错误日志
	 * @param String $key
	 * @param String $code
	 * @param String $msg
	 */
	private function errorlog($keys, $code, $msg){
		$error = date('H:i:s').":\n".$code.";\nkeys:".var_export($keys, true).";\nmsg:{$msg}\n";
		$file = RECHO_PHP . 'Runtime/Log/mongo.txt';
		@file_put_contents($file, $error, @filesize($file)<512*1024 ? FILE_APPEND : null);
		die('MongoDB Invalid!!!');
	}
}

class mumongodb{
	const PROFILING_OFF = MongoDB::PROFILING_OFF;
	const PROFILING_SLOW = MongoDB::PROFILING_SLOW;
	const PROFILING_ON = MongoDB::PROFILING_ON;
	
	private $oMongoDB = null;
	private $oMuMongoCollection = null;
	/**
	 * 实例化一个库
	 * @param Mongo $conn Mongo连接对象
	 * @param String $name 库名
	 * @return mumongodb
	 */
	public function __construct($conn, $name){
		$this->oMongoDB = new MongoDB( $conn, $name);
	}
	/**
	 * 验证用户名和密码
	 * @return Array Array ( [errmsg] => auth fails [ok] => 0 ) 
	 */
	public function authenticate( $username, $password){
		return $this->oMongoDB->authenticate( $username, $password);
	}
	/**
	 * 发送指令.参见: http://www.mongodb.org/display/DOCS/List+of+Database+Commands
	 * @param Array $data
	 */
	public function command( $data ){
		return $this->oMongoDB->command( $data);
	}
	/**
	 * 创建一个表
	 * @param String $name 表名
	 * @param Boolean $capped 是否定长
	 * @param int $size 如果定长,则需指定该长度
	 * @param int $max 如果是定长,则需指定最多行数
	 * @return muMongoCollection
	 */
	public function createCollection($name, $capped=false, $size=0, $max=0){
		$this->oMongoDB->createCollection($name, $capped, $size, $max); //创建
		return is_object( $this->oMuMongoCollection[$name]) ? $this->oMuMongoCollection[$name] : $this->oMuMongoCollection[$name] = new muMongoCollection($this->oMongoDB, $name);
	}
	/**
	 * 创建一个数据库
	 * @param unknown_type $collection
	 * @param Object $a Object or _id
	 * @return Array
	 */
	public function createDBRef( $collection, $a ){
		return $this->oMongoDB->createDBRef( $collection, $a);
	}
	/**
	 * 删除当前句柄所指的数据库
	 * @return Array ( [dropped] => admin.$cmd [ok] => 1 ) 
	 */
	public function drop(){
		return $this->oMongoDB->drop();
	}
	/**
	 * 删除当前数据库上的某个表
	 * @param String/MongoCollection $coll
	 * @return Array ( [dropped] => admin.$cmd [ok] => 1 ) 
	 */
	public function dropCollection( $coll){
		return $this->oMongoDB->dropCollection( $coll);
	}
	/**
	 * 执行js方法
	 * @param String $code "function(greeting, name) { return greeting+', '+name+'!'; }"
	 * @param Array $args array("Good bye", "Joe")
	 * @return Array ( [retval] => ** [ok] => 1 ) 
	 */
	public function execute( $code, $args){
		return $this->oMongoDB->execute( $code, $args);
	}
	/**
	 * 强制创建一个db错误
	 * @return Array
	 */
	public function forceError(){
		return $this->oMongoDB->forceError();
	}
	/**
	 * 选择表
	 * @param string $name
	 * @return muMongoCollection
	 */
	public function __get( $name){
		return $this->selectCollection( $name);
	}
	public function getDBRef( $ref ){
		return $this->oMongoDB->getDBRef( $ref);
	}
	public function getGridFS( $prefix="fs"){
		return $this->oMongoDB->getGridFS( $prefix);
	}
	public function getProfilingLevel(){
		return $this->oMongoDB->getProfilingLevel();
	}
	public function lastError(){
		return $this->oMongoDB->lastError();
	}
	/**
	 * 获取当前数据库中所有的表.注意要循环这个返回值才能看到表名
	 * @return Object
	 */
	public function listCollections(){
		return $this->oMongoDB->listCollections();
	}
	public function prevError(){
		return $this->oMongoDB->prevError();
	}
	/**
	 * 修复和整理数据库碎片
	 * @param Boolean $preserve_cloned_files
	 * @param Boolean $backup_original_files
	 * @return Array
	 */
	public function repair($preserve_cloned_files=false, $backup_original_files=false){
		return $this->oMongoDB->repair($preserve_cloned_files, $backup_original_files);
	}
	/**
	 * 重置错误信息
	 * @return Array
	 */
	public function resetError(){
		return $this->oMongoDB->resetError();
	}
	/**
	 * 选择一个表
	 * @param string $name
	 * @return muMongoCollection
	 */
	public function selectCollection( $name){
		return is_object( $this->oMuMongoCollection[$name]) ? $this->oMuMongoCollection[$name] : $this->oMuMongoCollection[$name] = new muMongoCollection($this->oMongoDB, $name);
	}
	public function setProfilingLevel( $level){
		return $this->oMongoDB->setProfilingLevel( $level);
	}
	public function __toString(){
		return $this->oMongoDB->__toString();
	}
}
class muMongoCollection{
	public $oMongoCollection = null;
	
	public function __construct( $db , $name ){
		$this->oMongoCollection = new MongoCollection( $db, $name);
	}
	/**
	 * 批量插入
	 * @param Array $a 要插入的数据数组
	 * @param Array $options = array('safe'=>0/1) 是否开启安全插入,开启后返回值不同.例如:insert保证_id的唯一性,如果没有设置safe,则不管有没有存储成功都返回true,否则如果已经有相同_id会抛出异常,返回false
	 * @return Boolean
	 */
	public function batchInsert( $a, $options=array()){
		try {
			$result = $this->oMongoCollection->batchInsert( $a, $options);
		}catch (Exception $e){
			$result = false;
		}
		return $result ? true : false;
	}
	/**
	 * 获取记录数
	 * @param Array $query 查询条件.默认为所有值
	 * @return int
	 */
	public function count( $query=array()){
		return $this->oMongoCollection->count( $query);
	}
	public function createDBRef( $a ){
		return $this->oMongoCollection->createDBRef( $a);
	}
	/**
	 * 删除该表某些列上的索引
	 * @param String/Array $keys 列名
	 * @return Array
	 */
	public function deleteIndex( $keys){
		return $this->oMongoCollection->deleteIndex( $keys);
	}
	/**
	 * 删除该表的所有索引
	 * @return Array
	 */
	public function deleteIndexes(){
		return $this->oMongoCollection->deleteIndexes();
	}
	/**
	 * 删除当前表
	 * @return Array
	 */
	public function drop(){
		return $this->oMongoCollection->drop();
	}
	/**
	 * 创建索引.如果存在则忽略
	 * @param Array $keys array('列名'=>'索引类型1升序-1降序')
	 * @param Array $options = array("unique是否唯一索引","dropDups如果是唯一索引是否删除重复的值","background是否延时加索引","safe是否保证安全","name索引名") 形如: array('unique'=>1)
	 * @return Boollen
	 */
	public function ensureIndex( $keys, $options ){
		try {
			$result = $this->oMongoCollection->ensureIndex( $keys, $options);
		}catch (Exception $e){
			$result = false;
		}
		return $result ? true : false;
	}
	/**
	 * 执行查询.获取结果集
	 * @param Array array('col' => array( '$gt' => 5, '$lt' => 20 )); $query 参见 http://www.mongodb.org/display/DOCS/Advanced+Queries#AdvancedQueries-ConditionalOperators%3A%3C%2C%3C%3D%2C%3E%2C%3E%3D
	 * 	$gt >
		$lt <
		$gte >=
		$lte <=
		$ne !=
		$in array()
		$nin array()
		$mod array(m, r)
		$all
		$size 元素个数
		$exists 存在这个属性
		$type 2字符串16整型
		/regument/i 匹配
		'string' 字符串相等
		$not /regument/i 不匹配
	 * @param Array $fields 需要取回的字段
	 * @return muMongoCursor
	 */
	public function find( $query=array(), $fields=array()){
		return new muMongoCursor( $this->oMongoCollection->find( $query, $fields));
	}
	/**
	 * 执行查询.获取其中一行
	 * @param Array $query
	 * @param Array $fields
	 * @return Array
	 */
	public function findOne( $query=array(), $fields=array()){
		$result = $this->oMongoCollection->findOne( $query, $fields);
		return is_array( $result) ? $result : array();
	}
	/**
	 * 选择表
	 * @param String $name
	 * @return Object
	 */
	public function __get( $name ){
		return $this->oMongoCollection->__get( $name);
	}
	public function getDBRef( $ref){
		return $this->oMongoCollection->getDBRef( $ref);
	}
	/**
	 * 获取索引信息
	 */
	public function getIndexInfo(){
		return $this->oMongoCollection->getIndexInfo();
	}
	/**
	 * 获取表名
	 */
	public function getName(){
		return $this->oMongoCollection->getName();
	}
	/**
	 * 执行一个类似Group By的操作
	 * @param String $keys
	 * @param unknown_type $initial
	 * @param unknown_type $reduce
	 * @param unknown_type $condition
	 * @return unknown
	 */
	public function group( $keys, $initial, $reduce, $condition=array()){
		return $this->oMongoCollection->group( $keys, $initial, $reduce, $condition);
	}
	/**
	 * 插入一行记录.如果有相同的_id,则插入失败
	 * @param Array $a
	 * @param Array $options = array('safe' => 0/1) 是否执行安全插入(返回值不同)
	 * @return Boolean
	 */
	public function insert( $a, $options=array()){
		try{
			$result = $this->oMongoCollection->insert( $a, $options);
		}catch (Exception $e){
			$result = false;
		}
		return $result ? true : false;
	}
	/**
	 * 删除指定记录
	 * @param Array $criteria 查询条件 
	 * @param Array $options = array('justOne"=>0/1是否只删一行, 'safe' => 0/1是否执行安全删除
	 * @return Boolean
	 */
	public function remove( $criteria, $options=array()){
		try {
			$result = $this->oMongoCollection->remove( $criteria, $options);
		}catch (Exception $e){
			$result = false;
		}
		return $result ? true : false;
	}
	/**
	 * 插入或更新一行.不管有没有相同的_id
	 * @param Array $a
	 * @param Array $options = array('safe' => 0/1)
	 * @return Boolean
	 */
	public function save( $a, $options=array() ){
		try{
			$result = $this->oMongoCollection->save( $a, $options);
		}catch (Exception $e){
			$result = false;
		}
		return $result ? true : false;
	}
	/**
	 * 信息为ns字符串显示
	 * @return String
	 */
	public function __toString(){
		return $this->oMongoCollection->__toString();
	}
	/**
	 * 更新相应条件下的记录
	 * @param Array $criteria
	 * @param Array $newobj
	 * @param Array $options = array("upsert"=>0/1没有记录是否插入,"multiple"=>0/1是否更新多行记录,"safe"=>0/1是否安全控制
	 * @return boolean
	 */
	public function update( $criteria, $newobj, $options=array()){
		try {
			$result = $this->oMongoCollection->update( $criteria, $newobj, $options);
		}catch (Exception $e){
			$result = false;
		}
		return $result ? true : false;
	}
	/**
	 * 校验数据
	 * @param Boolean $scan_data 是否
	 * @return Array
	 */
	public function validate( $scan_data=false){
		return $this->oMongoCollection->validate( $scan_data);
	}
}
class muMongoCursor implements Iterator{
	static $slaveOkay = false;
	/**
	 * @var MongoCursor
	 */
	private $oMongoCursor = null;
	
	/**public function __construct( $connection, $ns, $query=array(), $fields=array()){
		$this->oMongoCursor = new MongoCursor( $connection, $ns, $query, $fields);
	}**/
	public function __construct( $oMongoCursor){
		$this->oMongoCursor = $oMongoCursor;
	}
	/**
	 * 添加选项
	 * @param String $key
	 * @param String $value
	 * @return muMongoCursor
	 */
	public function addOption( $key, $value){
		$this->oMongoCursor->addOption( $key, $value);
		return $this;
	}
	/**
	 * 获取该查询的记录数
	 * @param Boolean $all
	 * @return int
	 */
	public function count( $all=false){
		return $this->oMongoCursor->count( $all);
	}
	/**
	 * 返回当前游标所指的数据
	 * @return Array
	 */
	public function current(){
		return $this->oMongoCursor->current();
	}
	/**
	 * 检查在服务端是否游标还存在
	 * @return Boolean
	 */
	public function dead(){
		return $this->oMongoCursor->dead();
	}
	/**
	 * 执行查询
	 * @return null
	 */
	protected function doQuery(){
		return $this->oMongoCursor->doQuery();
	}
	/**
	 * 解释该查询
	 * @return Array
	 */
	public function explain(){
		return $this->oMongoCursor->explain();
	}
	/**
	 * 设置需要返回或不需要返回的字段
	 * @param Array $f = array('mnick'=>0不返回1返回)
	 * @return muMongoCursor
	 */
	public function fields( $f){
		$this->oMongoCursor->fields( $f);
		return $this;
	}
	/**
	 * 获取下一个值,并且把游标移动
	 * @return Array
	 */
	public function getNext(){
		return $this->oMongoCursor->getNext();
	}
	/**
	 * 获取该查询的信息
	 * @return Array
	 */
	public function info(){
		return $this->oMongoCursor->info();
	}
	/**
	 * 检查是否还有下一项
	 * @return Boolean
	 */
	public function hasNext(){
		return $this->oMongoCursor->hasNext();
	}
	/**
	 * 对该查询给一些辅助索引的提示
	 * @param Array $key_pattern 索引名
	 * @return muMongoCursor 
	 */
	public function hint( $key_pattern ){
		$this->oMongoCursor->hint( $key_pattern);
		return $this;
	}
	/**
	 * 设置服务端当前游标是否永久存在.默认是超时则清除该游标
	 * @param Boolean $liveForever
	 * @return muMongoCursor
	 */
	public function immortal( $liveForever=true){
		$this->oMongoCursor->immortal( $liveForever);
		return $this;
	}
	/**
	 * 当前游标所指的_id值
	 * @return String
	 */
	public function key(){
		return $this->oMongoCursor->key();
	}
	/**
	 * 设置返回的行数
	 * @param int $num 0不限制
	 * @return muMongoCursor
	 */
	public function limit( $num ){
		$this->oMongoCursor->limit( $num);
		return $this;
	}
	/**
	 * 游标下一条记录
	 * @return null 
	 */
	public function next(){
		return $this->oMongoCursor->next();
	}
	/**
	 * 清除游标
	 * @return null
	 */
	public function reset(){
		return $this->oMongoCursor->reset();
	}
	/**
	 * 把游标指到第一条记录
	 * @return null
	 */
	public function rewind(){
		return $this->oMongoCursor->rewind();
	}
	/**
	 * 跳过查询结果的$num条记录
	 * @param int $num
	 * @return muMongoCursor
	 */
	public function skip( $num ){
		$this->oMongoCursor->skip( $num);
		return $this;
	}
	/**
	 * 设置此次查询是否可以在从db上执行
	 * @param Boolean $okay
	 * @return muMongoCursor
	 */
	public function slaveOkay( $okay=true){
		$this->oMongoCursor->slaveOkay( $okay);
		return $this;
	}
	/**
	 * 设置使用快照查询(缓存该查询)
	 * @return muMongoCursor
	 */
	public function snapshot(){
		$this->oMongoCursor->snapshot();
		return $this;
	}
	/**
	 * 对结果中的某些字段排序
	 * @param Array $fields = array('mid'=>-1降序1升序)
	 * @return muMongoCursor
	 */
	public function sort( $fields){
		$this->oMongoCursor->sort( $fields);
		return $this;
	}
	/**
	 * 设置光标在取完结果集后是否还让其存在
	 * @param Boolean $tail
	 * @return muMongoCursor
	 */
	public function tailable( $tail=true){
		$this->oMongoCursor->tailable( $tail);
		return $this;
	}
	/**
	 * 设置一个超时
	 * @param int $ms
	 * @return muMongoCursor
	 */
	public function timeout( $ms){
		$this->oMongoCursor->timeout( $ms);
		return $this;
	}
	/**
	 * 检查游标是否在读一个存在的资源
	 * @return Boolean
	 */
	public function valid(){
		return $this->oMongoCursor->valid();
	}
}