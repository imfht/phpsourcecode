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
namespace Home\Controller;

class CartController extends CommonController {
	
	//结算
	function checkout(){
	
		$this->title='结算-';
		$this->meta_keywords=C('SITE_KEYWORDS');
		$this->meta_description=C('SITE_DESCRIPTION');		
		
		$cart=new \Lib\Cart();
		if ((!$cart->has_goods()) ) {
			$this->redirect('/cart');
			die;
		}

		$this->shipping_required =$cart->has_shipping();	
		$this->display();
	}
	
	//显示购物车中商品列表
	function show_cart_goods(){
		
		$cart=new \Lib\Cart();
		
		$goods=$cart->get_all_goods();
		
		$this->goods=$goods;
		
		$total_all_price=0;
		$weight = 0;
		$w=new \Lib\Weight();
		
		if(isset($goods)){
			foreach ($goods as $k => $v) {
	
				$total_all_price+=$v['total'];
				if ($v['shipping']) {
					$weight += $w->convert($v['weight'], $v['weight_class_id'],C('WEIGHT_ID'));
				}
			}
		}
		
		$this->total_price=$total_all_price;
		
		$this->weight=$weight;
		
		$this->display('index');
		
	}
	
	//更新购物车商品数量
	function update_quantity(){
		$d=I('post.');
		$cart=new \Lib\Cart();
		$hashids = new \Lib\Hashids(C('PWD_KEY'), C('URL_ID'));
		$goods_id=$hashids->decode($d['id']);	
		
		$key=$d['o'];
		
		$goods=M('goods')->find($goods_id[0]);
		
		//商品存在
		if($goods){		

			if($goods['quantity']<$d['q']){
				$json['error']='商品数量不足，剩余'.$goods['quantity'].'个！！';
			}
			if (!$json) {
			
				$cart->update($key,$d['q']);
				
				$goods_list=$cart->get_all_goods();				

				$total=0;
				$total_all_price=0;
				$weight = 0;
				$w=new \Lib\Weight();
				
				foreach ($goods_list as $k => $v) {
					if($k==$key){
						$price=$v['price'];
						$total_price=$v['total'];
					}
					$total+=$v['quantity'];
					$total_all_price+=$v['total'];
					if ($v['shipping']) {
						$weight += $w->convert($v['weight'], $v['weight_class_id'],C('WEIGHT_ID'));
					}
				}
				
				//商品数量
				session('cart_total',$total);	
				
				$json['success']=$total;
				
				//商品单价
				$json['price']=$price;
				//单个商品总价
				$json['total_price']=$total_price;
				//所有商品总价
				$json['total_all_price']=$total_all_price;
				//所有商品重量
				$json['weight']=$weight;
				
			}
			$this->ajaxReturn($json);
			die;
		}

	}
	//删除商品
	function remove(){
		
		$cart=new \Lib\Cart();
		
		$cart->remove($_GET['data']);			
		
		$total=$cart->count_goods();
				
		session('cart_total',$total);
		
		$this->redirect('/cart');	
	}
	
	
	//加入购物车	
	function add(){
		$data=I('post.');
		
		if (isset($data['goods_id'])) {
			$goods_id = $data['goods_id'];
		} else {
			$goods_id = 0;
		}
		$product=M('goods')->find($goods_id);
		
		//商品存在
		if($product){
			
			$cart=new \Lib\Cart();
			
			if (isset($data['quantity'])) {
				$quantity = $data['quantity'];
			} else {
				$quantity = 1;
			}
		
			if (isset($data['option'])) {
				$option = array_filter($data['option']);
			} else {
				$option = array();	
			}			
		
			$json=array();
			
			$goods_quantity=$cart->get_goods_quantity($goods_id);
		
			if($goods_quantity<$quantity){
				$json['error']['quantity']='商品数量不足，剩余'.$goods_quantity.'个！！';
			}	
			
			$goods_options=A('goods')->get_goods_options($goods_id);
			
			foreach ($goods_options as $product_option) {
				if ($product_option['required'] && empty($option[$product_option['goods_option_id']])) {					
					$json['error']['option'][$product_option['goods_option_id']] = sprintf('必填', $product_option['name']);
				}
			}

			if (!$json) {
				
				$cart->add($goods_id,$quantity,$option);
				
				$total=$cart->count_goods();
				
				session('cart_total',$total);
				
				$json['success']='成功加入购物车！！';
				
				$json['total']=$total;
			}
			
			$this->ajaxReturn($json);
			die;
		}		
	}
    
}