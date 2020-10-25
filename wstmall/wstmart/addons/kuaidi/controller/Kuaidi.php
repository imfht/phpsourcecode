<?php
namespace addons\kuaidi\controller;

use think\addons\Controller;
use addons\kuaidi\model\Kuaidi as M;
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
 * 快递查询控制器
 */
class Kuaidi extends Controller{
	public function __construct(){
		parent::__construct();
		$this->assign("v",WSTConf('CONF.wstVersion')."_".WSTConf('CONF.wsthomeStyleId'));
	}


	/**
	 * 获取物流详情
	 */
	public function checkExpress(){
		$m = new M();
        $orderId = (int)input("orderId");
        $hasExpress = $m->checkHasExpress($orderId);
        $expressLogs = [];
        if($hasExpress) {
            $express = $m->getExpress($orderId);
            foreach ($express as $v) {
                if ($v["expressNo"] != "" && $v['expressId']>0) {
                    $res = $m->getOrderExpresses($orderId, $v['expressId'], $v['expressNo']);
                    $res['expressId'] = $v['expressId'];
                    $res['expressNo'] = $v['expressNo'];
                    $expressLogs['expressData'][] = $res;
                }
            }
            foreach($expressLogs["expressData"] as $k => $v){
                $state = isset($v["logs"]["state"])?$v["logs"]["state"]:'-1';
                $expressLogs['express'][$k]["stateText"] = $m->getExpressState($state);
                $expressLogs['express'][$k]["expressId"] = $v["expressId"];
                $expressLogs['express'][$k]["expressNo"] = $v["expressNo"];
                $expressLogs['express'][$k]["expressName"] = $res['expressName'];
            }
            $expressLogs["goodlist"] = $m->getOrderInfo();
        }
		return WSTReturn('',1,$expressLogs);
	}
}