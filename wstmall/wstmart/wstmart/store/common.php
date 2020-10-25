<?php
use think\Db;
use think\Session;
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
 */
/**
 * 获取指定父级的商家店铺分类
 */
function WSTStoreCats($parentId){
	$shopId = (int)session('WST_STORE.shopId');
	$dbo = Db::table('__SHOP_CATS__')->where(['dataFlag'=>1, 'isShow' => 1,'parentId'=>$parentId,'shopId'=>$shopId]);
	return $dbo->field("catName,catId")->order('catSort asc')->select();
}

/**
 * 判断门店访问权限
 */
function WSTStoreGrant($url){
    $SHOP = session('WST_STORE');
    if($SHOP['userType']!=2)return false;
    if($SHOP['roleId']==0)return true;
    $privilegeUrl = $SHOP['privilegeUrls'];
    $hasPrivilege = false;
    if($privilegeUrl){
    	$url = strtolower($url);
    	$privilegeUrl = json_decode($privilegeUrl);
    	foreach ($privilegeUrl as $key => $rv) {
    		foreach ($rv as $rkey => $vv) {
    		    if(in_array($url,$vv->urls))$hasPrivilege = true;
    	    }
    	}
    }
    return $hasPrivilege;
}
