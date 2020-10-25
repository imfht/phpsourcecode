<?php
/**
 * Created by PhpStorm.
 * User: peiyu
 * Date: 2018-1-9
 * Time: 19:03
 */

namespace DataComposer\Providers\api;
use DataComposer\comm;

use Log;
use DataComposer\Providers\handle as hand;

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

		$handle_begin=null;
		$handle_end=null;;
		if($handle){
			foreach ($handle as $item ){
				if($item[1]=='begin'){
					$handle_begin=$item[0];
					continue;
				}
				if($item[1]=='end'){
					$handle_end=$item[0];
					continue;
				}
			}
		}
		else{
			if( isset( $config['property']) && array_key_exists("callback",$config['property'])){
				if(isset($config['property']['callback']['begin'])) {
					$_handle_begin=$config['property']['callback']['begin'];
					if(count($_handle_begin)==2){
						$_c= new $_handle_begin[0]();
						$handle_begin=[$_c,$_handle_begin[1]];
					}

				}
				if(isset($config['property']['callback']['end'])) {
					$_handle_end=$config['property']['callback']['end'];
					if(count($_handle_end)==2){
						$_c= new $_handle_end[0]();
						$handle_end=[$_c,$_handle_end[1]];
					}
				}
			}
		}

		$_connct=null;
		$method='GET';
		$options=['query'=>['maxLimit'=>$maxLimit,'_'=> rand(10000000,99999999) ]];
		if( isset( $config['property']) && array_key_exists("url",$config['property'])){
			$_connct=$config['property']['url'];
		}
		if( isset( $config['property']) && array_key_exists("method",$config['property'])){
			$method=$config['property']['method'];
		}
		if( isset( $config['property']) && array_key_exists("options",$config['property'])){
			$options= array_merge($options,$config['property']['options']) ;
		}
		$httpClint='\\DataComposer\\Providers\\api\\guzzle';

		if( isset( $config['property']) && array_key_exists("httpClient",$config['property'])){
			$httpClint=$config['property']['httpClient'];
		}
		$client=new $httpClint();
		if(!$client) {
			Log::error('no: '.$httpClint );
			return [];
		}

		if(!$_connct)throw new \Exception($name. " connectstring error");
		$url=$_connct;

		$_data=[];
		$pkey = '';
		$ckey = '';
		$eq='';
		$querydata=[];
		if (isset($config['property']['relationKey']) && $parenData) {

			list($pkey,$ckey,$eq) = comm::getPkeyCkey($config['property']['relationKey']);
			$querydata = array_unique(array_column($parenData, $pkey));

		}
		if($querydata){
			switch (strtolower($method)){
				case 'get':
					$options['query']=[$ckey=>implode(',',$querydata) ];
					break;
				default:
					$options['form_params']=[$ckey=>implode(',',$querydata) ];
					break;
			}
		}

		$_addcachkey='';
		if($handle_begin){
			$_h=call_user_func_array( $handle_begin, [$name,$client,['url'=>$_connct,'method'=>$method,'options'=>$options ,'extend'=>'begin']]);
			if($_h && is_array($_h)){
				if(array_key_exists('client',$_h))$client=$_h['client'];
				if(array_key_exists('url',$_h))$_connct=$_h['url'];
				if(array_key_exists('method',$_h))$method=$_h['method'];
				if(array_key_exists('options',$_h))$options=$_h['options'];
				if(array_key_exists('addcachkey',$_h))$_addcachkey=$_h['addcachkey'];
			}
		}


		$getDataHandle=function()use($client,$method, $url, $options,$handle_end,$name){
			$_data=$client->apiRequest($method, $url, $options);
			Log::info( json_encode([$method, $url, $options,$_data],JSON_UNESCAPED_UNICODE) ); 
			if($handle_end){
				$_h=call_user_func_array( $handle_end, [$name ,null,['data'=>$_data,'options'=>$options,'extend'=>'end']]);
				if($_h && is_array($_h)){
					if(array_key_exists('data',$_h))$_data=$_h['data'];
				}
			}
			if(is_string($_data)){
				$_data=json_decode($_data,true);
			}
			return $_data;
		};
		$cachkeyList=[$default_fwtype,$_connct,$method,$options,$_addcachkey];
		$_data=$this->cache($cachkeyList,$getDataHandle );

		return $_data;

	}

}