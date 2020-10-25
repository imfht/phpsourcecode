<?php
/**
 * oscshop2 B2C电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015-2016 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */
namespace osc\admin\model;
use think\Db;
class Option{
	
	public function validate($data) {

		$error=array();
		if ((mb_strlen($data['name']) <1) || (mb_strlen($data['name']) > 30)) {
			$error['error'] = '选项名称必须大于1小于30个字！！';
		}elseif (($data['type'] == 'select' || $data['type'] == 'radio' || $data['type'] == 'checkbox'|| $data['type'] == 'image') && !isset($data['option_value'])) {
			$error['error'] ='选项值必填！！';
		}elseif (isset($data['option_value'])) {
			foreach ($data['option_value'] as $option_value_id => $option_value) {				
				if ((mb_strlen($option_value['name']) < 1) || (mb_strlen($option_value['name']) > 30)) {
					$error['error'] ='选项值必须大于0小于30个字！！';
				}				
			}
		}
		if($error){
			return $error;
			die;
		}
	
	}
	
	public function add_option($data){			
		
			$option['name']=$data['name'];	
			$option['type']=$data['type'];		
			$option['update_time']=date('Y-m-d H:i:s',time());	
			$option['value']='';	
			foreach ($data['option_value'] as $k=> $v) {
				if(!empty($v)){
					if($v!=end($data['option_value'])){
						$option['value'].=$v['name'].',';
					}else{
						$option['value'].=$v['name'];
					}					
				}				
			}	
			
			$option_id=Db::name('Option')->insert($option,false,true);
			
			if($option_id){
				
				foreach ($data['option_value'] as $k => $v) {						
						if(!empty($v)){
							
							$value['option_id']=$option_id;
							$value['value_name']=$v['name'];						
							$value['value_sort_order']=$v['sort_order'];
							
							Db::name('OptionValue')->insert($value);
						}
					}
				
				return true;
			}else{
				return false;
			}		
	}
	public function edit_option($data){		
			
			$option['option_id']=$data['id'];
			$option['name']=$data['name'];	
			$option['type']=$data['type'];		
			$option['update_time']=date('Y-m-d H:i:s',time());		
			$option['value']='';	
			
			foreach ($data['option_value'] as $k=> $v) {
				if(!empty($v)){
					if($v!=end($data['option_value'])){
						$option['value'].=$v['name'].',';
					}else{
						$option['value'].=$v['name'];
					}					
				}				
			}	
			
			$r=Db::name('option')->update($option,false,true);	
			
			if($r){
				
				Db::name('option_value')->where('option_id',$data['id'])->delete();
					
				foreach ($data['option_value'] as $k => $v) {						
						if(!empty($v)){
							
							$value['option_id']=$data['id'];
							$value['value_name']=$v['name'];						
							$value['value_sort_order']=$v['sort_order'];
							
							Db::name('OptionValue')->insert($value,false,true);
						}
					}
				
				return true;
			}else{
				return false;
			}		
	}
	
	function get_options($filter_name) {
			
			$sql = "SELECT * FROM ".config('database.prefix'). "option";
			
			if (isset($filter_name) && !is_null($filter_name)) {
				$sql .= " WHERE name LIKE '" . $filter_name . "%'";
			}			
	
			$query = Db::query($sql);
	
			return $query;
	}	
	
	
}
?>