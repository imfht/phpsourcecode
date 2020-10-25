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
use Think\Model;
class GoodsModel extends Model{
	
	function copy_goods($goods_id){
		$query = M()->query("SELECT DISTINCT * FROM " . C('DB_PREFIX') . "goods p LEFT JOIN " . C('DB_PREFIX') . "goods_description pd ON (p.goods_id = pd.goods_id) WHERE p.goods_id =" . (int)$goods_id);

		if ($query) {
			$data = $query[0];
		
			$data['viewed'] = '0';
			$data['image']='';							
						
			$data['goods_description'] =M('goods_description')->where(array('goods_id'=>$goods_id))->find();
			
			$data['goods_description']['name']=$data['name'];
			
			$data['goods_discount'] = M('goods_discount')->where(array('goods_id'=>$goods_id))->select();

			$category = M('goods_to_category')->where(array('goods_id'=>$goods_id))->select();
			
			foreach ($category as $k => $v) {
				$data['goods_category'][]=$v['category_id'];
			}
			
			$this->add_Goods($data);
		}
	}
	
	
	public function del_Goods($id){
		try{
			
			$image=M('goods')->where(array('goods_id'=>$id))->field('image')->find();			
			if(!empty($image)){
				A('Image')->del_image('goods',$image['image'],'goods');
			}
			
			$gallery=M('goods_image')->where(array('goods_id'=>$id))->field('image')->select();
			
			if(!empty($gallery)){
				foreach ($gallery as $key => $value) {
					A('Image')->del_image('gallery',$value['image'],'gallery');
				}
			}
			
					
			M('goods')->where(array('goods_id'=>$id))->delete();
			M('goods_description')->where(array('goods_id'=>$id))->delete();
			M('goods_image')->where(array('goods_id'=>$id))->delete();
			M('goods_to_category')->where(array('goods_id'=>$id))->delete();	
			M('goods_discount')->where(array('goods_id'=>$id))->delete();	
			M('goods_option')->where(array('goods_id'=>$id))->delete();
			M('goods_option_value')->where(array('goods_id'=>$id))->delete();				
			return array(
				'status'=>'success',
				'message'=>'删除成功',
				'jump'=>U('Goods/index')
				);
			
		}catch(Exception $e){
			return array(
				'status'=>'fail',
				'message'=>'删除失败,未知异常',
				'jump'=>U('Goods/index')
			);
		}
	}
	
	public function get_goods_data($id){
		
		$d=M('Goods')->find($id);
		
		$d['thumb_image']=resize($d['image'], 100, 100);
		
		return $d;
		
	}

	public function get_goods_image_data($id){
		
		$d=M('goods_image')->where(array('goods_id'=>$id))->select();
		
		foreach ($d as $k => $v) {
			$d[$k]['thumb']=resize($v['image'], 100, 100);
		}
		
		return $d;
		
	}
		public function get_goods_category_data($id){
		
		$sql='SELECT pc.name,ptc.category_id FROM '.C('DB_PREFIX').'goods_to_category ptc,'
		.C('DB_PREFIX').'goods_category pc WHERE pc.id=ptc.category_id AND ptc.goods_id='.$id;
		
		$d=M()->query($sql);

		
		return $d;
		
	}
	
	public function show_goods_page($search){
		
		$sql='SELECT p.goods_id,p.name,p.price,p.quantity,p.status,p.model,p.image,gtc.category_id FROM '.C('DB_PREFIX').'goods_description pd,'
		.C('DB_PREFIX').'goods p,'.C('DB_PREFIX').'goods_to_category gtc WHERE pd.goods_id=p.goods_id and p.goods_id=gtc.goods_id';
		
		if(isset($search['name'])){
			$sql.=" and p.name like '%".$search['name']."%'";
		}
		if(isset($search['category'])){
			$sql.=" and gtc.category_id=".$search['category'];
		}
		if(isset($search['status'])){
			$sql.=" and p.status=".$search['status'];
		}
		
		$count=count(M()->query($sql));
		
		$Page = new \Think\Page($count,C('BACK_PAGE_NUM'));
		
		$show  = $Page->show();// 分页显示输出	
		
		$sql.=' order by p.goods_id desc LIMIT '.$Page->firstRow.','.$Page->listRows;
		
		$list=M()->query($sql);		
		
		foreach ($list as $key => $value) {
			$list[$key]['image']=resize($value['image'], 50, 50);
		}
		
		return array(
			'empty'=>'<tr><td colspan="20">~~暂无数据</td></tr>',
			'list'=>$list,
			'page'=>$show
		);	
		
	}	
		public function validate($data){

			$error=array();
			if(empty($data['goods_description']['name'])){
				$error='产品名称必填';
			}
			if(!isset($data['goods_category'])){
				$error='产品分类必填';
			}
			
			if($error){
			
				return array(
					'status'=>'back',
					'message'=>$error				
				);
				
			}
			
			
			
	}
		
	public function edit_Goods($data){
		$error=$this->validate($data);
			
			if($error){
				return $error;
			}
			
			$id=$data['goods_id'];
			
			$goods['goods_id']=$id;
			$goods['name']=$data['goods_description']['name'];
			$goods['image']=$data['image'];
			$goods['model']=$data['model'];
			$goods['sku']=$data['sku'];
			$goods['location']=$data['location'];
			$goods['price']=$data['price'];
			$goods['quantity']=$data['quantity'];
			$goods['minimum']=$data['minimum'];
			$goods['subtract']=$data['subtract'];
			$goods['stock_status_id']=$data['stock_status_id'];
			$goods['shipping']=$data['shipping'];
			$goods['length']=$data['length'];
			$goods['width']=$data['width'];
			$goods['height']=$data['height'];
			$goods['length_class_id']=$data['length_class_id'];
			$goods['weight_class_id']=$data['weight_class_id'];
			$goods['weight']=$data['weight'];			
			$goods['status']=$data['status'];
			$goods['sort_order']=$data['sort_order'];
			$goods['date_modified']=date('Y-m-d H:i:s',time());

			
			$r=M('Goods')->save($goods);
			
			if($r){					
				
				$goods_description['summary']=$data['goods_description']['summary'];
				$goods_description['description']=$data['goods_description']['description'];
				$goods_description['meta_description']=$data['goods_description']['meta_description'];
				$goods_description['meta_keyword']=$data['goods_description']['meta_keyword'];
				$goods_description['tag']=$data['goods_description']['tag'];				
				M('goods_description')->where(array('goods_id'=>$id))->save($goods_description);
				
				try{
					M('GoodsToCategory')->where(array('goods_id'=>$id))->delete();
					//商品分类
					if(isset($data['goods_category'])){
						foreach ($data['goods_category'] as $category_id) {
							$this->execute("INSERT INTO " . C('DB_PREFIX') . "goods_to_category SET goods_id = '" . (int)$id . "', category_id = '" . (int)$category_id . "'");
						}
					}
					M('GoodsImage')->where(array('goods_id'=>$id))->delete();	
					if (isset($data['goods_image'])) {
						foreach ($data['goods_image'] as $goods_image) {
							$this->execute("INSERT INTO " . C('DB_PREFIX') . "goods_image SET goods_id = '" . (int)$id . "', image = '" . $goods_image['image'] . "', sort_order = '" . (int)$goods_image['sort_order'] . "'");
						}
					}
					M('goods_discount')->where(array('goods_id'=>$id))->delete();	
					if (isset($data['goods_discount'])) {
						foreach ($data['goods_discount'] as $discount) {
							$this->execute("INSERT INTO " . C('DB_PREFIX') . "goods_discount SET goods_id = '" . (int)$id . "', quantity = '" . $discount['quantity'] . "', price=".$discount['price']);
						}
					}
					//商品选项
					//商品选项	
					M('goods_option')->where(array('goods_id'=>$id))->delete();
					M('goods_option_value')->where(array('goods_id'=>$id))->delete();					
					if (isset($data['goods_option'])) {
						foreach ($data['goods_option'] as $goods_option) {
						if ($goods_option['type'] == 'select' || $goods_option['type'] == 'radio' 
						|| $goods_option['type'] == 'checkbox') {
								
							$option['goods_id']=$id;
							$option['option_id']=(int)$goods_option['option_id'];
							$option['required']	=(int)$goods_option['required'];
							$option['option_name']	=$goods_option['name'];
							$option['type']	=$goods_option['type'];
							
							$option_id=M('goods_option')->add($option);
				
							if (isset($goods_option['goods_option_value']) && count($goods_option['goods_option_value']) > 0 ) {
									foreach ($goods_option['goods_option_value'] as $goods_option_value) {
										$option_value['goods_option_id']=(int)$option_id;
										$option_value['goods_id']=$id; 
										$option_value['option_id']=(int)$goods_option['option_id']; 
										
										$option_value['image']=$goods_option_value['option_value_imgae']; 
										
										$option_value['option_value_id']=(int)$goods_option_value['option_value_id']; 
										$option_value['quantity']=(int)$goods_option_value['quantity'];
										$option_value['subtract']=(int)$goods_option_value['subtract'];
										$option_value['price']=(float)$goods_option_value['price'] ;
										$option_value['price_prefix']=$goods_option_value['price_prefix'];
								
										$option_value['weight']=(float)$goods_option_value['weight']; 
										$option_value['weight_prefix']=$goods_option_value['weight_prefix'];
										M('goods_option_value')->add($option_value);					
									} 
								}
								
							}
					
						}
					}	
					return array(
						'status'=>'success',
						'message'=>'修改成功',
						'jump'=>U('Goods/index')
					);
				}catch(Exception $e){
					return array(
					'status'=>'fail',
					'message'=>'修改失败,未知异常',
					'jump'=>U('Goods/index')
					);
				}
				
			}else{
				return array(
				'status'=>'fail',
				'message'=>'修改失败',
				'jump'=>U('Goods/index')
				);
			}
			
	}	
		
	 function add_Goods($data){
			
			$error=$this->validate($data);
			
			if($error){
							
				return $error;
			}			
			$goods['name']=$data['goods_description']['name'];
			$goods['image']=$data['image'];
			$goods['model']=$data['model'];
			$goods['sku']=$data['sku'];
			$goods['location']=$data['location'];
			$goods['price']=$data['price'];
			$goods['quantity']=$data['quantity'];
			$goods['minimum']=$data['minimum'];
			$goods['subtract']=$data['subtract'];
			$goods['stock_status_id']=$data['stock_status_id'];
			$goods['shipping']=$data['shipping'];
			$goods['length']=$data['length'];
			$goods['width']=$data['width'];
			$goods['height']=$data['height'];
			$goods['length_class_id']=$data['length_class_id'];
			$goods['weight_class_id']=$data['weight_class_id'];
			$goods['weight']=$data['weight'];			
			$goods['status']=$data['status'];
			$goods['sort_order']=$data['sort_order'];
			$goods['date_added']=date('Y-m-d H:i:s',time());
		
			
			$goods_id=M('Goods')->add($goods);
			
			if($goods_id){
				
				try{
					$goods_description['goods_id']=$goods_id;
				
					$goods_description['summary']=$data['goods_description']['summary'];
					$goods_description['description']=$data['goods_description']['description'];
					$goods_description['meta_description']=$data['goods_description']['meta_description'];
					$goods_description['meta_keyword']=$data['goods_description']['meta_keyword'];
					$goods_description['tag']=$data['goods_description']['tag'];
					
					M('goods_description')->add($goods_description);
					
					if (isset($data['goods_category'])) {
						foreach ($data['goods_category'] as $category_id) {
							$this->execute("INSERT INTO " . C('DB_PREFIX') . "goods_to_category SET goods_id = '" . (int)$goods_id . "', category_id = '" . (int)$category_id . "'");
						}
					}
					if (isset($data['goods_image'])) {
						foreach ($data['goods_image'] as $goods_image) {
							$this->execute("INSERT INTO " . C('DB_PREFIX') . "goods_image SET goods_id = '" . (int)$goods_id . "', image = '" . $goods_image['image'] . "', sort_order = '" . (int)$goods_image['sort_order'] . "'");
						}
					}
					if (isset($data['goods_discount'])) {
						foreach ($data['goods_discount'] as $discount) {
							$this->execute("INSERT INTO " . C('DB_PREFIX') . "goods_discount SET goods_id = '" . (int)$goods_id . "', quantity = '" . $discount['quantity'] . "', price=".$discount['price']);
						}
					}
					//商品选项					
					if (isset($data['goods_option'])) {
						foreach ($data['goods_option'] as $goods_option) {
						if ($goods_option['type'] == 'select' || $goods_option['type'] == 'radio' 
						|| $goods_option['type'] == 'checkbox') {
								
							$option['goods_id']=$goods_id;
							$option['option_id']=(int)$goods_option['option_id'];
							$option['required']	=(int)$goods_option['required'];
							$option['option_name']	=$goods_option['name'];
							$option['type']	=$goods_option['type'];
							
							$option_id=M('goods_option')->add($option);
				
							if (isset($goods_option['goods_option_value']) && count($goods_option['goods_option_value']) > 0 ) {
									foreach ($goods_option['goods_option_value'] as $goods_option_value) {
										$option_value['goods_option_id']=(int)$option_id;
										$option_value['goods_id']=(int)$goods_id; 
										$option_value['option_id']=(int)$goods_option['option_id']; 
										
										$option_value['image']=$goods_option_value['option_value_imgae']; 
										
										$option_value['option_value_id']=(int)$goods_option_value['option_value_id']; 
										$option_value['quantity']=(int)$goods_option_value['quantity'];
										$option_value['subtract']=(int)$goods_option_value['subtract'];
										$option_value['price']=(float)$goods_option_value['price'] ;
										$option_value['price_prefix']=$goods_option_value['price_prefix'];
								
										$option_value['weight']=(float)$goods_option_value['weight']; 
										$option_value['weight_prefix']=$goods_option_value['weight_prefix'];
										M('goods_option_value')->add($option_value);					
									} 
								}
								
							}
					
						}
					}	
					
					return array(
						'status'=>'success',
						'message'=>'新增成功',
						'jump'=>U('Goods/index')
					);
				}catch(Exception $e){
					return array(
					'status'=>'fail',
					'message'=>'新增失败',
					'jump'=>U('Goods/index')
					);
				}
			}else{
				return array(
				'status'=>'fail',
				'message'=>'新增失败',
				'jump'=>U('Goods/index')
				);
			}
		
		
	}	

	 function get_goods_options($goods_id) {
		$goods_option_data = array();
		
		$goods_option_query = M()->query("SELECT * FROM " . C('DB_PREFIX') . "goods_option po LEFT JOIN " 
		. C('DB_PREFIX') . "option o ON po.option_id = o.option_id WHERE po.goods_id =".(int)$goods_id);
		
		foreach ($goods_option_query as $goods_option) {
			$goods_option_value_data = array();	
				
			$goods_option_value_query = M()->query("SELECT * FROM " . C('DB_PREFIX') 
			. "goods_option_value WHERE goods_option_id = '" 
			. (int)$goods_option['goods_option_id'] . "'");
	
			foreach ($goods_option_value_query as $goods_option_value) {
				$goods_option_value_data[] = array(
					'goods_option_value_id' => $goods_option_value['goods_option_value_id'],
					'option_value_id'         => $goods_option_value['option_value_id'],
					'quantity'                => $goods_option_value['quantity'],
					'subtract'                => $goods_option_value['subtract'],
					'price'                   => $goods_option_value['price'],
					'price_prefix'            => $goods_option_value['price_prefix'],
					'image'				=> $goods_option_value['image'],
					'weight'                  => $goods_option_value['weight'],
					'weight_prefix'           => $goods_option_value['weight_prefix']					
				);
			}
				
			$goods_option_data[] = array(
				'goods_option_id'    => $goods_option['goods_option_id'],
				'option_id'            => $goods_option['option_id'],
				'name'                 => $goods_option['name'],
				'type'                 => $goods_option['type'],					
				'option_value'         => $goods_option['name'],
				'required'             => $goods_option['required'],
				'goods_option_value'   =>  $goods_option_value_data,				
			);
		}
	
		return $goods_option_data;
	}





}
?>