<?php
/**
 *+------------------
 * SFDP-超级表单开发平台V3.0
 *+------------------
 * Copyright (c) 2018~2020 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
namespace sfdp;

use think\Db;

class ScriptDb{
	
	public static function script($sid){
		return Db::name('sfdp_script')->where('sid',$sid)->find();
	}
	public static function scriptSave($data){
		$info = self::ver($data['sid']);
		if(!$info){
			$ver = [
				's_bill'=>OrderNumber(),
				'add_user'=>'Sys',
				'sid'=>$data['sid'],
				's_fun'=>$data['function'],
				'add_time'=>time()
			];
			$id = Db::name('sfdp_script')->insertGetId($ver);
				  Db::name('sfdp_design_ver')->where('sid',$data['sid'])->where('status',1)->update(['s_fun_id'=>$id,'s_fun_ver'=>$ver['s_bill']]);
			$bill = $ver['s_bill'];
		}else{
			$ver = [
				'id'=>$info['id'],
				's_fun'=>$data['function']
			];	
			Db::name('sfdp_script')->update($ver);
			$bill=$info['s_bill'];
		}	
		return $bill;
	}
}