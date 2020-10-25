<?php
namespace wstmart\admin\controller;
use wstmart\admin\model\CronJobs as M;
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
 * 定时任务控制器
 */
class Cronjobs extends Base{
	/**
	 * 处理售后单
	 */
	public function autoDealOrderService(){
		$rs = model('common/OrderServices')->crontab();
		if (WSTConf('CONF.isOpenSupplier')==1){
			$rs = model('common/SupplierOrderServices')->crontab();
		}
		return json($rs);
	}
	/**
	 * 取消未付款订单
	 */
	public function autoCancelNoPay(){
		$m = new M();
        $rs = $m->autoCancelNoPay();
        if (WSTConf('CONF.isOpenSupplier')==1){
			$rs = model('SupplierCronJobs')->autoCancelNoPay();
		}
        return json($rs);
	}
	/**
	 * 自动好评
	 */
	public function autoAppraise(){
        $m = new M();
        $rs = $m->autoAppraise();
        if (WSTConf('CONF.isOpenSupplier')==1){
			$rs = model('SupplierCronJobs')->autoAppraise();
		}
        return json($rs);
	}
	/**
	 * 自动确认收货
	 */
	public function autoReceive(){
	 	$m = new M();
        $rs = $m->autoReceive();
        if (WSTConf('CONF.isOpenSupplier')==1){
			$rs = model('SupplierCronJobs')->autoReceive();
		}
        return json($rs);
	}

	/**
	 * 发送队列消息
	 */
	public function autoSendMsg(){
	 	$m = new M();
        $rs = $m->autoSendMsg();
        return json($rs);
	}
	/**
	 * 生成sitemap.xml
	 */
	public function autoFileXml(){
		$m = new M();
		$rs = $m->autoFileXml();
		return json($rs);
	}

	/**
	 * 商家订单自动结算
	 */
	public function autoShopSettlement(){
		$m = new M();
		$rs = $m->autoShopSettlement();
		if (WSTConf('CONF.isOpenSupplier')==1){
			$rs = model('SupplierCronJobs')->autoSupplierSettlement();
		}
		return json($rs);
	}

	public  function clearPoster($value=''){
		$m = new M();
		$rs = $m->clearPoster();
		return json($rs);
	}

    /**
     * 店铺到期后自动修改店铺为停止状态
     */
    public function autoChangeShopStatus(){
        $m = new M();
        $rs = $m->autoChangeShopStatus();
        return json($rs);
    }

    /**
     * 供货商到期后自动修改供货商为停止状态
     */
    public function autoChangeSupplierStatus(){
        $m = new M();
        $rs = $m->autoChangeSupplierStatus();
        return json($rs);
    }
}