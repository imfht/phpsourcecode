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
 
namespace osc\mobile\controller;
use osc\mobile\validate\Address;
use \think\Db;
class Cart extends MobileBase{

	function index(){
		
		$uid=null;
				
		$uid=osc_service('mobile','user')->is_login();	
		
		if(in_wechat()){
			$wechat=wechat();
			//调用微信收货地址接口，需要开通微信支付
			$this->assign('signPackage',$wechat->getJsSign(request()->url(true)));	
			session('jssdk_order',null);
		}
		
		$cart=osc_cart();
		if('points'==input('param.type')){
			$goods=$cart->get_all_goods($uid,'points');	
			$shipping=$cart->has_shipping($uid,'points');
		}else{
			$goods=$cart->get_all_goods($uid);	
			$shipping=$cart->has_shipping($uid);
		}
	
		$total_all_price=0;
		
		$total_point=0;
		
		$weight = 0;
		
		if(!empty($goods)){
			foreach ($goods as $k => $v) {
				$total_point+=$v['total_pay_points'];
				$total_all_price+=$v['total'];
				if ($v['shipping']) {
					$weight += osc_weight()->convert($v['weight'], $v['weight_class_id'],config('weight_id'));
				}
			}				
		}
		
		if(isset($shipping['error'])){
				
			$this->assign('error',$shipping['error']);	
			
			$this->assign('shipping','true');
		}else{			
			$this->assign('shipping',$shipping);
		}
		
		if($shipping){//需要配送
		
			$address_id=(int)user('address_id');
			//配送地址		
			$address=Db::name('address')->where('address_id',$address_id)->find();				
			//计算运费
			if(isset($address)){			
			
				$this->assign('address',$address);
					
				$t=osc_transport()->calc_transport(config('default_transport_id'),$weight,$address['city_id']);
				
				$transport=$t['price'];
				
			}else{
				$transport=0;
			}		
			//手机版中
			if(!in_wechat()){
				$this->assign('all_address',Db::name('address')->where('uid',(int)$uid)->select());
			}
			$this->assign('transport',$transport);	
		}

		$this->assign('goods',$goods);		
		$this->assign('total_price',$total_all_price);		
		$this->assign('weight',$weight);		
		
		if('points'==input('param.type')){//积分购物车			
			$this->assign('total_point',$total_point);
			$this->assign('points',true);	
		}
		
		$this->assign('SEO',['title'=>'购物车-'.config('SITE_TITLE')]);
		$this->assign('top_title','购物车');
		 return $this->fetch();
	}
	
	function add(){
				
		$uid=osc_service('mobile','user')->is_login();	
		
		if(!$uid){
			return ['error'=>'请先登录','url'=>url('login/login')];
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
		
		if(isset($param['pay_type'])){
			$param['type']='points';
		}
		
		//加入购物车	
		if($cart->add($param)){
			//计算购物车商品数量
			$total=$cart->count_cart_total($uid);
			//设置session中购物车商品数量
			osc_service('mobile','user')->set_cart_total($total);
			
			storage_user_action($uid,user('username'),config('FRONTEND_USER'),'加入商品到购物车');
			return ['success'=>'加入成功','total'=>$total];
		}else{
			return ['error'=>'加入失败'];
		}
	}

	//更新购物车
	public function update(){
				
		$uid=osc_service('mobile','user')->is_login();	
		
		if(!$uid){
			return ['error'=>'请先登录','url'=>url('login/login')];
		}
		
		$d=input('post.');
		
		$cart=osc_cart();

		$goods_id=(int)$d['id'];	
		
		$quantity=(int)$d['q'];
		
		$cart_id=(int)$d['cart_id'];
		
		$city_id=(int)$d['city_id'];
		
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
		
		//更新 购物车的商品数量			
		osc_service('mobile','user')->set_cart_total($return['total_quantity']);	
		
		storage_user_action(user('uid'),user('nickname'),config('FRONTEND_USER'),'更新了购物车商品');
		
		//计算运费
		if($city_id){			
			$t=$t=osc_transport()->calc_transport(config('default_transport_id'),$return['weight'],$city_id);
			$transport=$t['price'];
		}else{
			$transport=0;
		}	
	
		return [
			//运费
			'transport'=>$transport,
			//数量
			'success'=>$return['total_quantity'],
			//商品单价
			'price'=>$return['goods_price'],
			//所有商品总价
			'total_all_price'=>$return['total_all_price'],
			//所有商品重量
			'weight'=>$return['weight'],
		];
		
		
	}
	
	public function remove(){
				
		$uid=osc_service('mobile','user')->is_login();	
		
		if(!$uid){
			return ['error'=>'请先登录','url'=>url('login/login')];
		}
		
		$cart=osc_cart();
		
		$cart->remove((int)input('param.cart_id'),$uid);	
				
		storage_user_action(user('uid'),user('nickname'),config('FRONTEND_USER'),'删除了购物车商品');
		
		if(('points'!=input('param.type'))){
			$goods=$cart->get_all_goods($uid);	
			$total=$cart->count_cart_total($uid);
		}else{
			$goods=$cart->get_all_goods($uid,'points');	
			$total=$cart->count_cart_total($uid,'points');
		}			
		
		osc_service('mobile','user')->set_cart_total($total);	
		
		$total_all_price=0;
		
		$total_point=0;
		
		$weight = 0;
		
		if(!empty($goods)){
			foreach ($goods as $k => $v) {
				$total_point+=$v['total_pay_points'];
				$total_all_price+=$v['total'];
				if ($v['shipping']) {
					$weight += osc_weight()->convert($v['weight'], $v['weight_class_id'],config('weight_id'));
				}
			}				
		}		
		
		$city_id=(int)input('param.city_id');
		
		if($city_id){			
			$t=osc_transport()->calc_transport(config('default_transport_id'),$weight,$city_id);
			$transport=$t['price'];
		}else{
			$transport=0;
		}	
			
		return [
			//运费
			'transport'=>$transport,
			//数量
			'total_num'=>$total,
			//商品单价
			'total_pay_points'=>$total_point,
			//所有商品总价
			'total_all_price'=>$total_all_price,
			//所有商品重量
			'weight'=>$weight,
		];	

	}
	
	//更新地址，计算运费(积分兑换的不需要计算运费)
	function update_address(){
		if(request()->isPost()){
			$data=input('post.');					
				 	
			$validate=new Address();
				
			if(!$validate->check($data)){				
			    return ['error'=>$validate->getError()];				
			}
			
			$province_id=get_area_id_by_name($data['province']);
			
			$city_id=get_area_id_by_name($data['city_id']);
			
			$country_id=get_area_id_by_name($data['country_id']);
			
			$weight=$data['weight'];
			
			$address['uid']=user('uid');
			$address['name']=$data['name'];
			$address['telephone']=$data['tel'];		
			$address['address']=$data['address'];
			
			$address['province_id']=$province_id;
			$address['city_id']=$city_id;
			$address['country_id']=$country_id;
			
			if($data['type']=='add'){
				$r=Db::name('address')->insert($address,false,true);
				$address_id=$r;
			}elseif($data['type']=='edit'){
				$r=Db::name('address')->where('address_id',(int)$data['address_id'])->update($address,false,true);
				$address_id=(int)$data['address_id'];
			}		
			storage_user_action(user('uid'),user('nickname'),config('FRONTEND_USER'),'更新了收货地址');
			
			Db::name('member')->where('uid',user('uid'))->update(['address_id'=>$address_id]);
			
			if($r){
				$transport=osc_transport()->calc_transport(config('default_transport_id'), $weight,$city_id);
				return ['success'=>'1','transport'=>$transport,'city_id'=>$city_id,'address_id'=>$address_id];
			}else{
				return ['error'=>'编辑失败'];
			}
		}
	}
	
	function get_address_list(){
		
		$list=Db::name('address')->where('uid',user('uid'))->select();
		
		if($list){
			return ['list'=>$list];
		}else{
			return null;
		}
		
	}
	//选择收货地址后，计算运费,手机版
	function update_transport(){
		if(request()->isPost()){
			$weight=input('post.weight');
			$city_id=input('post.city_id');
			
			Db::name('member')->where('uid',user('uid'))->update(['address_id'=>(int)input('post.address_id')]);
			
			$transport=osc_transport()->calc_transport(config('default_transport_id'),$weight,$city_id);
			if($transport){
				return ['transport'=>$transport];
			}else{
				return ['error'=>'运费计算失败，请设置一个默认的运费模板'];
			}
		}
		
	}
	
	//选择收货地址后，计算运费,微信版，(地址名称和数据库表area数据不同有可能找不到数据)
	function weixin_update_transport(){
		
		$data=input('post.');
		
		$province_id=get_area_id_by_name($data['province']);
		
		$city_id=get_area_id_by_name($data['area_id']);
		
		$country_id=get_area_id_by_name($data['country']);		
		
		$uid=(int)user('uid');
		
		$address['uid']=$uid;
		$address['name']=$data['name'];
		$address['telephone']=$data['tel'];		
		$address['address']=$data['address'];
		
		$address['province_id']=$province_id;
		$address['city_id']=$city_id;
		$address['country_id']=$country_id;
		
		Db::name('address')->where('uid',$uid)->delete();
		
		$address_id=Db::name('address')->insert($address,false,true);
		
		storage_user_action($uid,user('nickname'),config('FRONTEND_USER'),'更新了收货地址');
		
		Db::name('member')->where('uid',$uid)->update(['address_id'=>$address_id]);		
		
		$transport=osc_transport()->calc_transport(config('default_transport_id'),$data['weight'],$city_id);		
		
		return ['transport'=>$transport,'address_id'=>$address_id,'city_id'=>$city_id];
	}
	
	function get_address(){
		
		$address_id=(int)input('param.aid');
		if(!$address_id){
			return ['error'=>'操作失败'];
		}
		$address=Db::name('address')->where('address_id',$address_id)->find();
		
		return ['success'=>'1',
		'address'=>$address,
		'area'=>get_area_name($address['province_id']).' '.get_area_name($address['city_id']).' '.get_area_name($address['country_id'])];		
	}
	
}
