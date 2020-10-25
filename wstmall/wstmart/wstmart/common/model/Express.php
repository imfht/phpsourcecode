<?php
namespace wstmart\common\model;
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
 * 快递业务处理类
 */
use think\Db;
class Express extends Base{
	protected $pk = 'expressId';
    /**
	 * 获取快递列表
	 */
    public function listQuery(){
         return $this->where('dataFlag',1)->select();
    }
    public function shopExpressList($sId=0){
		$shopId = $sId==0?(int)session('WST_USER.shopId'):$sId;
		$where = [];
		$where[] = ["shopId","=",$shopId];
		$where[] = ["isEnable","=",1];
		$where[] = ["se.dataFlag","=",1];
		$where[] = ["e.dataFlag","=",1];
		$rs = Db::name("shop_express se")
				->join("express e","se.expressId=e.expressId","inner")
				->field("se.id,e.expressName")
				->where($where)
				->select();
		return $rs;
	}
}
