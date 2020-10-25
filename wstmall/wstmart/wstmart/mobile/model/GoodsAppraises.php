<?php
namespace wstmart\mobile\model;
use wstmart\common\model\GoodsAppraises as CGoodsAppraises;
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
 * 评价类
 */
class GoodsAppraises extends CGoodsAppraises{
	/**
	 *  获取评论
	 */
	public function getAppr(){
		$oId = (int)input('oId');
		$uId = (int)session('WST_USER.userId');
		$gId = (int)input('gId');
		$specId = (int)input('sId');
		$orderGoodsId = (int)input('orderGoodsId');
		$rs = $this->where(['orderId'=>$oId,'userId'=>$uId,'goodsId'=>$gId,'goodsSpecId'=>$specId,'orderGoodsId'=>$orderGoodsId])->find();
		if($rs!==false){
			$rs = !empty($rs)?$rs:['goodsScore'=>'','timeScore'=>'','serviceScore'=>'','content'=>''];
			return WSTReturn('',1,$rs);
		}
		return WSTReturn('获取出错',-1);
	}
	
}
