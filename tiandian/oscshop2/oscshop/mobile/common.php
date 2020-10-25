<?php
use \wechat\OscshopWechat;
use \think\Db;

if (!function_exists('wechat')) {
    /**
     * 助手函数
     */
    function wechat()
    {
    	return OscshopWechat::getInstance([
    	'appid'=>config('appid'),
    	'appsecret'=>config('appsecret'),
    	'token'=>config('token'),
    	'encodingaeskey'=>config('encodingaeskey')]);		
    }
}

//是否在微信中
function in_wechat() {
    return strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false;
}
//取得登录用户信息
function user($key){
	
	$member=cookie('mobile_user_info');
	
	if(empty($member)){
		return null;
	}
	
	if(!isset($member[$key])&&$member['uid']){
		
		$user=Db::name('member')->where('uid',$member['uid'])->find();
	
		if(isset($user[$key])){
			return $user[$key];
		}
		
		return null;		
	}
	
	return $member[$key];
	
}
//通过地区的名称取地区的id
function get_area_id_by_name($area_name){
	
	if (!$area_list = cache('area_name_list')) {
		
		$list=Db::name('area')->field('area_id,area_name')->select();
		
		foreach ($list as $k => $v) {
			$area[$v['area_name']]=$v;
		}
		cache('area_name_list', $area);	
		
		$area_list=$area;
	}
	return $area_list[$area_name]['area_id'];
	
}
//小数转百分比
function num_to_percent($n)
{	
	return ($n*100)."%";
} 
//取代理级别信息
function get_agent_level_info($al_id,$key)
{	
	if (!$agent_level = cache('agent_level')) {
		
		$list=Db::name('agent_level')->select();
		
		foreach ($list as $k => $v) {
			$agent[$v['al_id']]=$v;
		}
		cache('agent_level', $agent);	
		$agent_level=$agent;
	}
	if(isset($agent_level[$al_id][$key])){
		return $agent_level[$al_id][$key];
	}
	return null;	
} 
//取得用户信息
function get_member_info($uid,$key){
	$user=Db::name('member')->where('uid',$uid)->find();
	
	if(!$uid){
		return null;
	}
	return $user[$key];
}

function deal_agent_share(){
	
	if(in_wechat()){	
		//代理商的ID		
		$pid=hashids_decode(input('param.osc_aid'));
		$uid=(int)user('uid');
		if($pid&&$uid){				
			$mem=Db::name('member')->where('uid',$uid)->find();		
			if($mem['pid']==0){
				Db::name('member')->where('uid',$uid)->update(['pid'=>$pid]);
			}
		}
	}
}

?>
