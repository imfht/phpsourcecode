<?php
/**
 * Created by PhpStorm.
 * User: peiyu
 * Date: 2018-1-9
 * Time: 19:03
 */

namespace DataComposer\Providers\file;

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
		
		$filetype='json';
		if( isset( $config['property']) && array_key_exists("fileType",$config['property'])){
			$filetype=$config['property']['fileType'];
		}
		$dbtype='\\DataComposer\\Providers\\file\\'.$filetype;

		$_connct=null;
		if( isset( $config['property']) && array_key_exists("fullFileName",$config['property'])){
			$_connct=$config['property']['fullFileName'];
		}
		$_db= new $dbtype();
		if(!$_db) {
			Log::error('no: '.$dbtype );
			return [];
		}
		$_data=[];
		$pkey = '';
		$ckey = '';
		if (isset($config['property']['relationKey']) && $parenData) {
			list($pkey,$ckey,$eq) = comm::getPkeyCkey($config['property']['relationKey']);

			$keydata = array_unique(array_column($parenData, $pkey));
			$getDataHandle=function()use($_db,$keydata,$handle,$name,$ckey,$maxLimit,$_connct){
				$_data=[];
				$_data = $_db->get($_connct);
				Log::info(json_encode($_data,JSON_UNESCAPED_UNICODE ));

				if($handle){
					$_h=call_user_func_array( $handle, [$name ,null,['data'=>$_data,'column'=>$ckey,'keydata'=>$keydata,'maxLimit'=>$maxLimit]]);
					if($_h && is_array($_h)){
						if(array_key_exists('data',$_h))$_data=$_h['data'];
					}
				}

				return $_data;
			};

			$_data=$this->cache([$default_fwtype,$_connct,$keydata],$getDataHandle );

		}

		return $_data;

	}

}