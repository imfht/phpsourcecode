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
class Goods{
	
	public function validate($data){

		$error=array();
		if(empty($data['name'])){
			$error['error']='产品名称必填';
		}elseif(!isset($data['goods_category'])){
			$error['error']='产品分类必填';
		}
		
		if (isset($data['goods_option'])) {
				foreach ($data['goods_option'] as $goods_option) {
					
					if(!isset($goods_option['goods_option_value'])){
						$error['error']='选项值必填';
					}					
					
					foreach ($goods_option['goods_option_value'] as $k => $v) {
						if((int)$v['quantity']<=0){
							$error['error']='选项数量必填';
						}
					}
				}
		}
		
		if($error){
			return $error;				
		}
						
	}
	
	public function add_goods($data){		
			
			$goods['name']=$data['name'];
			$goods['image']=$data['image'];
			$goods['model']=$data['model'];
			$goods['sku']=$data['sku'];
			$goods['location']=$data['location'];
			$goods['price']=(float)$data['price'];
			$goods['quantity']=(int)$data['quantity'];
			$goods['points']=$data['points'];
			$goods['pay_points']=$data['pay_points'];
			
			if($goods['pay_points']>0){
				$goods['is_points_goods']=1;
			}else{
				$goods['is_points_goods']=0;
			}	
			
			$goods['minimum']=(int)$data['minimum'];
			$goods['subtract']=$data['subtract'];
			
			$goods['shipping']=$data['shipping'];

			$goods['weight_class_id']=$data['weight_class_id'];
			$goods['weight']=(float)$data['weight'];			
			
			$goods['length']=$data['length'];
			$goods['width']=$data['width'];
			$goods['height']=$data['height'];
			$goods['length_class_id']=$data['length_class_id'];
			
			$goods['status']=$data['status'];
			$goods['sort_order']=(int)$data['sort_order'];
			$goods['date_added']=date('Y-m-d H:i:s',time());
		
			
			$goods_id=Db::name('goods')->insert($goods,false,true);
			
			if($goods_id){
				
				try{
					$goods_description['goods_id']=$goods_id;
				
					$goods_description['summary']=$data['goods_description']['summary'];
					$goods_description['description']=$data['goods_description']['description'];
					$goods_description['meta_description']=$data['goods_description']['meta_description'];
					$goods_description['meta_keyword']=$data['goods_description']['meta_keyword'];
					
					
					Db::name('goods_description')->insert($goods_description);
					
					if (isset($data['goods_category'])) {
						foreach ($data['goods_category'] as $category_id) {
							Db::execute("INSERT INTO " . config('database.prefix'). "goods_to_category SET goods_id = '" . (int)$goods_id . "', category_id = '" . (int)$category_id . "'");
						}
					}
					
					if (isset($data['goods_attribute'])) {
						foreach ($data['goods_attribute'] as $attribute_id) {
							Db::execute("INSERT INTO " . config('database.prefix'). "goods_attribute SET goods_id = '" . (int)$goods_id . "', attribute_value_id =" . (int)$attribute_id);
						}
					}

					if (isset($data['goods_brand'])) {
						foreach ($data['goods_brand'] as $brand_id) {
							Db::execute("INSERT INTO " . config('database.prefix'). "goods_brand SET goods_id = '" . (int)$goods_id . "', brand_id = " . $brand_id );
						}
					}
					
					if (isset($data['goods_image'])){
						foreach ($data['goods_image'] as $image) {
							Db::execute("INSERT INTO " . config('database.prefix'). "goods_image SET goods_id =" . (int)$goods_id . ",image = '" . $image['image']."',sort_order =" . (int)$image['sort_order']);
						}
					}
					
					if (isset($data['mobile_image'])){
						foreach ($data['mobile_image'] as $mobile_image) {
							Db::execute("INSERT INTO " . config('database.prefix'). "goods_mobile_description_image SET goods_id =" . (int)$goods_id . ", image = '" . $mobile_image['image']."', description = '" . $mobile_image['description'] .  "', sort_order =" . (int)$mobile_image['sort_order']);
						}
					}
					
					if(isset($data['goods_discount'])){
						foreach ($data['goods_discount'] as $discount) {
							Db::execute("INSERT INTO " . config('database.prefix'). "goods_discount SET goods_id = '" . (int)$goods_id . "', quantity = '" . (int)$discount['quantity'] . "', price=".(float)$discount['price']);
						}
					}
					//商品选项					
					if (isset($data['goods_option'])) {
						$quantity=0;
						foreach ($data['goods_option'] as $goods_option) {
						if ($goods_option['type'] == 'select' || $goods_option['type'] == 'radio' || $goods_option['type'] == 'checkbox') {							

							$option_id=Db::name('goods_option')->insert(['goods_id'=>(int)$goods_id, 'option_id'=>(int)$goods_option['option_id'], 'required'=>(int)$goods_option['required'], 'option_name'=>$goods_option['name'],'type'=>$goods_option['type']],false,true);	
																	
							if (isset($goods_option['goods_option_value']) && count($goods_option['goods_option_value']) > 0 ) {
									foreach ($goods_option['goods_option_value'] as $goods_option_value) {
									
										Db::execute("INSERT INTO ".config('database.prefix')."goods_option_value SET goods_option_id=".(int)$option_id.",goods_id=".(int)$goods_id.",option_id=".(int)$goods_option['option_id'].",image='".(isset($goods_option_value['option_value_image'])?$goods_option_value['option_value_image']:'')."',option_value_id=".(int)$goods_option_value['option_value_id'].",quantity=".(int)$goods_option_value['quantity'].",subtract=".(int)$goods_option_value['subtract'].",price='".(float)$goods_option_value['price']."',price_prefix='".$goods_option_value['price_prefix']."',weight='".(float)$goods_option_value['weight']."',weight_prefix='".$goods_option_value['weight_prefix']."'");	
										$quantity+=$goods_option_value['quantity'];
									} 
								}
								
							}
					
						}
					
						Db::name('goods')->where('goods_id',$goods_id)->update(['quantity'=>$quantity]);

					}	
										
				    return true;
					
				}catch(Exception $e){
					return false;
				}
			}else{
				return false;
			}
		
		
	}
	
	public function edit_option($data){
		
			$id=(int)$data['goods_id'];
			$quantity=0;
			Db::name('goods_option')->where('goods_id',$id)->delete();
			Db::name('goods_option_value')->where('goods_id',$id)->delete();					
			if (isset($data['goods_option'])) {
				foreach ($data['goods_option'] as $goods_option) {
				if ($goods_option['type'] == 'select' || $goods_option['type'] == 'radio' 
				|| $goods_option['type'] == 'checkbox') {						
						
						$option_id=Db::name('goods_option')->insert(['goods_id'=>(int)$id, 'option_id'=>(int)$goods_option['option_id'], 'required'=>(int)$goods_option['required'], 'option_name'=>$goods_option['name'],'type'=>$goods_option['type']],false,true);
												
						if (isset($goods_option['goods_option_value']) && count($goods_option['goods_option_value']) > 0 ) {
								foreach ($goods_option['goods_option_value'] as $goods_option_value) {								
									Db::execute("INSERT INTO ".config('database.prefix')."goods_option_value SET goods_option_id=".(int)$option_id.",goods_id=".(int)$id.",option_id=".(int)$goods_option['option_id'].",image='".(isset($goods_option_value['option_value_image'])?$goods_option_value['option_value_image']:'')."',option_value_id=".(int)$goods_option_value['option_value_id'].",quantity=".(int)$goods_option_value['quantity'].",subtract=".(int)$goods_option_value['subtract'].",price='".(float)$goods_option_value['price']."',price_prefix='".$goods_option_value['price_prefix']."',weight='".(float)$goods_option_value['weight']."',weight_prefix='".$goods_option_value['weight_prefix']."'");	
									$quantity+=$goods_option_value['quantity'];
								} 
						}						
					}			
				}
			}

			if($quantity>0)
			Db::name('goods')->where('goods_id',$id)->update(['quantity'=>$quantity]);

	}

		function copy_goods($goods_id){
			
			$goods=Db::name('goods')->find($goods_id);	
			if ($goods) {
				$data = $goods;
				
				unset($data['goods_id']);
								
				$data['goods_description'] =Db::name('goods_description')->where('goods_id',$goods_id)->find();
				
				$data['goods_discount'] = Db::name('goods_discount')->where('goods_id',$goods_id)->select();
				
				$data['goods_image'] = Db::name('goods_image')->where('goods_id',$goods_id)->select();
				
				$data['mobile_image'] = Db::name('goods_mobile_description_image')->where('goods_id',$goods_id)->select();
				
				$data['goods_option'] =osc_goods()->get_goods_options($goods_id);
				
				$category = Db::name('goods_to_category')->where('goods_id',$goods_id)->select();
				
				foreach ($category as $k => $v) {
					$data['goods_category'][]=$v['category_id'];
				}
				
				$attribute = Db::name('goods_attribute')->where('goods_id',$goods_id)->select();
				
				foreach ($attribute as $k => $v) {
					$data['goods_attribute'][]=$v['attribute_value_id'];
				}
				
				$brand = Db::name('goods_brand')->where('goods_id',$goods_id)->select();
				
				foreach ($brand as $k => $v) {
					$data['goods_brand'][]=$v['brand_id'];
				}

			
				$this->add_goods($data);
			}
		}
		
		public function del_goods($goods_id){
			
			try{
									
				Db::name('goods')->where('goods_id',$goods_id)->delete();
				Db::name('goods_description')->where('goods_id',$goods_id)->delete();
				Db::name('goods_image')->where('goods_id',$goods_id)->delete();
				Db::name('goods_to_category')->where('goods_id',$goods_id)->delete();	
				
				Db::name('goods_attribute')->where('goods_id',$goods_id)->delete();	
				Db::name('goods_brand')->where('goods_id',$goods_id)->delete();	
				
				Db::name('goods_discount')->where('goods_id',$goods_id)->delete();	
				Db::name('goods_option')->where('goods_id',$goods_id)->delete();
				Db::name('goods_option_value')->where('goods_id',$goods_id)->delete();				
				Db::name('goods_mobile_description_image')->where('goods_id',$goods_id)->delete();
				
				return true;
			}catch(Exception $e){
				return false;
			}
		}

		public function get_link_data($id){
			$goods_id=(int)$id;
			return  [
				'goods_categories'=>Db::query('SELECT gc.name,gtc.category_id FROM '.config('database.prefix').'goods_to_category gtc,'.config('database.prefix').'category gc WHERE gc.id=gtc.category_id AND gtc.goods_id='.$goods_id),
				'goods_attribute'=>Db::query('SELECT av.attribute_value_id,av.value_name FROM '.config('database.prefix').'goods_attribute ga,'.config('database.prefix').'attribute_value av WHERE av.attribute_value_id=ga.attribute_value_id AND ga.goods_id='.$goods_id),
				'goods_brand'=>Db::query('SELECT b.name,b.brand_id FROM '.config('database.prefix').'goods_brand gb,'.config('database.prefix').'brand b WHERE gb.brand_id=b.brand_id AND gb.goods_id='.$goods_id)
			];
		}
		
		public function edit_links($data){
			try{
				$goods_id=(int)$data['goods_id'];
			
				if (isset($data['goods_category'])) {
					Db::name('goods_to_category')->where('goods_id',$goods_id)->delete();
					foreach ($data['goods_category'] as $category_id) {						
						Db::execute("INSERT INTO " . config('database.prefix'). "goods_to_category SET goods_id =".$goods_id.", category_id = " . (int)$category_id);
					}
				}
				
				if (isset($data['goods_attribute'])) {
					Db::name('goods_attribute')->where('goods_id',$goods_id)->delete();
					foreach ($data['goods_attribute'] as $attribute_id) {
						Db::execute("INSERT INTO " . config('database.prefix'). "goods_attribute SET goods_id =".$goods_id.", attribute_value_id =" . (int)$attribute_id);
					}
				}

				if (isset($data['goods_brand'])) {
					Db::name('goods_brand')->where('goods_id',$goods_id)->delete();
					foreach ($data['goods_brand'] as $brand_id) {
						Db::execute("INSERT INTO " . config('database.prefix'). "goods_brand SET goods_id =".$goods_id.", brand_id = " . $brand_id );
					}
				}
				
				return true;
			}catch(Exception $e){
				return false;
			}
		}
		
}
?>