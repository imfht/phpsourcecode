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
 
namespace osc\member\controller;
use osc\common\controller\HomeBase;
use think\Db;
class Cart extends HomeBase
{
	
		
    public function index()
    {		
		$cart=osc_cart();
		
		$goods=$cart->get_all_goods(member('uid'));		
	
		$total_all_price=0;
		
		$weight = 0;
		
		if(isset($goods)){
			foreach ($goods as $k => $v) {
	
				$total_all_price+=$v['total'];
				if ($v['shipping']) {
					$weight += osc_weight()->convert($v['weight'], $v['weight_class_id'],config('weight_id'));
				}
			}
		}
		
		$this->assign('goods',$goods);
		
		$this->assign('total_price',$total_all_price);
		
		$this->assign('weight',$weight);
		
		$this->assign('SEO',['title'=>'购物车-'.config('SITE_TITLE'),'keywords'=>config('SITE_KEYWORDS'),'description'=>config('SITE_DESCRIPTION')]);
		
		return $this->fetch();
   
    }
	//加入购物车
	public function add()
    {    
		
		if(!$uid=osc_service('member','user')->is_login()){
			return ['error'=>'请先登录！！'];
		}
		
		$cart=osc_cart();
		
		$param=input('post.');
		
   		//判断商品是否存在，并验证最小起订量   		
   		if($error=$cart->check_minimum($param)){   			
   			return $error;			
   		}		
		//验证商品数量和必选项
		if($error=$cart->check_quantity($param)){			
			return $error;
		}	
		$param['uid']=$uid;
		//加入购物车	
		if($cart->add($param)){
			//计算购物车商品数量
			$total=$cart->count_cart_total($uid);
			//设置session中购物车商品数量
			osc_service('member','user')->set_cart_total($total);
			storage_user_action(member('uid'),member('username'),config('FRONTEND_USER'),'加入商品到购物车');
			return ['success'=>'加入成功！！','total'=>$total];
		}else{
			return ['error'=>'加入失败！！'];
		}
	
    }
	//更新购物车
	public function update(){
		
		if(!$uid=osc_service('member','user')->is_login()){
			return ['error'=>'请先登录！！'];
		}
		
		$d=input('post.');
		
		$cart=osc_cart();

		$goods_id=(int)$d['id'];	
		
		$quantity=(int)$d['q'];
		
		$cart_id=(int)$d['cart_id'];
		
		$cart_data=Db::name('cart')->find($cart_id);			
		$param['option']=json_decode($cart_data['goods_option'], true);
		$param['goods_id']=$goods_id;
		$param['quantity']=$quantity;	
		
		//判断商品是否存在，验证最小起订量   		
   		if($error=$cart->check_minimum($param)){   			
   			return $error;			
   		}			
				
		//验证商品数量和必选项
		if($error=$cart->check_quantity($param)){			
			return $error;
		}
		//更新购物车商品数量		
		$return=$cart->update_goods_quantity($goods_id,$cart_id,$quantity,$uid);
		
		//更新 购物车( 0 ) 的商品数量			
		osc_service('member','user')->set_cart_total($return['total_quantity']);
		
		storage_user_action(member('uid'),member('username'),config('FRONTEND_USER'),'更新了购物车商品');
		
		$json['success']=$return['total_quantity'];			
		//商品单价
		$json['price']=$return['goods_price'];
		//单个商品总价
		$json['total_price']=$return['goods_total_price'];
		//所有商品总价
		$json['total_all_price']=$return['total_all_price'];
		//所有商品重量
		$json['weight']=$return['weight'];
			
		return $json;
		
		
	}
	
	public function remove(){
		
		if(!osc_service('member','user')->is_login()){
			return ['error'=>'请先登录！！'];
		}
		
		$cart=osc_cart();
		
		$cart->remove((int)input('param.id'),member('uid'));			
		
		$total=$cart->count_cart_total(member('uid'));
		
		osc_service('member','user')->set_cart_total($total);		
				
		storage_user_action(member('uid'),member('username'),config('FRONTEND_USER'),'删除了购物车商品');
				
		$this->redirect('/cart');	
	}
	
}
