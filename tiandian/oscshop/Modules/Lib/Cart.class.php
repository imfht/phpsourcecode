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
namespace Lib;

class Cart {
	
	private $data = array();

	
	//加入购物车
	public function add($goods_id, $qty = 1, $option) {
		
		$key = (int)$goods_id.':';
		
		if ($option) {
			$key .= base64_encode(serialize($option)) . ':';
		}  else {
			$key .= ':';
		}
		
		if ((int)$qty && ((int)$qty > 0)) {			
			
			$key='cart.'.$key;
			
			$s=session($key);
			
			if (!isset($s)) {
				session($key,(int)$qty);
			} else {
				$qty=$s+(int)$qty;
				session($key,(int)$qty);			
			}		
			
		}	
		$this->data = array();
	}	
	
	//删除商品
	public function remove($key) {
		/**
		 * 此处有一bug,传人的$key是小写的,原因是开启了url小写状态,但是session取出的cart的key却是大写的。
		 * 下面代码把cart的key转为了小写，进行对比来修复这个bug
		 */
		$s=session('cart');	
		if (isset($s)) {
			foreach ($s as $k => $v) {			
			$lower_k=strtolower($k);
			if($key==$lower_k){//传人的$key等于转换过的$k值		
				$key='cart.'.$k;//重新给$key赋值
				session($key,null);				
			}		
		}
		}
	}	
	
	//更新购物车
	public function update($key, $qty) {
			
		$ckey='cart.'.$key;			
		
		if ((int)$qty && ((int)$qty > 0)) {
			session($ckey,(int)$qty);			
		} else {
			$this->remove($key);
		}
	
	}
	
	//获取购物车全部商品
	public function get_all_goods() {
		
		if (!($this->data)) {
			
			$cart=session('cart');			
			
			$hashids = new \Lib\Hashids(C('PWD_KEY'), C('URL_ID'));
			foreach ($cart as $key => $quantity) {			
				
				$goods = explode(':', $key);
				
				$goods_id = $goods[0];
				
				$stock = true;
			
				// Options
				if (!empty($goods[1])) {
					$options = unserialize(base64_decode($goods[1]));
				} else {
					$options = array();
				} 

				$goods_query = M()->query("SELECT * FROM " . C('DB_PREFIX') . "goods p LEFT JOIN " 
				. C('DB_PREFIX') . "goods_description pd ON (p.goods_id = pd.goods_id) WHERE p.goods_id = " 
				. (int)$goods_id . " AND p.status = 1");			
				
				if ($goods_query) {						
					
					$option_price = 0;
			
					$option_weight = 0;

					$option_data = array();
				
					foreach ($options as $goods_option_id => $option_value) {
						$option_query = M()->query("SELECT po.goods_option_id, po.option_id, o.name, o.type FROM "
						 . C('DB_PREFIX') . "goods_option po LEFT JOIN `" 
						 . C('DB_PREFIX') . "option` o ON (po.option_id = o.option_id) 
						  WHERE po.goods_option_id = '" 
						 . (int)$goods_option_id . "' AND po.goods_id = " . (int)$goods_id);
				
				
						if ($option_query) {
							if ($option_query[0]['type'] == 'select' || $option_query[0]['type'] == 'radio') {
								$option_value_query = M()->query("SELECT pov.option_value_id, 
				ov.value_name, pov.quantity, pov.subtract, pov.price, pov.price_prefix,pov.weight, pov.weight_prefix FROM " 
				. C('DB_PREFIX') . "goods_option_value pov LEFT JOIN " 
				. C('DB_PREFIX') . "option_value ov ON (pov.option_value_id = ov.option_value_id) WHERE pov.goods_option_value_id = '" 
				. (int)$option_value . "' AND pov.goods_option_id = " 
				. (int)$goods_option_id);
					
								if ($option_value_query) {
									if ($option_value_query[0]['price_prefix'] == '+') {
										$option_price += $option_value_query[0]['price'];
									} elseif ($option_value_query[0]['price_prefix'] == '-') {
										$option_price -= $option_value_query[0]['price'];
									}

									if ($option_value_query[0]['weight_prefix'] == '+') {
										$option_weight += $option_value_query[0]['weight'];
									} elseif ($option_value_query[0]['weight_prefix'] == '-') {
										$option_weight -= $option_value_query[0]['weight'];
									}

									if ($option_value_query[0]['subtract'] && (!$option_value_query[0]['quantity'] || ($option_value_query[0]['quantity'] < $quantity))) {
										$stock = false;
									}

									$option_data[] = array(
										'goods_option_id'       => $goods_option_id,
										'goods_option_value_id' => $option_value,
										'option_id'               => $option_query[0]['option_id'],
										'option_value_id'         => $option_value_query[0]['option_value_id'],
										'name'                    => $option_query[0]['name'],
										'value'            => $option_value_query[0]['value_name'],
										'type'                    => $option_query[0]['type'],
										'quantity'                => $option_value_query[0]['quantity'],
										'subtract'                => $option_value_query[0]['subtract'],
										'price'                   => $option_value_query[0]['price'],
										'price_prefix'            => $option_value_query[0]['price_prefix'],
														
										'weight'                  => $option_value_query[0]['weight'],
										
										'weight_prefix'           => $option_value_query[0]['weight_prefix']
									);								
								}
							} elseif ($option_query[0]['type'] == 'checkbox' && is_array($option_value)) {
								foreach ($option_value as $goods_option_value_id) {
								

								$option_value_query = M()->query("SELECT pov.option_value_id, ov.value_name, pov.quantity,
								 pov.subtract, pov.price, pov.price_prefix,pov.weight,
								  pov.weight_prefix FROM " . C('DB_PREFIX') .  "goods_option_value pov LEFT JOIN ". C('DB_PREFIX') 
								  ."option_value ov ON (pov.option_value_id = ov.option_value_id) WHERE pov.goods_option_value_id = '" 
								.(int)$goods_option_value_id . "' AND pov.goods_option_id = ". (int)$goods_option_id);


									if ($option_value_query) {
										if ($option_value_query[0]['price_prefix'] == '+') {
											$option_price += $option_value_query[0]['price'];
										} elseif ($option_value_query[0]['price_prefix'] == '-') {
											$option_price -= $option_value_query[0]['price'];
										}

										if ($option_value_query[0]['weight_prefix'] == '+') {
											$option_weight += $option_value_query[0]['weight'];
										} elseif ($option_value_query[0]['weight_prefix'] == '-') {
											$option_weight -= $option_value_query[0]['weight'];
										}

										if ($option_value_query[0]['subtract'] && (!$option_value_query[0]['quantity'] || ($option_value_query[0]['quantity'] < $quantity))) {
											$stock = false;
										}

										$option_data[] = array(
											'goods_option_id'       => $goods_option_id,
											'goods_option_value_id' => $goods_option_value_id,
											'option_id'               => $option_query[0]['option_id'],
											'option_value_id'         => $option_value_query[0]['option_value_id'],
											'name'                    => $option_query[0]['name'],
											'value'            		=> $option_value_query[0]['value_name'],
											'type'                    => $option_query[0]['type'],
											'quantity'                => $option_value_query[0]['quantity'],
											'subtract'                => $option_value_query[0]['subtract'],
											'price'                   => $option_value_query[0]['price'],
											'price_prefix'            => $option_value_query[0]['price_prefix'],
								
											'weight'                  => $option_value_query[0]['weight'],
											'weight_prefix'           => $option_value_query[0]['weight_prefix']
										);								
									}
								}						
							} 
						}
					} 
					
					
					$price = $goods_query[0]['price'];						
					
					$discount=M()->query("SELECT price FROM " . C('DB_PREFIX') . "goods_discount WHERE goods_id = '" . (int)$goods_id . "' AND quantity <=" . (int)$quantity . " ORDER BY quantity DESC, price ASC LIMIT 1");
		
					if($discount){
						$price=$discount[0]['price'];
					}
					
					
					$this->data[$key] = array(
						'key'                       => $key,
			
						'goods_id'                  =>$hashids->encode($goods_query[0]['goods_id']),
						'name'                      => $goods_query[0]['name'],
						'model'                     => $goods_query[0]['model'],
						'shipping'                  => $goods_query[0]['shipping'],						
						'image'                     => resize($goods_query[0]['image'],C('goods_cart_thumb_width'),C('goods_cart_thumb_height')),
						'quantity'                  => $quantity,
						'minimum'                   => $goods_query[0]['minimum'],
						'subtract'                  => $goods_query[0]['subtract'],						
						'price'                     =>$price+$option_price,						
						'total'                     =>($price+$option_price) * $quantity,
					
						'weight'          			=>($goods_query[0]['weight'] + $option_weight) * $quantity,
						'weight_class_id'           => $goods_query[0]['weight_class_id'],
						'length'                    => $goods_query[0]['length'],
						'width'                     => $goods_query[0]['width'],
						'height'                    => $goods_query[0]['height'],
						'length_class_id'           => $goods_query[0]['length_class_id'],
						'stock'                     => $stock,
						'option'                    => $option_data,				
					);
				
				} else {
					$this->remove($key);
				}
			}
		}
	
		return $this->data;
	}	
	
	//计算商品总数
	public function count_goods() {
		$goods_total = 0; 

		$goods = $this->get_all_goods();
		
		foreach ($goods as $goods) {
			
			$goods_total += $goods['quantity'];
		}		
		
		return $goods_total;
	}	
	//得到商品数量
	public function get_goods_quantity($goods_id){
		
		return M('goods')->where(array('goods_id'=>$goods_id))->getField('quantity');		
		
	}
	//取得商品重量
	public function getWeight() {
		$weight = 0;
		$w=new \Lib\Weight();
		foreach ($this->get_all_goods() as $product) {
			
			if ($product['shipping']) {
				$weight += $w->convert($product['weight'], $product['weight_class_id'],C('WEIGHT_ID'));
			}
		}

		return $weight;
	}
	
	//是否需要派送,下载类商品不需要配送
	public function has_shipping() {
		$shipping = false;

		foreach ($this->get_all_goods() as $product) {
			if ($product['shipping']) {
				$shipping = true;

				break;
			}
		}

		return $shipping;
	}
	//购物车是否为空
	public function has_goods() {
		$s=session('cart');
		return count($s);
	}
}
