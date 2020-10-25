<?php 
namespace wstmart\admin\model;
use think\Db;
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
 * 系统业务处理
 */
class Index extends Base{
	/**
	 * 获取基础统计信息
	 */
	public function summary(){
		$data = [];
		$today = date('Y-m-d');
		//销量
		$data['sale']['today'] = Db::name('orders')->where([['orderStatus','<>',-1],['dataFlag','=',1],['createTime','like',$today.'%']])->sum('totalMoney');
		//订单
		$data['order']['today'] = Db::name('orders')->where([['orderStatus','<>',-1],['dataFlag','=',1],['createTime','like',$today.'%']])->count();
		//店铺
		$data['shop']['today'] = Db::name('shops')->where([['dataFlag','=',1],['applyTime','like',$today.'%'],['applyStatus','=',2]])->count();
		//会员
		$data['user']['today'] = Db::name('users')->where([['userType','=',0],['createTime','like',$today.'%'],['dataFlag','=',1]])->count();
		//退款
		$data['refund']['today'] = Db::name('order_refunds')->where([['createTime','like',$today.'%']])->count();
        //昨天的信息
        $yesterday = date("Y-m-d",strtotime("-1 day"));
        $ydata = cache('WST_ADMIN_MAIN'.$yesterday);
        if(!$ydata){
        	$ydata = [];
        	$ydata['sale'] = Db::name('orders')->where([['orderStatus','<>',-1],['dataFlag','=',1],['createTime','like',$yesterday.'%']])->sum('totalMoney');
            $ydata['order'] = Db::name('orders')->where([['orderStatus','<>',-1],['dataFlag','=',1],['createTime','like',$yesterday.'%']])->count();
            $ydata['shop'] = Db::name('shops')->where([['dataFlag','=',1],['applyTime','like',$yesterday.'%'],['applyStatus','=',2]])->count();
            $ydata['user'] = Db::name('users')->where([['userType','=',0],['createTime','like',$yesterday.'%'],['dataFlag','=',1]])->count();
            $ydata['refund'] = Db::name('order_refunds')->where([['createTime','like',$today.'%']])->count();
            cache('WST_ADMIN_MAIN'.$yesterday,$ydata,86400);
        }
        $data['sale']['yesterday'] = $ydata['sale'];
        $data['order']['yesterday'] = $ydata['order'];
        $data['shop']['yesterday'] = $ydata['shop'];
        $data['user']['yesterday'] = $ydata['user'];
        $data['refund']['yesterday'] = $ydata['refund'];
        //店铺审核
        $shopAudit1 = Db::name('shops')->where([['dataFlag','=',1],['applyStatus','=',1]])->count();
        $shopAudit2 = Db::name('shop_applys')->where([['dataFlag','=',1],['applyStatus','=',0]])->count();
        $data['tips']['shopAudit'] = $shopAudit1 + $shopAudit2;

        //商品审核
        $data['tips']['goodsAudit'] = Db::name('goods')->where([['dataFlag','=',1],['goodsStatus','=',0],['isSale','=',1],['createTime','like',date('Y-m-d').'%']])->count();
        //提现申请
        $data['tips']['cashDraw'] = Db::name('cash_draws')->where([['cashSatus','=',0]])->count();
        //退款申请
        $data['tips']['refund'] = Db::name('order_refunds')->where([['refundStatus','=',1]])->count();
        //订单投诉
        $data['tips']['complains'] = Db::name('order_complains')->where([['complainTime','like',date('Y-m-d').'%'],['complainStatus','in',[0,3]]])->count();
        //商品举报
        $data['tips']['informs'] = Db::name('informs')->where([['informStatus','=',0],['dataFlag','=',1]])->count();
		$rs = Db::query('select VERSION() as sqlversion');
		$data['MySQL_Version'] = $rs[0]['sqlversion'];
		$data['time']['startDate'] = date('Y-m-d',strtotime("-1month"));
        $data['time']['endDate'] = date('Y-m-d');
		return $data;
	}
	
    /**
	 * 保存授权码
	 */
	public function saveLicense(){
		$data = [];
		$data['fieldValue'] = input('license');
	    $result = model('SysConfigs')->where('fieldCode','mallLicense')->update($data);
		if(false !== $result){
			cache('WST_CONF',null);
			return WSTReturn("操作成功",1);
		}
		return WSTReturn("操作失败");
	}

	/**
	 * 获取系统消息
	 */
	public function getSysMessages(){
		$tasks = strtolower(input('post.tasks'));
		$tasks = explode(',',$tasks);
		$data = [];
		if(in_array('shopapply',$tasks)){
			$data['45'] = Db::name('shops')->where([['dataFlag','=',1],['applyStatus','=',1]])->count();
		}
		if(in_array('goodsaudit',$tasks)){
			$data['54'] = Db::name('goods')->where([['dataFlag','=',1],['goodsStatus','=',0],['isSale','=',1]])->count();
		}
		if(in_array('ordercomplains',$tasks)){
			$data['51'] = Db::name('order_complains')->where([['complainStatus','in',[0,3]]])->count();
		}
		if(in_array('informs',$tasks)){
			$data['188'] = Db::name('informs')->where([['dataFlag','=',1],['informStatus','=',0]])->count();
		}
		if(in_array('groupons',$tasks)){
			$data['315'] = Db::name('groupons')->where([['dataFlag','=',1],['grouponStatus','=',0]])->count();
		}
		if(in_array('auctions',$tasks)){
			$data['306'] = Db::name('auctions')->where([['dataFlag','=',1],['auctionStatus','=',0]])->count();
		}
		if(in_array('pintuans',$tasks)){
			$data['316'] = Db::name('pintuans')->where([['dataFlag','=',1],['tuanStatus','=',0]])->count();
		}
		return $data;
	}
}