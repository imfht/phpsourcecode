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
use think\Model;
class Category extends Model{
	
	public function add($data){
		
		$validate = validate('Category');
		if(!$validate->check($data)){
		    return ['error'=>$validate->getError()];
		}
		
		$category['image']=$data['image'];
		$category['pid']=$data['pid'];
		$category['name']=$data['name'];
		$category['meta_keyword']=$data['meta_keyword'];
		$category['meta_description']=$data['meta_description'];
		$category['update_time']=time();
		
		$cid=Db::name('category')->insert($category,false,true);
		
		if($cid){
			
			if(isset($data['category_attribute'])){
				foreach ($data['category_attribute'] as $attribute_id) {
					Db::execute("INSERT INTO " . config('database.prefix'). "category_to_attribute SET cid =" . $cid . ",attribute_id =" . (int)$attribute_id);
				}
			}
			if(isset($data['category_brand'])){
				foreach ($data['category_brand'] as $brand_id) {
					Db::execute("INSERT INTO " . config('database.prefix'). "category_to_brand SET cid =" . $cid . ",brand_id =" . (int)$brand_id);
				}
			}
			return true;
		}else{
			return false;
		}
		
	}
	
	public function edit($data){
		
		$validate = validate('Category');
		
		if(!$validate->check($data)){
		    return ['error'=>$validate->getError()];
		}
		$cid=(int)$data['id'];
		
		$category['image']=$data['image'];
		$category['pid']=$data['pid'];
		$category['name']=$data['name'];
		$category['meta_keyword']=$data['meta_keyword'];
		$category['meta_description']=$data['meta_description'];
		$category['update_time']=time();
		
		$r=Db::name('category')->where('id',$cid)->update($category,false,true);
		
		if($r){
			
			if(isset($data['category_attribute'])){
				Db::name('category_to_attribute')->where('cid',$cid)->delete();
				foreach ($data['category_attribute'] as $attribute_id) {
					Db::execute("INSERT INTO " . config('database.prefix'). "category_to_attribute SET cid =" . $cid . ",attribute_id =" . (int)$attribute_id);
				}
			}
			if(isset($data['category_brand'])){
				Db::name('category_to_brand')->where('cid',$cid)->delete();
				foreach ($data['category_brand'] as $brand_id) {
					Db::execute("INSERT INTO " . config('database.prefix'). "category_to_brand SET cid =" . $cid . ",brand_id =" . (int)$brand_id);
				}
			}
			return true;
		}else{
			return false;
		}
		
	}

	public function del_category($cid){			
			
			try{
									
				Db::name('category')->where('id',$cid)->delete();
				Db::name('category_to_attribute')->where('cid',$cid)->delete();
				Db::name('category_to_brand')->where('cid',$cid)->delete();			
				
				return true;
			}catch(Exception $e){
				return false;
			}
	}

	//分类关联数据
	public function category_link_data($cid){
		return [
			'attribute'=>Db::query('SELECT a.name,a.value,a.attribute_id FROM '.config('database.prefix').'attribute a,'.config('database.prefix').'category_to_attribute cta WHERE a.attribute_id=cta.attribute_id AND cta.cid='.$cid),
			'brand'=>Db::query('SELECT b.name,b.brand_id FROM '.config('database.prefix').'brand b,'.config('database.prefix').'category_to_brand ctb WHERE b.brand_id=ctb.brand_id AND ctb.cid='.$cid)
			];
	}
	
}
?>