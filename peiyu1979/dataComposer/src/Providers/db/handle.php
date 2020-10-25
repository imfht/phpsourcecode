<?php
/**
 * Created by PhpStorm.
 * User: peiyu
 * Date: 2018-1-9
 * Time: 19:03
 */

namespace DataComposer\Providers\db;

use DataComposer\comm;
use DataComposer\Providers\handle as hand;
use Log;

class handle extends hand
{

	public function todo(){
		parent::setCache();
		$name=$this->name;
		$config=$this->config;
		$maxLimit=$this->maxLimit;
		$default_fwtype=$this->fwtype;
		$parenData=$this->parenData;
		$handle=$this->callback;

		if(!isset($config['property']['tableName']))throw new \Exception($name. " tableName error");
		$tableName = $config['property']['tableName'];
		$_maxLimit = $maxLimit;
		$_where = [];
		$_whereIn = [];
		$_orderBy = [];
		$_fields = [];
		if (isset($config['property']['maxLimit'])) {
			$_maxLimit = $config['property']['maxLimit'];
		}
		if (isset($config['property']['where'])) {
			$_where = $config['property']['where'];
		}
		if (isset($config['property']['whereIn'])) {
			$_whereIn = $config['property']['whereIn'];
		}
		if (isset($config['property']['orderBy'])) {
			$_orderBy = $config['property']['orderBy'];
		}
		if (isset($config['property']['fields'])) {
			$_fields = $config['property']['fields'];
		}

		$dbtype='\\DataComposer\\Providers\\db\\'.$default_fwtype;

		$_connct=null;
		if(array_key_exists("connectName",$config['property'])){
			$_connct=$config['property']['connectName'];
		}
		$_db= new $dbtype($tableName,$_connct);
		if(!$_db) {
			Log::error('no: '.$dbtype );
			return [];
		}
		if(!$handle){
			if( isset( $config['property']) && array_key_exists("callback",$config['property'])){

					$_handle=$config['property']['callback'];
					if(count($_handle)==2){
						$_c= new $_handle[0]();
						$handle=[$_c,$_handle[1]];
					}
			}
		}
		$_addcachkey='';
		if($handle){
			$handle=$handle[0];
			$_h= call_user_func_array( $handle,[ $name,$_db->getDB(),['maxLimit'=>$_maxLimit,'where'=>$_where,'whereIn'=>$_whereIn,'orderBy'=>$_orderBy,'fields'=> $_fields]]);
			if($_h && is_array($_h)){
				if(array_key_exists('db',$_h))$_db->setDB($_h['db']);
				if(array_key_exists('maxLimit',$_h))$_maxLimit=$_h['maxLimit'];
				if(array_key_exists('where',$_h))$_where=$_h['where'];
				if(array_key_exists('whereIn',$_h))$_whereIn=$_h['whereIn'];
				if(array_key_exists('orderBy',$_h))$_orderBy=$_h['orderBy'];
				if(array_key_exists('fields',$_h))$_fields=$_h['fields'];
				if(array_key_exists('addcachkey',$_h))$_addcachkey=$_h['addcachkey'];
			}

		}

		$pkey = '';
		$ckey = '';
		if (isset($config['property']['relationKey']) && $parenData) {
			list($pkey,$ckey,$eq) = comm::getPkeyCkey($config['property']['relationKey']);
			if ($ckey && $_fields && !in_array($ckey, $_fields)) {
				$_fields[] = $ckey;
			}
			
			$keydata = array_unique(array_column($parenData, $pkey));
			$_db = $_db->whereIn($ckey, $keydata);
		}

		if (array_key_exists("dataSource", $config)) {
			//$_child = [];
			$dslist = $config['dataSource'];
			foreach ($dslist as $ds_name => $ds) {
				if (isset($ds['property']['relationKey'])) {
					$_key=$ds['property']['relationKey'];
					list($__pkey,$__ckey,$__eq) = comm::getPkeyCkey($_key);
					if ($__pkey && $_fields && !in_array($__pkey, $_fields)) {
						$_fields[] = $__pkey;
					}
				}
			}
		}
		$_db = $_db->limit($_maxLimit);
		if ($_where) $_db = $_db->where($_where);
		if ($_whereIn){
			foreach ($_whereIn as $k=>$v){
				$_db = $_db->whereIn($k,$v);
			}
		} 
		foreach ($_orderBy as $item) {
			$c = count($item);
			if ($c == 1) $_db = $_db->orderBy($item[0]);
			elseif ($c == 2) $_db = $_db->orderBy($item[0], $item[1]);
		}
		if ($_fields) $_db = $_db->select($_fields);

		$getDataHandle=function()use($_db){
			$_data = $_db->get();
			Log::info(json_encode($_data,JSON_UNESCAPED_UNICODE ));
			return $_data;
		};
		$cachkeyList=[$default_fwtype,$_connct,$tableName,$_where,$_maxLimit,$_orderBy,$_fields,$_addcachkey];
		$_data=$this->cache($cachkeyList,$getDataHandle );

		return $_data;

	}

}