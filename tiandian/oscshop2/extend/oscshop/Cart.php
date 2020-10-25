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
namespace oscshop;
use think\Db;
class Cart{
	
	
	/**
	 * 取得会员购物车商品
	 * $uid 会员id
	 * $type 支付类型，money在线付款，points积分兑换
	 */
	public function get_all_goods($uid,$type='money') {
		
		$goods_data = array();
		
		if(!$uid){
			return $goods_data;
		}
		
		$cart_list=Db::name('cart')->where(array('uid'=>$uid,'type'=>$type))->select();
		
		if(!empty($cart_list)){
			
			foreach ($cart_list as $k => $cart) {
				$stock = true;
				$goods=Db::name('goods')->find($cart['goods_id']);
				
				if($goods){
					$option_price = 0;					
					$option_weight = 0;	
					$option_data = array();
										
					foreach ((array)(json_decode($cart['goods_option'])) as $goods_option_id => $option_value) {
						
						$option_id=explode(',', $goods_option_id);
				
						$option_query = Db::query("SELECT go.goods_option_id, go.option_id, o.name, o.type FROM ". config('database.prefix') . "goods_option go LEFT JOIN " . config('database.prefix') . "option o ON (go.option_id = o.option_id) WHERE go.option_id = " . (int)$option_id[1] . " AND go.goods_id = " . (int)$cart['goods_id']);
												 
						 if(!empty($option_query)){
						 	
							if ($option_query[0]['type'] == 'select' || $option_query[0]['type'] == 'radio') {
								$option_value_query = Db::query("SELECT gov.goods_option_id,gov.goods_option_value_id,gov.option_value_id,ov.value_name, gov.quantity, gov.subtract, gov.price, gov.price_prefix,gov.weight, gov.weight_prefix FROM " .
								 config('database.prefix') . "goods_option_value gov LEFT JOIN " .
								  config('database.prefix') . "option_value ov ON (gov.option_value_id = ov.option_value_id) WHERE gov.option_value_id = "
								   . (int)$option_value . " AND gov.option_id = " .(int)$option_id[1]. " AND gov.goods_id = " . (int)$cart['goods_id']);
								
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

									if ($option_value_query[0]['subtract'] && (!$option_value_query[0]['quantity'] || ($option_value_query[0]['quantity'] < $cart['quantity']))) {
										$stock = false;
									}

									$option_data[] = array(
										'goods_option_id'         => $option_value_query[0]['goods_option_id'],
										'goods_option_value_id'   => $option_value_query[0]['goods_option_value_id'],
										'option_id'               => $option_query[0]['option_id'],
										'option_value_id'         => $option_value_query[0]['option_value_id'],
										'name'                    => $option_query[0]['name'],
										'value'            		  => $option_value_query[0]['value_name'],
										'type'                    => $option_query[0]['type'],
										'quantity'                => $option_value_query[0]['quantity'],
										'subtract'                => $option_value_query[0]['subtract'],
										'price'                   => $option_value_query[0]['price'],
										'price_prefix'            => $option_value_query[0]['price_prefix'],														
										'weight'                  => $option_value_query[0]['weight'],										
										'weight_prefix'           => $option_value_query[0]['weight_prefix']
									);								
								}
								
							}elseif ($option_query[0]['type'] == 'checkbox' && is_array($option_value)) {
								foreach ($option_value as $option_value_id) {								

								$option_value_query = Db::query("SELECT gov.goods_option_id,gov.goods_option_value_id,gov.option_value_id, ov.value_name, gov.quantity,
								 gov.subtract, gov.price, gov.price_prefix,gov.weight,
								  gov.weight_prefix FROM " . config('database.prefix') .  "goods_option_value gov LEFT JOIN ". config('database.prefix') 
								  ."option_value ov ON (gov.option_value_id = ov.option_value_id) WHERE gov.option_value_id =" 
								.(int)$option_value_id . " AND gov.option_id = " .(int)$option_id[1]. " AND gov.goods_id = " . (int)$cart['goods_id']);
									

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

										if ($option_value_query[0]['subtract'] && (!$option_value_query[0]['quantity'] || ($option_value_query[0]['quantity'] < $cart['quantity']))) {
											$stock = false;
										}

										$option_data[] = array(
											'goods_option_id'         => $option_value_query[0]['goods_option_id'],
											'goods_option_value_id'   => $option_value_query[0]['goods_option_value_id'],
											'option_id'               => $option_query[0]['option_id'],
											'option_value_id'         => $option_value_query[0]['option_value_id'],
											'name'                    => $option_query[0]['name'],
											'value'            		  => $option_value_query[0]['value_name'],
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

					$price = $goods['price'];						
					
					$discount=Db::query("SELECT price FROM " . config('database.prefix') . "goods_discount WHERE goods_id = '" . (int)$cart['goods_id'] . "' AND quantity <=" . (int)$cart['quantity'] . " ORDER BY quantity DESC, price ASC LIMIT 1");
		
					if($discount){
						$price=$discount[0]['price'];
					}
					
					$goods_data[] = array(
						'cart_id'                   => $cart['cart_id'],			
						'goods_id'                  => $goods['goods_id'],
						'name'                      => $goods['name'],
						'model'                     => $goods['model'],
						'shipping'                  => $goods['shipping'],						
						'image'                     => resize($goods['image'],80,80),
						'quantity'                  => $cart['quantity'],
						'minimum'                   => $goods['minimum'],
						'subtract'                  => $goods['subtract'],						
						'price'                     => $price+$option_price,						
						'total'                     => ($price+$option_price) * $cart['quantity'],
						'pay_points'                => ($goods['pay_points']),
						'total_pay_points'          => ($goods['pay_points']) * $cart['quantity'],						
						'total_return_points'       => ($goods['points']) * $cart['quantity'],						
						'weight'          			=> ($goods['weight'] + $option_weight) * $cart['quantity'],
						'weight_class_id'           => $goods['weight_class_id'],
						'length'                    => $goods['length'],
						'width'                     => $goods['width'],
						'height'                    => $goods['height'],
						'length_class_id'           => $goods['length_class_id'],
						'stock'                     => $stock,
						'option'                    => $option_data,				
					);


				}else {
					$this->remove((int)$cart['cart_id'],$uid);
				}				
			}			
		}
		return $goods_data;
	}

	
	/**
	 * 加入购物车
	 *@param uid 	       用户id
	 *@param goods_id  商品id
	 *@param quantity  商品数量 
	 *@param option    商品选项 
	 */
	public function add($data=array()){
		
		if(empty($data)){
			return false;
		}
		
		$cart['uid']=$data['uid'];
		$cart['goods_id']=(int)$data['goods_id'];
		$cart['quantity']=(int)$data['quantity'];
		
		if(isset($data['type'])){
			$cart['type']=$data['type'];
		}

		$cart['goods_option']= isset($data['option'])?json_encode(array_filter($data['option'])):'';
		
		$cart['create_time']=time();
	
		//判断是否有同一个商品
		if(Db::name('cart')->where(array('uid'=>$cart['uid'],'goods_id'=>$cart['goods_id'],'goods_option'=>$cart['goods_option']))->find()){
			Db::name('cart')->where('goods_id',$cart['goods_id'])->setInc('quantity',$cart['quantity']);
			return true;
		}else{
			if($cart_id=Db::name('cart')->insert($cart,false,true)){
				return true;
			}else{
				return false;
			}
		}
	}
	//购物车中删除
	public function remove($cart_id,$uid) {
		$map['cart_id']=['eq',$cart_id];
		$map['uid']=['eq',$uid];
		Db::name('cart')->where($map)->delete();
	}
	
	//计算购物车商品数量
	public function count_cart_total($uid,$type='money'){
		if($total=Db::name('cart')->where(array('uid'=>$uid,'type'=>$type))->sum('quantity')){
			return $total;
		}
		return 0;
	}
	
	
	
	/**
	 * 判断商品是否存在，并验证最小起订量
	 *@param goods_id  商品id
	 *@param quantity  商品数量 
	 */
	public function check_minimum($param=array()){
		
		if(empty($param)){
			return false;
		}
		
		if($goods=Db::name('goods')->find((int)$param['goods_id'])){			
			if((int)$param['quantity']<$goods['minimum']){
   				return ['error'=>'最小起订量是'.$goods['minimum'],'minimum'=>$goods['minimum']];
   			} 			
		}else{
			return ['error'=>'商品不存在'];
		}
	}
	
	//得到商品数量
	public function get_goods_quantity($goods_id){		
		return Db::name('goods')->where('goods_id',$goods_id)->field('quantity')->find();		
	}
	
	/**
	 * 验证商品数量和必选项
	 *@param $param['goods_id']
	 *@param $param['quantity']
	 *@param $param['option']
	 */
	public function check_quantity($param=array()){		
		
		$goods_id=(int)$param['goods_id'];
		$quantity=(int)$param['quantity'];
		
		if (!isset($param['option'])) {		
			$param['option'] =[];	
		}
				
		$option=$this->get_goods_option_info($goods_id);	

		foreach ($option as $key=> $product_option) {			
			if ($product_option['required'] && empty($param['option'][$key])) {					
				return	['error'=> $product_option['name'].'是必选项','goods_option_id'=>$product_option['goods_option_id']];
			}			
		}		
		//存在选项的
		if(!empty($param['option'])){												
			foreach ($param['option'] as $k=>$v) {				
				if(is_array($v)){					
					foreach ($v as $k1 => $v1) {
						//需要扣减库存的要验证数量
						if($quantity>$option[$k][$v1]['quantity']&&($option[$k][$v1]['subtract']==1)){
							return ['error'=>$option[$k][$v1]['name'].'数量不足，剩余'.$option[$k][$v1]['quantity']];
						}
					}					
				}else{
					//需要扣减库存的要验证数量
					if($quantity>$option[$k][$v]['quantity']&&($option[$k][$v]['subtract']==1)){
					return ['error'=>$option[$k][$v]['name'].'数量不足，剩余'.$option[$k][$v]['quantity']];
					}
				}				
			}	
		}else{
			//不存在选项的			
			$goods=Db::name('goods')->where('goods_id',$goods_id)->find();
			
			$goods_quantity=$this->get_goods_quantity($goods_id);
			if($quantity>$goods_quantity['quantity']&&($goods['subtract']==1)){
				return ['error'=>'数量不足，剩余'.$goods_quantity['quantity']];
			}
		}				
	
	}
	
	public function get_goods_option_info($goods_id){
		
		$option=osc_goods()->get_goods_options($goods_id);		
		$goods_option=[];	

		foreach ($option as $k => $v) {				
			foreach ($v['goods_option_value'] as $k1 => $v1) {
				$goods_option[$goods_id.','.$v['option_id']]['required']=$v['required'];
				$goods_option[$goods_id.','.$v['option_id']]['name']=$v['name'];				
				$goods_option[$goods_id.','.$v['option_id']]['goods_option_id']=$v['goods_option_id'];	
				$goods_option[$goods_id.','.$v['option_id']][$v1['option_value_id']]=$v1;
				$goods_option[$goods_id.','.$v['option_id']][$v1['option_value_id']]['type']=$v['type'];			
			}
		}
		return $goods_option;
	}
	//更新购物车商品数量
	public function update($cart_id,$quantity,$uid) {
		Db::execute("UPDATE " . config('database.prefix') . "cart SET quantity = '" . (int)$quantity . "' WHERE cart_id = '" . (int)$cart_id . "' AND uid =" .$uid);
	} 
	//取得商品重量
	public function get_weight($uid) {
		$weight = 0;
		foreach ($this->get_all_goods($uid) as $product) {
			
			if ($product['shipping']) {
				$weight += osc_weight()->convert($product['weight'], $product['weight_class_id'],config('weight_id'));
			}
		}

		return $weight;
	}
	//更新商品数量
	public function update_goods_quantity($goods_id,$cart_id,$quantity,$uid){
			
			$this->update($cart_id,$quantity,$uid);
			
			$goods_list=$this->get_all_goods($uid);				
		
			$total=0;
			$total_all_price=0;
			$weight = 0;
			
			foreach ($goods_list as $k => $v) {					
				
				if($v['goods_id']==$goods_id){
					$price=$v['price'];
					$total_price=$v['total'];
				}
				
				$total+=$v['quantity'];
				$total_all_price+=$v['total'];
				if ($v['shipping']) {
					$weight += osc_weight()->convert($v['weight'], $v['weight_class_id'],config('weight_id'));
				}
			}
			
			return [
				'total_quantity'=>$total,//商品总数
				'goods_price'=>$price,//商品单价
				'goods_total_price'=>$total_price,//单个商品总金额
				'total_all_price'=>$total_all_price,//所有商品金额
				'weight'=>$weight
			];
			
	}
	//是否需要派送,下载类/虚拟商品不需要配送
	public function has_shipping($uid,$type='money') {
		
		if(!$uid){
			return;
		}
		
		$shipping=0;//需要配送
		$no_shipping=0;//不需要配送		
		$no_shipping_goods='';
		
		foreach ($this->get_all_goods($uid,$type) as $product) {
			if ($product['shipping']) {
				$shipping += 1;
			}else{
				$no_shipping_goods.=$product['name'].'，';
				$no_shipping+=1;
			}
		}
		
		if($shipping>0&&$no_shipping==0){
			return true;
		}elseif($shipping==0&&$no_shipping>0){
			return false;
		}elseif($shipping>0&&$no_shipping>0){
			return ['error'=>$no_shipping_goods.'是不需要配送的商品请单独结算下单'];
		}		
	}

	//取得积分商品,所需的总积分
	public function get_pay_points($uid,$type){
		$points=0;
		foreach ($this->get_all_goods($uid,$type) as $product) {
			$points+=$product['total_pay_points'];
		}
		
		return $points;
	}

}
?>