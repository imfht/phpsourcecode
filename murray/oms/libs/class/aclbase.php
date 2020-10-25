<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package ACL 访问授权控制基础类
*/

defined('INPOP') or exit('Access Denied');

class aclBase{
    
    //不允许任何人访问
    const NOBODY = 0;    
    //允许任何人访问
    const EVERYONE = 1;    
    //允许拥有角色的用户访问
    const HAS_ROLE = 2;    
    //允许不带有角色的用户访问
    const NO_ROLE = 3;
    //在资源-角色关联定义的角色才能访问
    const ALLOCATE_ROLES = 4;
	public $db;
    //定义相关的表名
    public $tableResources;
    public $tableRoles;
    public $tableRefResourcesRoles;
    public $tableRefUsersRoles;

	//必须继承
	public function __construct(){
		$this->tableResources = getTable('aclresources');
		$this->tableRoles = getTable('aclroles');
		$this->tableRefResourcesRoles = getTable('aclresourcesroles');
		$this->tableRefUsersRoles = getTable('users');
		$this->db = DB::getInstance();	
	}

	//销毁
	public function __destruct(){
		unset($this->db);
	}
    
    //格式化资源的访问权限并返回
    static function formatAccessValue($access){
        static $aclArr = array(self::NOBODY,self::EVERYONE,self::HAS_ROLE,self::NO_ROLE,self::ALLOCATE_ROLES);
        return in_array($access, $aclArr) ? $access : self::NOBODY;
    }
	
	//限制权限表操作范围
	function isAclTable($table){
		$aclTableArr = array($this->tableResources, $this->tableRoles, $this->tableRefResourcesRoles, $this->tableRefUsersRoles);
		return in_array($table, $aclTableArr) ? $table : false;
	}
	
	//添加
	function aclAdd($table, $data){
		$AclTable = $this->isAclTable($table);
		if(!$AclTable) return false;
		if(!$data) return false;
		$csql1 = $csql2 = $cs = "";
		$fsql1 = $fsql2 = $fs = "";
		foreach($data as $key=>$value){
			$csql1 .= $cs.$key;
			$csql2 .= $cs."'".$value."'";
			$cs = ",";
		}
		$isdone = $this->db->query("INSERT INTO ".$isAclTable." ($csql1) VALUES($csql2) ;");
		return $isdone;
	}

	//修改
	function aclEdit($table, $data, $field){
		$AclTable = $this->isAclTable($table);
		if(!$AclTable) return false;
		if(!$field) return false;
		if(!$data) return false;
		if(!$data[$field]) return false;
		$keyId = $data[$field];
		$sql = $s = "";
		$fsql = $fs = "";
		foreach($data as $key=>$value){
			$sql .= $s.$key."='".$value."'";
			$s = ",";
		}
		$isdone = $this->db->query("UPDATE ".$AclTable." SET ".$sql." WHERE ".$field." = '".$keyId."' ;");
		return $isdone;
	}
	
	//删除
	function aclDelete($table, $ids, $field){
		$AclTable = $this->isAclTable($table);
		if(!$AclTable) return false;
		if(!$field) return false;		
		if(!$ids) return false;
		$ids = is_array($ids) ? implode(',',$ids) : $ids;
		if($ids) $sql = " ".$field." IN( ".$ids." )";
		$isdone = $this->db->query("DELETE FROM ".$table." WHERE $sql ");
		return $isdone;
	}	
	
	//获取信息
	function aclGetInfo($table, $id, $field){
		$AclTable = $this->isAclTable($table);
		if(!$AclTable) return false;
		if(!$field) return false;		
		if(!$id) return false;
		$sql = " ".$field." = ".$id." ";
		$return = $this->db->get_one("SELECT * FROM ".$AclTable." WHERE ".$sql." limit 0,1;");
		return $return;
	}
    
    //创建资源,返回资源记录主键
    function createResource($rsid, $access, $router, $name){
        if(empty($rsid)) return false;
		$control = $router['control'];
		$action = $router['action'];
        $resource = array(
            'rsid' => $rsid,
			'aclcontrol' => $control,
			'aclaction' => $action,
            'access' => self::formatAccessValue($access),
            'name' => $name,
            'createdtime' => CURRENT_TIMESTAMP
        );
        return $this->aclAdd($this->tableResources,$resource);
    }
    
    //修改资源,返回成功状态
    function updateResource(array $resource){       
        if(!isset($resource['rsid'])) return false;        
        $resource['updatedtime'] = CURRENT_TIMESTAMP;        
        return $this->aclEdit($this->tableResources, $resource, 'rsid');
    }
    
    //删除资源
    function deleteResource($rsid){
        if(empty($rsid)) return false;
        return $this->aclDelete($this->tableResources, $rsid, 'rsid');
    }
	
	//根据ID获取资源记录
	function getInfoResourceById($rsid){
        if(empty($rsid)) return false;
        return $this->aclGetInfo($this->tableResources, $rsid, 'rsid');	
	}

	//获取资源记录
	function getInfoResource($router){
        if(empty($router)) return false;
		$control = $router['control'];
		$action = $router['action'];
		$sql = " aclcontrol = '".$control."' and aclaction = '".$action."' ";
		$return = $this->db->get_one("SELECT * FROM ".$this->tableResources." WHERE ".$sql." limit 0,1;");		
        return $return;	
	}
    
    //创建角色,返回角色记录主键
    function createRole($name,$desc){
        if(empty($name)) return false;        
        $role = array(
            'name' => $name,
            'desc' => $desc,
            'createdtime' => CURRENT_TIMESTAMP
        );        
        return $this->aclAdd($this->tableRoles, $role);
    }
    
    //修改角色,返回成功状态
    function updateRole(array $role){       
        if(!isset($role['rid'])) return false;        
        if(isset($role['name'])) unset($role['name']);
        $role['updatedtime'] = CURRENT_TIMESTAMP;        
        return $this->aclEdit($this->tableRoles, $role, 'rid');
    }
    
    //删除角色
    function deleteRole($roleid){
        if(empty($roleid)) return false;
        return $this->aclDelete($this->tableRoles, (int)$roleid, 'rid');
    }
	
	//根据ID获取资源记录
	function getInfoRole($roleid){
        if(empty($roleid)) return false;
        return $this->aclGetInfo($this->tableResources, $roleid, 'rid');	
	}
	
    //为资源指定角色,每次均先全部移除表中相关记录再插入
    function allocateRolesForResource($rsid, $roleIds, $setNull=false, $defaultAccess=-1){
        if(empty($rsid)) return false;        
        $roleIds = explode(',', $roleIds);
        if(empty($roleIds)){
            if($setNull){
                $this->aclDelete($this->tableRefResourcesRoles, $rsid, 'rsid');                
                if($defaultAccess != -1){
                    $defaultAccess = self::formatAccessValue($defaultAccess);
                    $this->updateResource(array('rsid'=>$rsid,'access'=>$defaultAccess));
                }
                return true; 
            }
            return false;
        }
        
        $this->aclDelete($this->tableRefResourcesRoles, $rsid, 'rsid');        
        $roleIds = array_unique($roleIds);        
        foreach ($roleIds as $roleid){
            $this->aclAdd($this->tableRefResourcesRoles,array('rsid'=>$rsid,'roleid'=>(int)$roleid));
        }
        return true;
    }
    
	//为资源清除角色
    function cleanRolesForResource($rsid){
        if(empty($rsid)) return false;
        return $this->aclDelete($this->tableRefResourcesRoles, (int)$rsid, 'rsid');
    }
    
	//为角色清除资源
    function cleanResourcesForRole($roleid){
        if(empty($roleid)) return false;
        return $this->aclDelete($this->tableRefResourcesRoles, (int)$roleid, 'roleid');
    }
    
    //为角色分配资源,每次均先全部移除表中相关记录再插入
    function allocateResourcesForRole($roleid,$rsids){
        if(empty($roleid)) return false;        
        $roleid = (int)$roleid;
        $rsids = explode(',', $rsids);
        if(empty($rsids)) return false;        
        $this->aclDelete($this->tableRefResourcesRoles, $roleid, 'roleid');        
        $rsids = array_unique($rsids);        
        foreach ($rsids as $rsid){
            $this->aclAdd($this->tableRefResourcesRoles,array('rsid'=>$rsid,'roleid'=>$roleid));
        }
        return true;
    }
    
	//更新用户角色
    function allocateRolesForUser($uid,$roleIds){
        if(empty($uid)) return false;        
        $uid = (int)$uid;
        $roleIds = explode(';', $roleIds);
        if(empty($roleIds)) return false;        
        $roleIds = array_unique($roleIds);
        $roles = sprintf(';%s;',implode(';',$roleIds));
        return $this->aclEdit($this->tableRefUsersRoles,array('uid'=>(int)$uid, 'roles'=>$roles));
    }
    
    //清除用户的角色信息
    function cleanRolesForUser($uid){
        if(empty($uid)) return false;
        return $this->aclEdit($this->tableRefUsersRoles,array('uid'=>(int)$uid, 'roles'=>''));
    }

}
?>