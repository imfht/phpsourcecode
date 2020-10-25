<?php
/**
 * Created by PhpStorm.
 * User: peiyu
 * Date: 2018-1-4
 * Time: 13:09
 */

namespace DataComposer;

use Log;

/**
 * Created by PhpStorm.
 * User: peiyu
 * Date: 2017/12/30
 * Time: 16:54
 */
class Engine
{

	protected $confProv;// 配置适配器
	protected $config;// 配置信息
	protected $conf;// 数据集配置
	protected $name;// 数据集名称
	protected $maxLimit = 1000; // 最大数据量
	protected $parameterValue=[];
	protected $callback=[];
	protected $cacheEnable =false;
	protected $cacheExpire=10;
	protected $nameList=[];//数据集名称白名单
	
	protected $default_fwtype = 'laveral';
	protected $default_connecttype = 'db';

	protected $connectTypeList=['db','mongo','redis','api','file','excel'];

	public function __construct($name, $frameworktype=null, array $config = null, array $dataComposerConfig = null)
	{
		$this->name = $name;

		$fwtype =$frameworktype?$frameworktype: config('app.frameworkType');
		if ($fwtype) {
			$this->default_fwtype = $fwtype;
		}
		if(!$config || !$dataComposerConfig){
			$conftype = 'DataComposer\\Providers\\CONFIG\\' . $this->default_fwtype;
			$confProv = new $conftype();
		}


		if ($dataComposerConfig) {
			$this->conf = $dataComposerConfig;
		} else {
			$this->conf = $confProv->getConfig($name);
		}
		if ($config) {
			$this->config = $config;
		} else {
			$this->config = $confProv->getConfig('conf');
		}

		$connecttype = '';

		if ($this->config && array_key_exists('connectType', $this->config)) {
			$connecttype = $this->config['connectType'];
			if ($connecttype) {
				$this->default_connecttype = $connecttype;
			}
		}
		if(!$this->conf  ){
			throw new \Exception($name." dataComposerConfig error");
		}
		if ($this->config && array_key_exists('maxLimit', $this->config)) {
			$_maxLimit = $this->config['maxLimit'];
			if ($_maxLimit) {
				$this->maxLimit = $_maxLimit;
			}
		}
		if ($this->config && array_key_exists('cacheEnable', $this->config)) {
			$this->cacheEnable  = $this->config['cacheEnable'];
		}
		if ($this->config && array_key_exists('cacheExpire', $this->config)) {
			$this->cacheExpire  = $this->config['cacheExpire'];
		}

	}

	//设置变量值
	public function SetParameterValue($name,array $parameterValue){
		if(!$name || !$parameterValue)return false;
		$this->parameterValue[$name]=$parameterValue;
		return true;
	}
	public function SetCallback($name,$callback,$extend=null){
		if(!$name || !$callback)return false;
		$this->callback[$name][]=[$callback,$extend];
		return true;
	}
	
	

	public function GetData(array $nameList=[])
	{
		$this->nameList=$nameList;
		return $this->one($this->name, $this->conf, null);

	}

	//处理一个数据源，形成嵌套
	protected function one($name, $config, $parenData = null)
	{
		if($this->nameList &&  $name!=$this->name && !in_array($name,$this->nameList) ){
			return $ret[$name]=[];
		}
		$pkey = '';
		$ckey = '';
		$_conncttype = $this->default_connecttype;
		if (isset($config['property'])){
			$v= $this->_SetParameterValue($name,$config['property']);
			Log::info(json_encode([$name,$v],JSON_UNESCAPED_UNICODE));
			if($v)$config['property']=$v;
		}
		$handle=null;
		if(array_key_exists($name , $this->callback)){
			$handle=$this->callback[$name];
		}

		if (isset($config['property']) && array_key_exists("connectType", $config['property'])) {
			$_conncttype = $config['property']['connectType'];
		}
		///获取 一个数据源 数据
		$_function=null;
		if( in_array(  $_conncttype,$this->connectTypeList) ){
			$dbtype = '\\DataComposer\\Providers\\' . $_conncttype . '\\handle';
		}else{
			///自定义数据读取器
			$dbtype=$_conncttype;
			if(isset($config['property']['function']))$_function=$config['property']['function'];
		}

		
		$conn = new $dbtype();
		$conn->name=$name;
		$conn->config=$config;
		$conn->maxLimit=$this->maxLimit;
		$conn->fwtype=$this->default_fwtype;
		$conn->parenData=$parenData;
		$conn->handle=$handle;
		$conn->cacheEnable=$this->cacheEnable;
		$conn->cacheExpire=$this->cacheExpire;
		$conn->dbtype=$_conncttype;
		

		if($_function){///自定义数据读取器 function
			$_data=call_user_func( [$conn,$_function] );
		}else{
			$_data = $conn->todo();
		}
		
		
		$pkey = null;
		$ckey = null;
		$eq=null;
		if(isset($config['property']['relationKey']) && $_data ){
			list($pkey,$ckey,$eq) = comm::getPkeyCkey($config['property']['relationKey']);
		}
		

		//如果有子节点 ，获取子节点数据
		if (array_key_exists("dataSource", $config)) {
			$_child = [];
			$dslist = $config['dataSource'];
			//获取所有子节点数据，并且合并
			foreach ($dslist as $ds_name => $ds) {
				$_child = array_merge($_child, $this->one($ds_name, $ds, $_data, $handle));
			}

			$_tmp = [];
			//将子节点数据分发到与之关联的父节点中
			foreach ($_data as $item) {
				foreach ($dslist as $ds_name => $ds) {
					$item[$ds_name]=[];
				}
				foreach ($_child as $k => $v) {
					if (array_key_exists($item[$v['pkey']], $v['data'])) $item[$k] = $v['data'][$item[$v['pkey']]];
				}
				$_tmp[] = $item;
			}

			if ($_tmp) $_data = $_tmp;
		}
		$ret = [];

		if ($ckey) {
			$IsUnique=false;
			if(isset($config['property']['relationType']) && $config['property']['relationType']=="one_to_one" ){
				$IsUnique=true;
			}

			$ret[$name]['data'] = $this->arrayAddKey($_data, $ckey,$IsUnique);
			$ret[$name]['pkey'] = $pkey;
			if ($ckey == 'val') var_dump($ret);
		}

		/// 对于根节点和其他节点的结构处理
		return $ret[$name] = $ret ? $ret : $_data;
	}

	private function arrayAddKey(array $arr, $keyName, $keyIsUnique = false)
	{
		$_list = [];
		foreach ($arr as $item) {
			if(!is_array($item) || !array_key_exists($keyName,$item) )continue;
			if ($keyIsUnique)
				$_list[$item[$keyName]] = $item;
			else    $_list[$item[$keyName]][] = $item;
		}
		return $_list;
	}

	private function _SetParameterValue($name,array $arr){
	
		if(!$arr || !array_key_exists($name,$this->parameterValue))return false;
		foreach($this->parameterValue[$name] as $k=>$v){
			$arr=$this->_SetParameterValue_($arr,$k,$v);
		}
		return $arr;
	}
	private function _SetParameterValue_(array $arr,$k,$v){
		$_k='{$'.$k.'}';
		foreach($arr as $key => $value) {
			if(!isset($value)) continue;

			if(is_string($value) && stripos($value,$_k)!== false ){
				$arr[$key]= str_replace($_k,$v,$value);
			}
			if(is_array($value)){
				$arr[$key]= $this->_SetParameterValue_($value,$k,$v );
			}
		}
		return $arr;
	}

}

