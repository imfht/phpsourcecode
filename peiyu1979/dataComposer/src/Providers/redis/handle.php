<?php
/**
 * Created by PhpStorm.
 * User: peiyu
 * Date: 2018-1-9
 * Time: 19:03
 */

namespace DataComposer\Providers\redis;

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

		$dbtype='\\DataComposer\\Providers\\redis\\'.$default_fwtype;

		$_connct=null;
		if( isset( $config['property']) && array_key_exists("connectName",$config['property'])){
			$_connct=$config['property']['connectName'];
		}
		$_db= new $dbtype($_connct);
		if(!$_db) {
			Log::error('no: '.$dbtype );
			return [];
		}

		if($handle){
			$handle=$handle[0];
			$_h= call_user_func_array( $handle, [$name,$_db->getDB(),[]]);
			if($_h && is_array($_h)){
				if(array_key_exists('db',$_h))$_db->setDB($_h['db']);
			}
			
		}
		$_data=[];
		$pkey = '';
		$ckey = '';
		if (isset($config['property']['relationKey']) && $parenData) {
			list($pkey,$ckey,$eq) = comm::getPkeyCkey($config['property']['relationKey']);

			$keydata = array_unique(array_column($parenData, $pkey));
			$getDataHandle=function()use($_db,$keydata,$ckey){
				$_data=[];
				$_vals = $_db->get( $keydata);
				Log::info(json_encode($_vals,JSON_UNESCAPED_UNICODE ));
				foreach ($keydata as $k=>$v){
					if($_vals[$k]===false)continue;
					$_data[]=[$ckey=>$v,'val'=>$_vals[$k]  ];
				}
				return $_data;
			};

			$_data=$this->cache([$default_fwtype,$_connct,$keydata],$getDataHandle );

		}

		return $_data;

	}

}