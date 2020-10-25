<?php
namespace wstmart\store\model;
/**
 * ============================================================================
 * WSTMart多用户商城
 * 版权所有 2016-2066 广州商淘信息科技有限公司，并保留所有权利。
 * 官网地址:http://www.wstmart.net
 * 交流社区:http://bbs.shangtao.net
 * 联系QQ:153289970
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 某些较杂业务处理类
 */
use think\Db;
class Systems extends Base{
	/**
	 * 获取定时任务
	 */
	public function getSysMessages(){
		$tasks = strtolower(input('post.tasks'));
		$tasks = explode(',',$tasks);
		$userId = (int)session('WST_STORE.userId');
		$shopId = (int)session('WST_STORE.shopId');
		$storeId = (int)session('WST_STORE.storeId');
		$data = [];
		if(in_array('message',$tasks)){
		    //获取用户未读消息
		    $data['message']['num'] = Db::name('messages')->where(['receiveUserId'=>$userId,'msgStatus'=>0,'dataFlag'=>1])->count();
		    $data['message']['sid'] = 383;
		}
		if($storeId>0){
			//待发货
			if(in_array('storeorder371',$tasks)){
			    $data['storeorder']['371'] = Db::name('orders')->where(['shopId'=>$shopId,'storeId'=>$storeId,'storeType'=>1,'orderStatus'=>0,'dataFlag'=>1])->count();
			}
			//待付款
			if(in_array('storeorder370',$tasks)){
			    $data['storeorder']['370'] = Db::name('orders')->where(['shopId'=>$shopId,'storeId'=>$storeId,'storeType'=>1,'orderStatus'=>-2,'dataFlag'=>1])->count();
			}
			
		}
		
		return $data;
	}
}
