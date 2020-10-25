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
 * 需tokyo_tyrant扩展支持 仅支持HASH型数据库
 * include(RC_PATH_LIB . 'class.Tyrant.php');
 * $config['TokyoTyrant'] = array('127.0.0.1', '11611'); //单服务器 array(地址, 端口)
 * $oTyrant = new Tyrant( $config['TokyoTyrant']);
 * $oTyrant->put('mnick', 'HanMeiMei', Tyrant::WRITEADD);
 * echo $oTyrant->get('mnick');
 */
class Tyrant{
	private $oTyrant = null; //连接对象
	private $arrServer = array(); //地址配置
	private $connected = false; //是否已经连接过
	private $persistent = false; //是否长连接: 每个CGI进程会建立一个不断的链接,TT客户端会先判断有没有链接,有则不再建立连接
	private $prefix = ''; //键前缀
	/**
	 * 覆盖式写入
	 */
	const WRITEOVER = 0;
	/**
	 * 添加式写入
	 */
	const WRITEADD = 1;
	/**
	 * 异步式写入
	 */
	const WRITENR = 2;
	
	public function __construct( $arrServer, $persistent=false){ //构造函数	
		$this->arrServer = $arrServer;
		$this->persistent = $persistent;
		if( ! class_exists( 'TokyoTyrant')){ //强制使用
			//die('This Lib Requires The TokyoTyrant Extention!');
		}
		//ini_set('tokyo_tyrant.key_prefix', $this->prefix);
	}
	
	private function connect(){
		if ( ! $this->connected){
			$this->connected = true; //标志已经连接过一次
			try {
				$this->oTyrant = new TokyoTyrant($this->arrServer[0], $this->arrServer[1], array('timeout'=>5, ' reconnect'=>true, 'persistent'=>$this->persistent?true:false));
			}catch (TokyoTyrantException $e){ //连接失败,记录
				$this->errorlog('Connect', $e->getCode(), $e->getMessage());
			}
		}
		return is_object( $this->oTyrant);
	}
	
	/**
	 * 给一个KEY做加减操作 相当于Memcached的increment和decrement
	 * KEY不存在则为0 $value 可以为负数 $isFloat 是否是浮点数 连接失败返回 false
	 * @return 最新的值.由这个方法存入的key必须由此方法通过加减0来获取,而不能用get方法
	 */
	public function add($key, $value, $isFloat=false){
		if( ! $this->connect()){
			return false;
		}
		
		return $this->oTyrant->add( $key, $value, $isFloat ? TokyoTyrant::RDBREC_DBL : TokyoTyrant::RDBREC_INT);
	}
	
	/**
	 * 设置值.相当于Memcached的set和setMulti
	 * 设置多个则为一个参数 array($key0=>$value0, $key1=>$value1) $op为写入方式,参考SELF::WRITE* 失败返回false否则true
	 */
	public function put($keys, $value=null, $op=0, $zip=false){
		return ocache::cache()->set( $keys, $value);
		if( ! $this->connect()){
			return false;
		}
		if( is_array( $keys)){
			foreach ($keys as $key => $value){
				$keys[$key] = $zip ? gzcompress(serialize( $value)) : serialize( $value);
			}
		}else{
			$value = $zip ? gzcompress(serialize( $value)) : serialize( $value);
		}
		try {
			$flag = true;
			switch ( $op){
				case Tyrant::WRITENR:
					$this->oTyrant->putNr($keys, $value);  //异步写,不等响应就返回
				break;
				case Tyrant::WRITEADD:
					$this->oTyrant->putKeep($keys, $value); //添加式写
				break;
				case Tyrant::WRITEOVER:
				default:
					$this->oTyrant->put($keys, $value);
				break;
			}
		}catch (TokyoTyrantException $e){ //设置失败,记录
			$flag = false;
			$this->errorlog($keys, $e->getCode(), $e->getMessage());
		}
		return $flag;
	}
	
	/**
	 * 做字符串连接操作.如果key不存在,则自动添加
	 */
	public function putCat($keys, $value=null){
		if( ! $this->connect()){
			return false;
		}
		try {
			$flag = true;
			$this->oTyrant->putCat($keys, $value);
		}catch (TokyoTyrantException $e){ //设置失败,记录
			$flag = false;
			$this->errorlog($keys, $e->getCode(), $e->getMessage());
		}
		return $flag;
	}
	/**
	 * 做字符串连接操作.并且仅保存-$width到最末的字符串.如果key不存在,则自动添加
	 */
	public function putShl( $key, $value, $width){
		if( ! $this->connect()){
			return false;
		}
		try {
			$flag = true;
			$this->oTyrant->putShl( $key, $value, $width);
		}catch (TokyoTyrantException $e){ //设置失败,记录
			$flag = false;
			$this->errorlog($keys, $e->getCode(), $e->getMessage());
		}
		return $flag;
	}
	
	/**
	 * 返回一个或一组值. 相当于Memcached的get和getMulti
	 * 如果$keys为数组,则返回键值对,如果为字符串,则返回单值 *此处有缺陷,如果keys是add进去的,则取出会错误.$zip: 是否解压缩.$serial:是否序列化,用于取出putCat存入的数据
	 */
	public function get( $keys, $zip=false, $serial=true){
		return ocache::cache()->get( $keys);
		if( ! $this->connect()){
			return false;
		}
		try {
			$flag = true;
			$result = $this->oTyrant->get( $keys);
		}catch (TokyoTyrantException $e) {
		    $flag = false;
			$this->errorlog($keys, $e->getCode(), $e->getMessage());
		}
		if($flag && is_array( $keys)){ //如果获取成功并且是一个数组key
			foreach ((array)$result as $k => $v){
				$result[$k] = $zip ? unserialize( @gzuncompress( $v)) : ($serial ? unserialize( $v) : $v);
			}
		}else if( $flag){ //如果获取成功并且是单记录
			$result = $zip ? unserialize( @gzuncompress( $result)) : ($serial ? unserialize( $result) : $result);
		}else{ //获取失败
			$result = false;
		}
		return $result;
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
			$result = $this->oTyrant->getIterator();
		}catch (TokyoTyrantException $e) {
		    $flag = false;
			$this->errorlog('getIterator', $e->getCode(), $e->getMessage());
		}
		
		return $flag ? $result : array();
	}
	
	/**
	 * 删除$keys对应的记录(不管有没有存在)相当于Memcached的delete但这里可以一次删多记录 成功为true
	 * $keys为字符串或数组
	 */
	public function out( $keys){
		return ocache::cache()->delete( $keys);
		if( ! $this->connect()){
			return false;
		}
		try {
			$flag = true;
			$this->oTyrant->out( $keys);
		}catch (TokyoTyrantException $e) {
		    $flag = false;
			$this->errorlog($keys, $e->getCode(), $e->getMessage());
		}
		return $flag;
	}
	
	/**
	 * 获取单个KEY的内容大小,以Byte为单位. 出错返回false
	 */
	public function size( $key){
		if( ! $this->connect()){
			return false;
		}
		try {
			$result = $this->oTyrant->size( $key);
		}catch (TokyoTyrantException $e) {
		    $result = false;
			$this->errorlog($keys, $e->getCode(), $e->getMessage());
		}
		return $result;
	}
	
	/**
	 * 返回$prefix开头的key,最多$max个 $prefix为''则返回所有Key.$max为-1返回所有匹配
	 */
	public function fwmKeys($prefix, $max){
		if( ! $this->connect()){
			return false;
		}
		
		return $this->oTyrant->fwmKeys($prefix, (int)$max);
	}
	
	/**
	 * 把数据全部写回硬盘.成功返回true
	 */
	public function sync(){
		if( ! $this->connect()){
			return false;
		}
		try {
			$flag = true;
			$this->oTyrant->sync();
		}catch (TokyoTyrantException $e){
			$flag = false;
			$this->errorlog('Sync', $e->getCode(), $e->getMessage());
		}
		return $flag;
	}
	
	/**
	 * 清空数据库
	 */
	public function vanish(){
		if((! defined('PRODUCTION_SERVER')) || PRODUCTION_SERVER || ! $this->connect()){  //产品线上不允许此操作
			return false;
		}
		try {
			$flag = true;
			$this->oTyrant->vanish();
		}catch (TokyoTyrantException $e){
			$flag = false;
			$this->errorlog('Vanish', $e->getCode(), $e->getMessage());
		}
		return $flag;
	}
	
	/**
	 * 返回当前数据库的记录数
	 */
	public function num(){
		if( ! $this->connect()){
			return false;
		}
		return $this->oTyrant->num();
	}
	
	/**
	 * 获取服务器的统计信息
	 */
	public function stat(){
		if( ! $this->connect()){
			return false;
		}
		
		return $this->oTyrant->stat();
	}
	
	/**
	 * 复制一份数据库 $path 数据库存储位置全路径(包括文件名)
	 */
	public function copy( $path){
		if( ! $this->connect()){
			return false;
		}
		try {
			$flag = true;
			$this->oTyrant->copy( $path);
		}catch (TokyoTyrantException $e){
			$flag = false;
			$this->errorlog('Copy', $e->getCode(), $e->getMessage());
		}
		return $flag;
	}
	
	private function errorlog($keys, $code, $msg){
		$error = date('H:i:s').":\n".$code.";\nkeys:".var_export($keys, true).";\nmsg:{$msg}\n";
		$file = RECHO_PHP . 'Runtime/Log/tyrant.txt';
		@file_put_contents($file, $error, @filesize($file)<512*1024 ? FILE_APPEND : null);
	}
}