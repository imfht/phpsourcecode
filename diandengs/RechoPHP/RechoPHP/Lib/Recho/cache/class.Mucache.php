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
memcacheq只支持set,get,delete方法,单次设置不能超过64k(可以压缩)
memcachedb不能使用CAS协议
CAS协议不能同时使用分组备份机制
对于高精度数据,不要用分布式
对于开启压缩的key,大于100Byte的将会被压缩
$config['memcache'][] = array( //Memcache,支持多个分布式(地址,端口,权重)
		array('127.0.0.1', '11211', 50),
		array('127.0.0.1', '11212', 50),
	);
$config['memcache'][] = array( //Memcache,支持多个分布式(地址,端口,权重)
		array('127.0.0.1', '11213', 50),
		array('127.0.0.1', '11214', 50),
	);	
**/

class Mucache{
	private $arrOmem = array(); //分组式
	private $arrServers = array(); //分组
	private $try = 2; //重试次数
	private $connected = false; //是否已经连接过
	private $persistent = false; //是否长连接
	private $prefix = ''; //Key前缀

	public function __construct( $arrServers, $persistent=false){ //构造函数	
		$this->arrServers = $arrServers;
		$this->persistent = $persistent;
		if( ! class_exists( 'Memcached')){ //强制使用
			die('This Lib Requires The Memcached Extention!');
		}
	}
	
	/**
	 * 支持多组连接.
	 * @return void
	 */
	private function connect(){
		if ( ! $this->connected){
			$this->connected = true; //标志已经连接过一次
			foreach ((array)$this->arrServers as $key => $arrSever){//创建多组
				$this->arrOmem[$key] = $this->persistent ? new Memcached( $this->genPool( $arrSever)) : new Memcached(); //根据是否持久连接
				$this->arrOmem[$key]->setOption(Memcached::OPT_TCP_NODELAY, true); //启用tcp_nodelay
				$this->arrOmem[$key]->setOption(Memcached::OPT_NO_BLOCK, true); //启用异步IO
				$this->arrOmem[$key]->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT); //分布式策略
				$this->arrOmem[$key]->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true); //分布式服务组分散.推荐开启 
				$this->arrOmem[$key]->setOption(Memcached::OPT_HASH, Memcached::HASH_CRC);  //Key分布
				//$this->arrOmem[$key]->setOption(Memcached::OPT_SERVER_FAILURE_LIMIT, 5); //
				//$this->arrOmem[$key]->setOption(Memcached::OPT_PREFIX_KEY, $this->prefix); //Key前缀
				//Memcached::HAVE_IGBINARY&&extension_loaded('igbinary')&&$this->arrOmem[$key]->setOption(Memcached::OPT_SERIALIZER, Memcached::SERIALIZER_IGBINARY); //序列化
				$this->persistent && count($this->arrOmem[$key]->getServerList()) ? '' : $this->arrOmem[$key]->addServers( $arrSever); //对于持久连接并且已经有服务端口的,不执行.控制每个进程只有一个持久连接
			}
		}
	}
	
	/**
	 * 设置mem值 默认缓存24小时
	 * @var key 键名,键值,过期时间,是否压缩. 后两个参数对Memcachedb透明
	 * @return 成功为true,否则false
	 */
	public function set($key, $value, $expire=86400, $zip=true){
		$this->connect();
		$setOk = 999; //成功设置标志,必须强等
		
		foreach ( (array)$this->arrOmem as $oMem){
			$oMem->setOption(Memcached::OPT_COMPRESSION, $zip ? true : false);
			for($try=0; $try<$this->try; $try++){ //确保每个组都保存成功,重试$this->try次
				
				$oMem->set($key, $value, $expire);
				$resultCode = $oMem->getResultCode();
				if( $resultCode == Memcached::RES_SUCCESS ){ //保存成功则退出此层循环
					$setOk = $setOk===999 ? true : $setOk;
					break;
				}
				$setOk = false;
				$this->errorlog($key, $try, $oMem->getServerList(), $oMem->getResultMessage().$resultCode , 'set');
				if(in_array($resultCode, array(Memcached::RES_BAD_KEY_PROVIDED))){
					break;
				}
			}
		}

		return $setOk===999 ? false : $setOk; //返回是否设置成功.没有成功由程序逻辑处理
	}
	
	/**
	 * 添加mem值 默认缓存24小时
	 * @var key 键名,键值,过期时间,是否压缩. 后两个参数对Memcachedb透明
	 * @return 成功true,否则false
	 */
	public function add($key, $value, $expire=86400, $zip=true){
		$this->connect();

		$setOk = 999; //成功设置标志,必须强等
		
		foreach ( (array)$this->arrOmem as $oMem){
			$oMem->setOption(Memcached::OPT_COMPRESSION, $zip ? true : false);
			for($try=0; $try<$this->try; $try++){ //确保每个组都保存成功,重试$this->try次
				
				$oMem->add($key, $value, $expire);
				$resultCode = $oMem->getResultCode();
				if($resultCode == Memcached::RES_SUCCESS ){ //保存成功则退出此层循环
					$setOk = $setOk===999 ? true : $setOk;
					break;
				}
				$setOk = false;
				$this->errorlog($key, $try, $oMem->getServerList(), $oMem->getResultMessage().$resultCode, 'add');
				
				if(in_array($resultCode, array(Memcached::RES_NOTSTORED, Memcached::RES_BAD_KEY_PROVIDED))){ //值已经存在或者Key非法
					break;
				}
			}
		}
		
		return $setOk===999 ? false : $setOk;
	}
	
	/**
	 * 替换mem值 默认缓存24小时
	 * @var key 键名,键值,过期时间,是否压缩. 后两个参数对Memcachedb透明
	 * @return 成功true,否则false
	 */
	public function replace($key, $value, $expire=86400, $zip=true){
		$this->connect();

		$setOk = 999; //成功设置标志,必须强等
		
		foreach ( (array)$this->arrOmem as $oMem){
			$oMem->setOption(Memcached::OPT_COMPRESSION, $zip ? true : false);
			for($try=0; $try<$this->try; $try++){ //确保每个组都保存成功,重试$this->try次
				
				$oMem->replace($key, $value, $expire);
				$resultCode = $oMem->getResultCode();
				if(in_array($resultCode, array(Memcached::RES_SUCCESS, Memcached::RES_NOTSTORED)) ){ //保存成功或者值没有存在则退出此层循环
					$setOk = $setOk===999 ? true : $setOk;
					break;
				}
				$setOk = false;
				$this->errorlog($key, $try, $oMem->getServerList(), $oMem->getResultMessage().$resultCode, 'replace');
			}
		}

		return $setOk===999 ? false : $setOk;
	}
	
	/**
	 * 设置多个mem值 默认缓存24小时
	 * @var key array(键名=>键值),过期时间,是否压缩. 后两个参数对Memcachedb透明
	 * @return 成功true,否则false
	 */
	public function setMulti($items, $expire=86400, $zip=true){
		$this->connect();

		$setOk = 999; //成功设置标志,必须强等
		
		foreach ( (array)$this->arrOmem as $oMem){
			$oMem->setOption(Memcached::OPT_COMPRESSION, $zip ? true : false);
			for($try=0; $try<$this->try; $try++){ //确保每个组都保存成功,重试$this->try次
				
				$oMem->setMulti($items, $expire);
				$resultCode = $oMem->getResultCode();
				if($resultCode == Memcached::RES_SUCCESS ){ //保存成功则退出此层循环
					$setOk = $setOk===999 ? true : $setOk;
					break;
				}
				$setOk = false;
				$this->errorlog($items, $try, $oMem->getServerList(), $oMem->getResultMessage().$resultCode, 'setMulti');
			}
		}
		
		return $setOk===999 ? false : $setOk;
	}
	
	/**
	 * 加验证地存入一个值 注意不支持分组!!!
	 * @return 成功true,否则false
	 */
	public function cas($cas, $key, $value, $expire=0){
		$this->connect();

		$setOk = 999; //成功设置标志,必须强等
		
		foreach ( (array)$this->arrOmem as $oMem){
			for($try=0; $try<$this->try; $try++){ //确保每个组都保存成功,重试$this->try次
				
				$oMem->cas($cas, $key, $value, $expire);
				$resultCode = $oMem->getResultCode();
				if( $resultCode == Memcached::RES_SUCCESS ){ //保存成功则退出此层循环
					$setOk = $setOk===999 ? true : $setOk;
					break;
				}
				$setOk = false;
				$this->errorlog($key, $try, $oMem->getServerList(), $oMem->getResultMessage().$resultCode,'cas');
				if( in_array($resultCode, array(Memcached::RES_PROTOCOL_ERROR, Memcached::RES_DATA_EXISTS))){ //如果不支持cas协议或者没有该键或者数据被更改
					break;
				}
			}
		}

		return $setOk===999 ? false : $setOk;
	}
	
	/**
	 * 追加字符串 @var $direct 0加到后面1加到前面 默认永久保存
	 * @return 成功true,否则false
	 */
	public function append($key, $value, $expire=0, $direct=0){
		$this->connect();
		
		$setOk = 999; //成功设置标志,必须强等
		
		foreach ( (array)$this->arrOmem as $oMem){
			$oMem->setOption(Memcached::OPT_COMPRESSION, false); //append不支持压缩
			for($try=0; $try<$this->try; $try++){ //确保每个组都保存成功,重试$this->try次
				$direct ? $oMem->prepend($key, $value) : $oMem->append($key, $value);
				if( $oMem->getResultCode() == Memcached::RES_NOTSTORED){
					$oMem->add($key, $value, $expire);
				}
				$resultCode = $oMem->getResultCode();
				if( $resultCode == Memcached::RES_SUCCESS ){ //保存成功则退出此层循环
					$setOk = $setOk===999 ? true : $setOk;
					break;
				}
				$setOk = false;
				$this->errorlog($key, $try, $oMem->getServerList(), $oMem->getResultMessage().$resultCode,'append');
			}
		}
		return $setOk===999 ? false : $setOk;
	}
	
	/**
	 * 累加/减 默认是加 永久保存
	 * @var $value 需要累加或减的值 注意不能减成负数,不能是负数
	 * @var $direct 0加1减
	 * @return 成功true,否则false
	 */
	public function increment($key, $value, $expire=0, $direct=0){
		$this->connect();
		
		$setOk = 999; //成功设置标志,必须强等
		
		foreach ( (array)$this->arrOmem as $oMem){
			$oMem->setOption(Memcached::OPT_COMPRESSION, false); //对数字不压缩
			for($try=0; $try<$this->try; $try++){ //确保每个组都保存成功,重试$this->try次
				$direct ? $oMem->decrement($key, $value) : $oMem->increment($key, $value);
				if( $oMem->getResultCode() == Memcached::RES_NOTFOUND){
					$oMem->add($key, $value, $expire);
				}
				$resultCode = $oMem->getResultCode();
				if($resultCode == Memcached::RES_SUCCESS ){ //保存成功则退出此层循环
					$setOk = $setOk===999 ? true : $setOk;
					break;
				}
				$setOk = false;
				$this->errorlog($key, $try, $oMem->getServerList(), $oMem->getResultMessage().$resultCode,'increment');
			}
		}
		return $setOk===999 ? false : $setOk;
	}
	
	/**
	 * 获取单键 $zip 是否解压缩.对应set的压缩 
	 * @var $inCas 是否进行cas验证 如果是则返回一个数组[结果集,cas字串] 注意不支持分组!!!
	 * @return 成功返回结果,否则false
	 */
	public function get( $key, $zip=true, $inCas=false){
		$this->connect();
		
		$result = $success = false; //返回的结果
		$aNocached = array(); //缓存没有命中的列表
		
		shuffle($this->arrOmem);//打乱数组,随机获取
		
		foreach ( (array)$this->arrOmem as $oMem){//取到一个就返回
			$oMem->setOption(Memcached::OPT_COMPRESSION, $zip ? true : false); //对于set是压缩的则解压缩取出
			for($try=0; $try<$this->try; $try++){ //确保在没有系统错误的情况下执行
				
				$result = $inCas ? @$oMem->get( $key, null, $cas) : @$oMem->get( $key);  //抑制错误,如不能正常解压

				$resultCode = $oMem->getResultCode();
				if( $resultCode == Memcached::RES_SUCCESS){ //获取成功,给没有数据的组赋值,并退出循环
					$success = true;
					
					foreach ( (array)$aNocached as $oTempMem ){
						$oTempMem->set($key, $result, 24*3600);
					}
					break;
				}else{ //强制返回false
					$result = false;
				}
				if( $resultCode == Memcached::RES_NOTFOUND){ //获取要么成功,要么没有找到该KEY,否则出错
					$aNocached[] = $oMem;
					break;
				}
				$this->errorlog($key, $try, $oMem->getServerList(), $oMem->getResultMessage().':'.(string)$resultCode,'get');
				if( in_array($resultCode, array(Memcached::RES_PAYLOAD_FAILURE, Memcached::RES_BAD_KEY_PROVIDED, '4294966295'))){ //不能正常解压或者key不正确,删除
					$oMem->delete( $key, 0);
					break;
				}
				if( $resultCode == Memcached::RES_PROTOCOL_ERROR){ //如果不支持cas协议
					break;
				}
			}
			if( $success == true){
				break;
			}
		}
		return $inCas ? array($result, $cas) : $result;
	}
	
	/**
	 * 获取多键.有缺陷,keys数组中的值必须都为字符串型.先要测试该方法是否可用,否则会引起致命错误
	 * @return 成功返回结果,否则false
	 */
	public function getMulti( $keys, $zip=true, $inCas=false){
		foreach ((array)$keys as $i => $key){
			$keys[$i] = (string)$key;
		}

		$this->connect();
		
		$num = array_rand((array)$this->arrOmem, 1);//随机到一组中获取
		$this->arrOmem[$num]->setOption(Memcached::OPT_COMPRESSION, $zip ? true : false); //对于set是压缩的则解压缩取出
		
		$result = $inCas ? @$this->arrOmem[$num]->getMulti($keys, $cas, Memcached::GET_PRESERVE_ORDER) : @$this->arrOmem[$num]->getMulti( $keys);  //抑制错误,如不能正常解压
		$resultCode = $this->arrOmem[$num]->getResultCode();
		if( $resultCode != Memcached::RES_SUCCESS){ //没有成功,强制返回false,写日志
			$result = false;
			$this->errorlog($keys, 0, $this->arrOmem[$num]->getServerList(), $this->arrOmem[$num]->getResultMessage().':'.(string)$resultCode,'getMulti');
		}
		return $inCas ? array($result, $cas) : $result;
	}
	
	/**
	 * $expire秒后删除
	 * @return 成功为true,否则false
	 */
	public function delete($key, $expire=0){
		$this->connect();
		
		$delOk = 999; //返回标志
		
		foreach ( (array)$this->arrOmem as $oMem){
			for($try=0; $try<$this->try; $try++){ //确保每个组都保存成功,重试N次
				$oMem->delete($key, (int)$expire);

				if( in_array($oMem->getResultCode(), array(Memcached::RES_SUCCESS, Memcached::RES_NOTFOUND)) ){ //删除成功或者已经没有那个KEY
					$delOk = $delOk===999 ? true : $delOk;
					break;
				}
				$delOk = false;
			}
		}
		return $delOk===999 ? false : $delOk;;
	}
	
	/**
	 * 把所有缓存设置为超时,以便其他key可以用
	 */
	public function flush( $delay=0){
		$this->connect();
		
		$flushOk = 999; //返回标志
		
		foreach ( (array)$this->arrOmem as $oMem){
			$oMem->flush( $delay );
			
			if( in_array( $oMem->getResultCode(), array( Memcached::RES_SUCCESS))){
				$flushOk = $flushOk===999 ? true : $flushOk;
			}
			$flushOk = false;
		}
		return $flushOk===999 ? false : true;
	}
	
	/**
	 * 获取运行状态.有缺陷,如果分布式中某点有故障,则获取不到记录
	 * @return unknown
	 */
	public function getStats(){
		$this->connect();
		
		foreach ( (array)$this->arrOmem as $oMem){
			$arrStatus[] = $oMem->getStats();
		}
		return $arrStatus;
	}
	
	/**
	 * 获取长连接名前缀,保证相同的端口配置用同一个长连接
	 */
	private function genPool( $arrSever){
		foreach ((array)$arrSever as $aServer){
			$aList[] = $aServer[0] . '-' . $aServer[1];
		}
		$aList = array_unique( (array)$aList);
		sort( $aList, SORT_STRING);
		return md5(implode('-', $aList));
	}
	
	/**
	 * 错误日志
	 * @param unknown_type $key
	 * @param unknown_type $try
	 * @param unknown_type $group
	 * @param unknown_type $msg
	 */
	private function errorlog($keys, $try, $group, $msg , $method){
		$error = date('Y-m-d H:i:s').":\nmethod:".$method.":\n".var_export( $group, true) . ";\nkeys:".var_export($keys, true).";\ntry:{$try};\nmsg:{$msg}\n\n";
		$file = RECHO_PHP . 'Runtime/Log/memcache.txt';
		@file_put_contents($file, "{$error}\n", @filesize($file)<512*1024 ? FILE_APPEND : null);
	}
}