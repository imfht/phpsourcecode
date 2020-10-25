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
namespace osc\member\service;
use think\Db;
//用户数据
class User{
	
	function is_login(){
				
		$user=('session'==config('member_login_type'))?session('member_user_auth'):cookie('member_user_auth');
		$user_auth_sign=('session'==config('member_login_type'))?session('member_user_auth_sign'):cookie('member_user_auth_sign');		

	    if (empty($user)) {
	        return 0;
	    } else {
	        return $user_auth_sign == data_auth_sign($user) ? $user['uid'] : 0;
	    }
		
	}
	
	function logout(){
		
		if('session'==config('member_login_type')){
			session('member_user_auth',null);			
		}elseif('cookie'==config('member_login_type')){
			cookie('member_user_auth', null);			
		}
		session('total',null);
	}	
	
	function user_info($uid=UID){		
		return Db::name('member')->where('uid',$uid)->find();		
	}	
	
	function user_group(){
		return Db::name('member_auth_group')->field('id,title')->where('status',1)->select();
	}
	

	//设置购物车商品数量
	function set_cart_total($total){
		session('total',$total);
	}
	//取得会员默认收货地址
	function get_address_id($uid){
		
		$aid=Db::name('member')->field('address_id')->where('uid',$uid)->find();
		
		return $aid['address_id'];
	}
	//取得会员所有收货地址
	function get_address($uid) {
		
		if(!isset($uid)){
			return false;
		}
		
		$area_id=Db::query("SELECT DISTINCT province_id,city_id,country_id FROM ".config('database.prefix')."address WHERE uid=".$uid);
		
		foreach ($area_id as $k => $v) {
			foreach ($v as $key => $value) {
				$area[]=$value;
			}
		}
		
		if(!isset($area)){
			return;
		}
	
		//地区的id,去除重复的
		$arr=array_unique($area);
		$aid=implode(',',$arr);

		//地区的名字
		$area_name=Db::query("SELECT area_name,area_id FROM ".config('database.prefix')."area WHERE area_id IN (".$aid.")");
	
		//取得会员的所有地址
		$address=Db::name('address')->where('uid',$uid)->select();
		
		foreach ($address as $key => $v) {
			$a[$v['address_id']]=$v;
		}
	
		foreach ($a as $k => $v) {
			
			foreach ($area_name as $key => $value) {
				if($v['province_id']==$value['area_id']){
					$a[$k]['province']=$value['area_name'];
				}
				if($v['city_id']==$value['area_id']){
					$a[$k]['city']=$value['area_name'];
				}
				if($v['country_id']==$value['area_id']){
					$a[$k]['country']=$value['area_name'];
				}
			}
			
		}
		return $a;		
	} 
	//新增收货地址
	function add_address($data){
		//写入地址表
		$address['uid']=member('uid');
					
		$address['name']=$data['name'];
		$address['telephone']=$data['telephone'];
		
		$address['address']=$data['address'];	
		
		$address['city_id']=$data['city_id'];
		$address['country_id']=$data['country_id'];
		$address['province_id']=$data['province_id'];
		
		$address_id=Db::name('address')->insert($address,false,true);		
		//会员表更新地址
		if($address_id){
			$member['address_id']=$address_id;
			$member['uid']=member('uid');
			Db::name('member')->update($member);
		}	
		storage_user_action(member('uid'),member('username'),config('FRONTEND_USER'),'新增了收货地址');
		return $address_id;
	}
}
?>