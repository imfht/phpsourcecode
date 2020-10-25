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
class Attribute{
	
	public function validate($data) {

		$error=array();
		if ((mb_strlen($data['name']) <1) || (mb_strlen($data['name']) > 20)) {
			$error['error'] = '属性名称必须大于1小于20个字！！';
		}elseif (isset($data['attribute_value'])) {
			foreach ($data['attribute_value'] as $attribute_value_id => $attribute_value) {				
				if ((mb_strlen($attribute_value['name']) < 1) || (mb_strlen($attribute_value['name']) > 60)) {
					$error['error'] ='属性值必须大于0小于60个字！！';
				}				
			}
		}
		if($error){
			return $error;
			die;
		}
	
	}
	
	public function add_attribute($data){			
		
			$attribute['name']=$data['name'];				
			$attribute['update_time']=time();	
			$attribute['value']='';	
			foreach ($data['attribute_value'] as $k=> $v) {
				if(!empty($v)){
					if($v!=end($data['attribute_value'])){
						$attribute['value'].=$v['name'].',';
					}else{
						$attribute['value'].=$v['name'];
					}					
				}				
			}	
			
			$attribute_id=Db::name('attribute')->insert($attribute,false,true);
			
			if($attribute_id){
				
				foreach ($data['attribute_value'] as $k => $v) {						
						if(!empty($v)){
							
							$value['attribute_id']=$attribute_id;
							$value['value_name']=$v['name'];	
							$value['name']=$attribute['name'];					
							$value['value_sort_order']=$v['sort_order'];
						
							Db::name('attribute_value')->insert($value);
						}
					}
				
				return true;
			}else{
				return false;
			}		
	}
	public function edit_attribute($data){		
			
			$attribute['attribute_id']=$data['id'];
			$attribute['name']=$data['name'];	

			$attribute['update_time']=time();		
			$attribute['value']='';	
			
			foreach ($data['attribute_value'] as $k=> $v) {
				if(!empty($v)){
					if($v!=end($data['attribute_value'])){
						$attribute['value'].=$v['name'].',';
					}else{
						$attribute['value'].=$v['name'];
					}					
				}				
			}	
			
			$r=Db::name('attribute')->update($attribute,false,true);	
			
			if($r){
				
				Db::name('attribute_value')->where('attribute_id',$data['id'])->delete();
					
				foreach ($data['attribute_value'] as $k => $v) {						
						if(!empty($v)){
							
							$value['attribute_id']=$data['id'];
							$value['value_name']=$v['name'];						
							$value['value_sort_order']=$v['sort_order'];						
							$value['name']=$attribute['name'];	
							Db::name('attributeValue')->insert($value,false,true);
						}
					}
				
				return true;
			}else{
				return false;
			}		
	}

	
	
}
?>