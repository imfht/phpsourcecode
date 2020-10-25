<?php
/**
 * Created by PhpStorm.
 * User: peiyu
 * Date: 2018-1-20
 * Time: 10:33
 */

namespace DataComposer\Providers;

use DataComposer\comm;
use Log;

abstract class handle
{
	public $name=null;
	public $config=null;
	public $maxLimit=null;
	public $fwtype=null;
	public $parenData=null;
	public $callback=null;
	public $cacheEnable =false;
	public $cacheExpire=10;
	public $dbtype='db';

	abstract function todo();

	protected function  setCache () {
		if (isset($config['property']['cacheEnable'])) {
			$this->cacheEnable = $config['property']['cacheEnable'];
		}
		if (isset($config['property']['cacheExpire'])) {
			$this->cacheExpire = $config['property']['cacheExpire'];
		}
	}
	
	protected function cache(array $cachkeyList,$getDataHandle){

		$cachkey='';
		if($this->cacheEnable){
			//$cachkeyList=[$default_fwtype,$_connct,$keydata];
			$cachkeystr=json_encode($cachkeyList,JSON_UNESCAPED_UNICODE);
			$cachkey=hash('sha256',$cachkeystr);
			Log::info($this->dbtype.'_cach_key:'.$cachkey.':'.$cachkeystr  );
			$_data=comm::cacheGet($this->fwtype,$cachkey);
			if($_data){
				Log::info($this->dbtype.'_cach_ok');
				return $_data;
			}
			else{
				Log::info($this->dbtype.'_cach_no');
			}
		}
		$_data=$getDataHandle();
		if($cachkey){
			comm::cacheSet($this->fwtype,$cachkey,$_data);
		}
		return $_data;
	}
}