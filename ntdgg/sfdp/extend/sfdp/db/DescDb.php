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

class DescDb{
	
	public static function getDesign($sid){
		$info = Db::name('sfdp_design')->find($sid);
		if($info){
			return  $info;
		}else{
			return  false;
		}
	}
	public static function getDesignJson($sid){
		$info = Db::name('sfdp_design')->find($sid);
		$json = json_decode($info['s_field'],true);
		if($info){
			return $json;
		}else{
			return  false;
		}
	}
	
	 /**
     * 获取设计版本
     *
     * @param $status 版本状态 0为禁用 1为启用
     */
	 public static function getDescVer($status=1)
    {
		 $info = Db::name('sfdp_design_ver')->where('status',$status)->select();
        return $info;
		
	}
	/**
     * 获取设计版本
     *
     * @param $status 版本状态 0为禁用 1为启用
     */
	 public static function getDescVerVal($id)
    {
		 $info = Db::name('sfdp_design_ver')->find($id);
        return $info;
		
	}
	/**
     * 获取设计版本
     *
     * @param $status 版本状态 0为禁用 1为启用
     */
	public static function descVerTodata($sid){
		$sfdp_ver_info = self::getDescVerVal($sid);
		$field = json_decode($sfdp_ver_info['s_field'],true);
		$list_field = json_decode($sfdp_ver_info['s_list'],true);
		$searct_field = $sfdp_ver_info['s_search'];
		$listid = ''; //变量赋值为空
		$listfield = []; //变量赋值为空
			foreach($list_field as $key=>$vals){
				$listid.=$vals['tpfd_db'].',';
				$listfield[$vals['tpfd_db']]=$vals['tpfd_name'];
			}
		$fieldArr = [];
		$fieldArrAll = [];
			foreach($field['list'] as $k=>$v){
				foreach($v['data'] as $k2=>$v2){
					//xx_type //tpfd_data //td_type
					if(($v2['td_type']=='dropdown'||$v2['td_type']=='radio'||$v2['td_type']=='checkboxes')and($v2['tpfd_list']=='yes')){
						$fieldArr[$v2['tpfd_db']]=$v2['tpfd_data'];
					}
					if($v2['td_type']=='dropdown'||$v2['td_type']=='radio'||$v2['td_type']=='checkboxes'){
						$fieldArrAll[$v2['tpfd_db']]=$v2['tpfd_data'];
					}
				}
			}
		$load_file = SfdpUnit::Loadfile($field['name_db'],$field['tpfd_class'],$field['tpfd_script']);
		return ['sid'=>$sfdp_ver_info['id'],'db_name'=>$field['name_db'],'load_file'=>$load_file,'btn'=>$field['tpfd_btn'],'field'=>rtrim($listid, ','),'fieldname'=>$listfield,'search'=>$searct_field,'title'=>$sfdp_ver_info['s_name'],'fieldArr'=>$fieldArr,'fieldArrAll'=>$fieldArrAll];
	}
	public static function getAddData($sid){
		
		$sfdp_ver_info = self::getDescVerVal($sid);
		if($sfdp_ver_info['s_fun_id']!=''){
			$fun = '<script src="\static/sfdp/user-defined/'.$sfdp_ver_info['s_fun_ver'].'.js"></script>';	
		}else{
			$fun = '';
		}
		$field = json_decode($sfdp_ver_info['s_field'],true);
		$load_file = SfdpUnit::Loadfile($field['name_db'],$field['tpfd_class'],$field['tpfd_script']);
		return ['info'=>$sfdp_ver_info,'fun'=>$fun,'load_file'=>$load_file];
		
	}
	public static function getViewData($sid,$bid){
		$sfdp_ver_info = self::getDescVerVal($sid);
		$field = json_decode($sfdp_ver_info['s_field'],true);
		$find = Db::name($field['name_db'])->find($bid);
		foreach($field['list'] as $k=>$v){
				foreach($v['data'] as $k2=>$v2){
					
					if($v2['td_type']=='dropdown'||$v2['td_type']=='radio'||$v2['td_type']=='checkboxes'){
						$value_arr = explode(",",$find[$v2['tpfd_db']]);
						$fiedsver = '';
						foreach($value_arr as $v3){
							$fiedsver .=$v2['tpfd_data'][$v3].',';
						}
						$field['list'][$k]['data'][$k2]['value'] = rtrim($fiedsver, ',');
					}else{
						$field['list'][$k]['data'][$k2]['value'] = $find[$v2['tpfd_db']];
					}
				}
		}
		return ['info'=>json_encode($field)];
	}
	/**
     * 获取设计版本
     *
     * @param $status 版本状态 0为禁用 1为启用
     */
	public static function getListData($sid,$map){
		$jsondata = self::descVerTodata($sid);
		$list = Db::name($jsondata['db_name'])->where($map)->field('id,'.$jsondata['field'])->order('id desc')->paginate(10);
		$list = $list->all();
		foreach ($list as $k => $v) {
			foreach($jsondata['fieldArr'] as $k2=>$v2){
				$list[$k][$k2] = $jsondata['fieldArr'][$k2][$v[$k2]] ?? '<font color="red">索引出错</font>';
			}
		}
		$jsondata['search'] = SfdpUnit::mergesearch($map,$jsondata['search']);
		return ['list'=>$list,'field'=>$jsondata,'title'=>$jsondata['title']];
	}
	
	public static function saveDesc($data,$type='save'){
		if($type=='save'){
			$search = [];
			$list = [];
			$data['s_field'] = htmlspecialchars_decode($data['ziduan']);
			$field = json_decode($data['s_field'],true);
			foreach($field['list'] as $k=>$v){
				foreach($v['data'] as $v2){
					if(isset($v2['tpfd_chaxun'])&&($v2['tpfd_chaxun']=='yes')){
						$search[] = $v2;
					}
					if(isset($v2['tpfd_list'])&&($v2['tpfd_list']=='yes')){
						$list[] = $v2;
					}
				}
			}
			$ver = [
				'id'=>$data['id'],
				's_title'=>$field['name'],
				's_db'=>$field['name_db'],
				's_list'=>json_encode($list),
				's_search'=>json_encode($search),
				's_field'=>htmlspecialchars_decode($data['ziduan']),
				's_design'=>1
			];
			return Db::name('sfdp_design')->update($ver);;
			
		}elseif($type=='update'){
			return Db::name('sfdp_design')->update($data);;
		}else{
			$ver = [
				's_bill'=>OrderNumber(),
				'add_user'=>'Sys',
				's_field'=>1,
				'add_time'=>time()
			];
			return Db::name('sfdp_design')->insertGetId($ver);
		}
		
		
	}
}