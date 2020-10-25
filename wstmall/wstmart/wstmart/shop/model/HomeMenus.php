<?php
namespace wstmart\shop\model;
use wstmart\common\model\HomeMenus as CHomeMenus;
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
 * 菜单业务处理
 */
class HomeMenus extends CHomeMenus{
	/**
     * 获取店铺菜单树
     */
    public function getShopMenus(){
        $m1 = $this->getMenus();
        $userType = (int)session('WST_USER.userType');
        $menuUrls = array();
        if($userType==1){
            $shopId = (int)session('WST_USER.shopId');
            $roleId = (int)session('WST_USER.roleId');
            if($roleId>0){
                $role = model("shop/ShopRoles")->getById($roleId);
                $menuUrls = isset($role["privilegeUrls"])?json_decode($role["privilegeUrls"],true):[];
                foreach ($m1[1] as $k1 => $menus1) {
                    if(!array_key_exists($menus1["menuId"],$menuUrls)){
                        unset($m1[1][$k1]);
                    }else{
                        if(isset($menus1["list"])){
                            if(count($menus1["list"])>0){
                                foreach ($menus1["list"] as $k2 => $menus2) {
                                    if(!array_key_exists($menus2["menuId"],$menuUrls[$menus1["menuId"]])){
                                        unset($m1[1][$k1]["list"][$k2]);
                                    }else{
                                        if(isset($menus2["list"])){
                                            if(count($menus2["list"])>0){
                                                foreach ($menus2["list"] as $k3 => $menus3) {
                                                    $purls = $menuUrls[$menus1["menuId"]][$menus2["menuId"]];
                                                    $urls = $purls["urls"];
                                                    if(!in_array(strtolower($menus3["menuUrl"]),$urls)){
                                                        unset($m1[1][$k1]["list"][$k2]["list"][$k3]);
                                                    }
                                                }
                                            }else{
                                                unset($m1[1][$k1]["list"][$k2]);
                                            }
                                        }else{
                                            unset($m1[1][$k1]["list"][$k2]);
                                        }
                                    }
                                }
                                if(count($m1[1][$k1]["list"])==0){
                                    unset($m1[1][$k1]);
                                }
                            }else{
                                unset($m1[1][$k1]);
                            }
                        }else{
                            unset($m1[1][$k1]);
                        }
                    }
                }
            }
        }
        return $m1;
    }

	/**
	 * 角色可访问url
	 */
	public function getShopMenusUrl(){
		$wst_user = session('WST_USER');
		
		if(!empty($wst_user)){
			$roleId = isset($wst_user["roleId"])?(int)$wst_user["roleId"]:0;
			if($roleId>0){
				$role = model("shop/ShopRoles")->getById($roleId);
				if(!empty($role)){
					$menuUrls = $role["menuUrls"];
					$menuOtherUrls = $role["menuOtherUrls"];
					$shopUrls = array_merge($menuUrls,$menuOtherUrls);
				}
				
			}
		}
		$shopUrls[] = "shop/shops/index";
		$shopUrls[] = "shop/reports/getstatsales";
		return $shopUrls;
	}

	/**
	 * 获取店铺角色菜单
	 */
	public function getRoleMenus(){
		$rs = $this->alias('m1')
				->join("__HOME_MENUS__ m2","m1.parentId=m2.menuId")
				->where([['m1.isShow','=',1],['m1.dataFlag','=',1],["m1.menuType",'=',1],["m2.parentId",'>',0]])
				->field('m1.menuId,m1.parentId,m2.parentId grandpaId,m1.menuName,m1.menuUrl,m1.menuOtherUrl,m1.menuType')
				->order('m1.menuSort asc,m1.menuId asc')
				->select();
		$m = array();
			//获取第一级
		foreach ($rs as $key => $v){
			$m[$v['menuId']] = ['menuId'=>$v['menuId'],'parentId'=>$v['parentId'],'grandpaId'=>$v['grandpaId'],'menuName'=>$v['menuName'],'menuUrl'=>$v['menuUrl'],'menuOtherUrl'=>$v['menuOtherUrl']];
		}
		return $m;
	}



}
