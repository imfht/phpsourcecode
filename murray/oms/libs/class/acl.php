<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package ACL 类
* 在用户表中存储了roles信息,用户-角色关联 user(id,roles)
* 为用户指派角色,每次均先全部移除表中相关记录再插入
* 在 用户表中增加 字段 roles 来取代 AclBase 中的 关联表
* 解决父类中用户量大情况的性能问题的一种备用方案
* 为了获取某角色对应的用户列表,在存入用户角色时 使用 [;role_id;role_id;role_id;role_id;] 的格式
* 在查找时则可以 使用 like '%;13;%' 来查询
* 校验步骤如下:
* 
* 1. 先校验 资源本身 access 属性
*    EVERYONE => true,NOBODY  => false * 其它的属性在下面继续校验
* 2. 从 session(或者 用户session表)中获取角色id集合
* 3. 如果 用户拥有角色 则 HAS_ROLE => true , NO_ROLE => false;反之亦然
* 4. 如果资源 access == ALLOCATE_ROLES
*      1. 从缓存(或者 $tableRefResourcesRoles)中获取 资源对应的角色id集合
*      2. 将用户拥有的角色id集合 与 资源对应的角色id集合求交集
*      3. 存在交集 => true;否则 => false
*/

defined('INPOP') or exit('Access Denied');

class acl extends aclBase{
	
	//对资源进行acl校验
	function verity($router){    
		if(empty($router)) return false;
		$rsRow = $this->getInfoResource($router);
		//未定义资源的缺省访问策略
		if(!$rsRow) return false;		
		$rsRow['access'] = self::formatAccessValue($rsRow['access']);    
		// 允许任何人访问
		if(self::EVERYONE == $rsRow['access']) return true;    
		// 不允许任何人访问
		if(self::NOBODY == $rsRow['access']) return false;    
		// 获取用户信息
		$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;    
		// 用户未登录,则当成无访问权限
		if(empty($user)) return false;    
		$user['roleids'] = empty($user['roleids']) ? null : explode(';', $user['roleids']);    
		$userHasRoles = !empty($user['roleids']);    
		//允许不带有角色的用户访问
		if(self::NO_ROLE == $rsRow['access']) return $userHasRoles ? false : true;    
		//允许带有角色的用户访问
		if(self::HAS_ROLE == $rsRow['access']) return $userHasRoles ? true : false;		
		//对用户进行资源<->角色校验
		if($userHasRoles){
			foreach($user['roleids'] as $roleid){
				if($roleid && $this->verityResourcesRoles($rsRow['rsid'], $roleid)) return true;
			}
		}
		return false;
	}
	
	//验证资源与身份
	function verityResourcesRoles($rsid, $roleid){
		if(!$roleid || !$rsid) return false;
		$sql = " roleid = ".(int)$roleid." and rsid = ".(int)$rsid." ";
		$has = $this->db->get_one("SELECT * FROM ".$this->tableRefResourcesRoles." WHERE ".$sql." limit 0,1;");
		if(!$has) return false;
		$hasRole = $this->getInfoResourceById((int)$rsid);
		if(self::ALLOCATE_ROLES == $hasRole['access']) return $hasRole['access'] ? true : false;	
	}

}
?>