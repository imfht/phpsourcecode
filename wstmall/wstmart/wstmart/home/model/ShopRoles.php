<?php
namespace wstmart\home\model;
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
 * 门店色务类
 */
class ShopRoles extends Base{
	/**
	 * 角色列表
	 */
	public function pageQuery(){
		$shopId = (int)session('WST_USER.shopId');
		$roleName = input("roleName/s");
		$where = ["shopId"=>$shopId,"dataFlag"=>1];
		if($roleName != ""){
			$where[] = ["roleName","like","%".$roleName."%"];
		}
		$page = $this
				->field('id,shopId,roleName,createTime')
		    	->where($where)
		    	->paginate(input('pagesize/d'))->toArray();
		return $page;
	}

	public function listQuery(){
		$shopId = (int)session('WST_USER.shopId');
		$where = ["shopId"=>$shopId,"dataFlag"=>1];
		$list = $this
				->field('id,shopId,roleName,createTime')
		    	->where($where)
		    	->select();
		return $list;
	}
	/**
	*  根据id获取店铺角色
	*/
	public function getById($id){
		$shopId = (int)session('WST_USER.shopId');
	    $role = $this->field('id,shopId,roleName,createTime,privilegeUrls,privilegeMsgs')
					->where(["id"=>$id,"shopId"=>$shopId,"dataFlag"=>1])
					->find();
		if(empty($role))return [];
		$menuList = json_decode($role["privilegeUrls"],true);
		$menuUrls = array();
		$menuOtherUrls = array();

		foreach ($menuList as $k1 => $menus1) {
			foreach ($menus1 as $k2 => $menus2) {
				$menuUrls = array_merge($menuUrls,$menus2["urls"]);
				$otherUrls = $menus2["otherUrls"];
				foreach ($otherUrls as $ko => $ourls) {
					$othurls = explode(',',$ourls);
					$menuOtherUrls = array_merge($menuOtherUrls,$othurls);
				}
			}
		}
		$role["privilegeMsgs"] = explode(",",$role["privilegeMsgs"]);
		$role["menuUrls"] = array_filter($menuUrls);
		$role["menuOtherUrls"] = array_filter($menuOtherUrls);
		return $role;
	}

	/**
	 * 新增店铺角色
	 */
	public function add(){
		$shopId = (int)session('WST_USER.shopId');
		$data["shopId"] = $shopId;
		$data["roleName"] = input('roleName/s');
		if($data["roleName"]==""){
			return WSTReturn('请输入角色名称',-1);
		}
		$data["privilegeMsgs"] = input('privilegeMsgs/s');
		$menuIds = input('menuIds/s');
		$urls = [];
		$otherUrls = [];
		if($menuIds==""){
			return WSTReturn('请选择权限',-1);
		}else{
			$roleMenus = model("HomeMenus")->getRoleMenus();
			$menuIds = explode(",",$menuIds);
			$menuList = array();
			for($i=0,$j=count($menuIds);$i<$j;$i++){
				$menu = $roleMenus[$menuIds[$i]];
				$menuList[$menu["grandpaId"]][$menu["parentId"]]["urls"][] = strtolower($menu["menuUrl"]);
				$menuList[$menu["grandpaId"]][$menu["parentId"]]["otherUrls"][] = strtolower($menu["menuOtherUrl"]);
			}
		}
		$data["privilegeUrls"] = json_encode($menuList);
		$data["createTime"] = date("Y-m-d H:i:s");
		$result = $this->save($data);
		if(false !== $result){
        	return WSTReturn("新增成功", 1);
        }
        return WSTReturn('新增失败',-1);
	}

	/**
	 * 修改店铺角色
	 */
	public function edit(){
		$shopId = (int)session('WST_USER.shopId');
		$id = (int)input('id');
		$data["roleName"] = input('roleName/s');
		if($data["roleName"]==""){
			return WSTReturn('请输入角色名称',-1);
		}
		$data["privilegeMsgs"] = input('privilegeMsgs/s');
		$menuIds = input('menuIds/s');
		$urls = [];
		$otherUrls = [];
		if($menuIds==""){
			return WSTReturn('请选择权限',-1);
		}else{
			$roleMenus = model("HomeMenus")->getRoleMenus();
			$menuIds = explode(",",$menuIds);
			$menuList = array();
			for($i=0,$j=count($menuIds);$i<$j;$i++){
				$menu = $roleMenus[$menuIds[$i]];
				$menuList[$menu["grandpaId"]][$menu["parentId"]]["urls"][] = strtolower($menu["menuUrl"]);
				$menuList[$menu["grandpaId"]][$menu["parentId"]]["otherUrls"][] = strtolower($menu["menuOtherUrl"]);
			}
		}
		$data["privilegeUrls"] = json_encode($menuList);
		$result = $this->where(["id"=>$id,"shopId"=>$shopId])->update($data);
		if(false !== $result){
        	return WSTReturn("修改成功", 1);
        }
        return WSTReturn('删除失败',-1);
	}

	/**
	 * 删除店铺角色
	 */
	public function del(){
		$shopId = (int)session('WST_USER.shopId');
		$id = input('post.id/d');
		$data = [];
		$data['dataFlag'] = -1;
	    $result = $this->where(["id"=>$id,"shopId"=>$shopId])->update($data);
        if(false !== $result){
        	return WSTReturn("删除成功", 1);
        }
        return WSTReturn('删除失败',-1);
	}
}
