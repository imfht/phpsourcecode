<?php
namespace wstmart\home\model;
use wstmart\common\model\GoodsCats as M;
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
 * 商品属性分类
 */
class Attributes extends Base{
	/**
	 * 获取可供筛选的商品属性
	 */
	public function listQueryByFilter($catId){
		$m = new M();
		$ids = $m->getParentIs($catId);
		if(!empty($ids)){
			$catIds = [];
			foreach ($ids as $key =>$v){
				$catIds[] = $v;
			}
			// 取出分类下有设置的属性。
			$attrs = $this->alias('a')
					  ->join('__GOODS_ATTRIBUTES__ ga','ga.attrId=a.attrId','inner')
					  ->field('ga.attrId,GROUP_CONCAT(distinct ga.attrVal) attrVal,a.attrName')
					  ->where(['a.isShow'=>1,'a.dataFlag'=>1])
					  ->where([['a.goodsCatId','in',$catIds],['a.attrType','<>',0]])
					  ->group('ga.attrId')
					  ->order('a.attrSort asc')
					  ->select();
			foreach ($attrs as $key =>$v){
			    $attrs[$key]['attrVal'] = explode(',',$v['attrVal']);
			}
			return $attrs;
		}
		return [];
	}
	/**
	* 根据商品id获取可供选择的属性
	*/
	public function getAttribute($goodsId){
		if(empty($goodsId))return [];
		$attrs = $this->alias('a')
					  ->join('__GOODS_ATTRIBUTES__ ga','ga.attrId=a.attrId','inner')
					  ->field('ga.attrId,GROUP_CONCAT(distinct ga.attrVal) attrVal,a.attrName')
					  ->where(['a.isShow'=>1,'a.dataFlag'=>1])
					  	->where([['ga.goodsId','in',$goodsId],['a.attrType','<>',0]])
					  ->group('ga.attrId')
					  ->order('a.attrSort asc')
					  ->select();
		if(empty($attrs))return [];
		foreach ($attrs as $key =>$v){
			    $attrs[$key]['attrVal'] = explode(',',$v['attrVal']);
		}
		return $attrs;
	}
}
