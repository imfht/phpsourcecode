<?php
namespace Admin\Controller;
use Think\Controller;

/**
 * 需要权限控制的控制器,统一继承这个
 * @author jlb
 * @since 2016年12月7日09:56:50
 */
class PrivilegeController extends CommonController
{



    public function __construct()
    {
    	parent::__construct();
    	if ( !$this->admin_id ) 
    	{
    		//请先登录
    		$this->error('请先登录',U('Index/index'));
    	}

    	//判断是否有权限.
    	if ( $this->role_id != 1 && (empty($this->privilegeList) || !$this->hasPrivilege(CONTROLLER_NAME, ACTION_NAME)) ) 
    	{
    		//没有权限
    		echo '<script>alert("您没有权限操作此项,如有需要请联系管理员");history.back(-1);</script>';
    		die;
    	}
    }

    /**
     * 判断当前操作是否具有权限
     * @param  [type]  $controller [description]
     * @param  [type]  $action     [description]
     * @return boolean             [description]
     */
    protected function hasPrivilege($controller, $action, $privilegeList=FALSE) 
    {	
    	if ( !$privilegeList ) 
        {
    		$privilegeList = array_column($this->privilegeList, 'url');
    	}
    	if ( !in_array($controller . '/' . $action, $privilegeList) ) 
    	{
    		return false;
    	}
    	return true;
    }
}