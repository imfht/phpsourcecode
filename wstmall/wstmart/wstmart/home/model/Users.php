<?php
namespace wstmart\home\model;
use wstmart\common\model\Users as CUsers;
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
 * 用户类
 */
use think\Db;
class Users extends CUsers{
	/**
	* 获取各订单状态数、未读消息数、账户安全等级
	*/ 
	function getStatusNum(){
		$userId = (int)session('WST_USER.userId');
		$data = [];
		// 用户消息
	    $data['message'] = Db::name('messages')->where(['receiveUserId'=>$userId,'msgStatus'=>0,'dataFlag'=>1])->count();
		//获取用户订单状态
	    $data['waitPay'] = Db::name('orders')->where(['userId'=>$userId,'orderStatus'=>-2,'dataFlag'=>1])->count();
	    $data['waitReceive'] = Db::name('orders')->where([['orderStatus','in',[0,1]],['userId','=',$userId],['dataFlag','=',1]])->count();
	    $data['received'] = Db::name('orders')->where(['userId'=>$userId,'orderStatus'=>2,'dataFlag'=>1])->count();
	    $data['waitAppr'] = Db::name('orders')->where(['userId'=>$userId,'orderStatus'=>2,'isAppraise'=>0,'dataFlag'=>1])->count();
	    // 账户安全等级
	    $level = 1;
	    $users = $this->where(['userId'=>$userId])->field('userPhone,userEmail')->find();
	    if(!empty($users['userPhone']))++$level;
	    if(!empty($users['userEmail']))++$level;
	    $data['level'] = $level;
	    //关注商品
	    $data['gfavorite'] = Db::name('favorites')->where(['userId'=>$userId,'favoriteType'=>0])->count();
	    return $data;
	}
}
