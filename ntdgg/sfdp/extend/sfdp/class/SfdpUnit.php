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
use think\facade\Config;
use think\Exception;


class SfdpUnit{
	/**
     * 创建数据表
     */
    public static function Bmenu()
    {
		$menu = DescDb::getDescVer();
		$menu_html = '';
		foreach($menu as $k=>$v){
			$menu_html .='<li><a data-href="'.url('sfdp/list',['sid'=>$v['id']]).'" data-title="'.$v['s_name'].'">'.$v['s_name'].'</a></li>';
		}
		return $menu_html;
    }
	public static function Bsearch($search){
		$map =[];
		if(count($search)<>0){
			$search_field = json_decode(htmlspecialchars_decode($search['search']),true);
			foreach($search_field as $k=>$v){
				if($search[$v['tpfd_db']]<>''){
					$map[$v['tpfd_db']] = ['eq',$search[$v['tpfd_db']]];
				}
			}
		}
		return $map;
	}
	public static function mergesearch($map,$jsondata){
		if(count($map)==0){
			$json = $jsondata;
		}else{
			$search_field = json_decode($jsondata,true);
			foreach($search_field as $k=>$v){
				$search_field[$k]['tpfd_zanwei'] = $map[$v['tpfd_db']][1] ?? '';
			}
			$json = json_encode($search_field);
		}
		return $json;
	} 
	public static function Loadfile($db_name,$css,$js){
		$js_str = '';
		if($js!=''){
			$js_arry = explode("@@",$js);
			foreach($js_arry as $v){
				$js_str .= '<script src="\static/sfdp/user-class/'.$db_name.'/'.$v.'"></script>';
			}
		}
		$css_str = '';
		if($css!=''){
			$css_arry = explode("@@",$css);
			foreach($css_arry as $v){
				$css_str .= '<link rel="stylesheet" href="\\static/sfdp/user-class/'.$db_name.'/'.$v.'" />';
			}
		}
		return ['js'=>$js_str,'css'=>$css_str];
	}
}