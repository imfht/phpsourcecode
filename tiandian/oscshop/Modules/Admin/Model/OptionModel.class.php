<?php
/**
 * oscshop 电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */
namespace Admin\Model;

class OptionModel{
	

	public function show_option_page($search){
		
		$count=M('option')->count();
		
		$Page = new \Think\Page($count,C('BACK_PAGE_NUM'));
		
		$show  = $Page->show();
		
		$list = M('option')->order('option_id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		
		return array(
			'empty'=>'<tr><td colspan="20">~~暂无数据</td></tr>',
			'list'=>$list,
			'page'=>$show
		);	

	}
	
	public function add_option($data){		
			
		
			$option['name']=$data['name'];	
			$option['type']=$data['type'];		
			$option['update_time']=date('Y-m-d H:i:s',time());		
			foreach ($data['option_value'] as $k=> $v) {
				if(!empty($v)){
					if($v!=end($data['option_value'])){
						$option['value'].=$v['name'].',';
					}else{
						$option['value'].=$v['name'];
					}					
				}				
			}	
			
			$option_id=M('option')->add($option);
			
			if($option_id){
				
				foreach ($data['option_value'] as $k => $v) {						
						if(!empty($v)){
							
							$value['option_id']=$option_id;
							$value['value_name']=$v['name'];						
							$value['value_sort_order']=$v['sort_order'];
							
							M('OptionValue')->add($value);
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
			foreach ($data['option_value'] as $k=> $v) {
				if(!empty($v)){
					if($v!=end($data['option_value'])){
						$option['value'].=$v['name'].',';
					}else{
						$option['value'].=$v['name'];
					}					
				}				
			}	
			
			$r=M('option')->save($option);
			
			if($r){
				
				M('option_value')->where(array('option_id'=>$data['id']))->delete();
					
				foreach ($data['option_value'] as $k => $v) {						
						if(!empty($v)){
							
							$value['option_id']=$data['id'];
							$value['value_name']=$v['name'];						
							$value['value_sort_order']=$v['sort_order'];
							
							M('OptionValue')->add($value);
						}
					}
				
				return true;
			}else{
				return false;
			}		
	}
		
	
	function getOptions($filter_name) {
			
			$sql = "SELECT * FROM ".C('DB_PREFIX') . "option";
			
			if (isset($filter_name) && !is_null($filter_name)) {
				$sql .= " WHERE name LIKE '" . $filter_name . "%'";
			}
			
		
			$query = M()->query($sql);
	
			return $query;
	}	
	function getOptionValues($option_id) {
		$option_value_data = array();
		
		$option_value_query = M()->query("SELECT * FROM " 
		. C('DB_PREFIX') . "option_value ov LEFT JOIN " 
		. C('DB_PREFIX') . "option o ON (ov.option_id = o.option_id) WHERE ov.option_id =" 
		. (int)$option_id);
				
		foreach ($option_value_query as $option_value) {
			$option_value_data[] = array(
				'option_value_id' => $option_value['option_value_id'],
				'name'            => $option_value['name'],
				'value'           => $option_value['value_name'],				
				'sort_order'      => $option_value['value_sort_order']
			);
		}
		
		return $option_value_data;
	}	
	
}
?>