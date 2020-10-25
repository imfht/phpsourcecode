<?php namespace qeephp\acl;

class Acl
{
	/**
     * 预定义角色常量
     */
	const NOBODY      = 'nobody';    
    const EVERYONE    = 'everyone';
    const NO_ROLE     = 'no_role';
    const HAS_ROLE    = 'has_role';
	
	private function __construct(array $acl)
	{
		$this->config = $acl;
	}
	
	/**
	 * @return qeephp\acl\Acl
	 */
	static function getInstance()
	{
		static $var = null;
		if (is_null($var)) $var = new static( array() );
		return $var;
	}
	
	static function formatAccessor($controller,$action)
	{
		return "{$controller}/{$action}";
	}

    /**
     * 检查指定角色是否有权限访问特定的动作
     *
     * @param string $role
     * @param string $module
     * @param string $accessor
     *
     * @return boolean
     */
    function authorized($role, $module, $accessor)
    {
    	if (empty($module) || empty($accessor)) return false;
    	    	
    	if ( empty($this->config[$module]) || !is_array($this->config[$module]) ) return false;
    	    	
        $default = val($this->config[$module], 'authorized_default');        
        $list = val($this->config[$module], 'authorized_list');

        if (!is_array($list))
        {
        	return self::EVERYONE === $default ? TRUE : FALSE;
        }
    	
        # 如果 action 在 角色 NOBODY中则直接返回false
		if ( !empty($list[self::NOBODY]) )
		{
			if ( in_array($accessor, $list[self::NOBODY]) )
        	{
        		return false;
        	}
		}
        
        # 如果 $accessor 在 角色 everyone中则直接返回true
		if ( !empty($list[self::EVERYONE]) )
		{
			if ( in_array($accessor, $list[self::EVERYONE]) )
        	{
        		return true;
        	}
		}
		
        if ( empty($role) )
        {
        	if ( !empty($list[self::NO_ROLE]) )
        	{
	        	if ( in_array($accessor, $list[self::NO_ROLE]) )
	        	{
	        		return true;
	        	}
        	}
        	if ( !empty($list[self::HAS_ROLE]) )
        	{
	        	if ( in_array($accessor, $list[self::HAS_ROLE]) )
	        	{
	        		return false;
	        	}
        	}
        }
        else
        {
        	if ( !empty($list[self::NO_ROLE]) )
        	{
	        	if ( in_array($accessor, $list[self::NO_ROLE]) )
	        	{
	        		return false;
	        	}
        	}
        	
        	if ( !empty($list[self::HAS_ROLE]) )
        	{
	        	if ( in_array($accessor, $list[self::HAS_ROLE]) )
	        	{
	        		return true;
	        	}
        	}
	        
        	if ( !empty($list[$role]) )
	        {
	        	if ( in_array($accessor, $list[$role]) )
	        	{
	        		return true;	
	        	}
	        }
        }
        
        return self::EVERYONE === $default ? TRUE : FALSE;
    }

    /**
     * 将用户数据保存到 session 中
     *
     * @param array $user
     * @param string $role
     * @param string $module
     */
    function change_user(array $user, $role ,$module ='default')
    {
    	$mainer_id = val($this->config[$module], 'authorized_mainer_id', 'default');
    	
        $user['role'] = $role;
        $_SESSION[$mainer_id] = $user;
    }
    
    /**
     * 获取保存在 session 中的用户数据
     * 
     * @param string $module
     * @param string $data_key 属性
     * 
     * @return array|null 不存在时返回null
     */
    function user($module, $data_key =null)
    {
    	$mainer_id = val($this->config[$module], 'authorized_mainer_id', 'default');
    	
        if (!isset($_SESSION[$mainer_id])){
            return null;
        }
        if (!is_null($data_key)){
            return val($_SESSION[$mainer_id], $data_key, '');
        }
        return $_SESSION[$mainer_id];
    }

    /**
     * 获取 session 中用户信息包含的角色
     *
     * @param string $module
     * @return array
     */
    function user_role($module)
    {
        return $this->user($module,'role');
    }
    
    /**
     * 从 session 中清除用户数据
     * 
     * @param string $module
     */
    function clean_user($module)
    {
    	$mainer_id = val($this->config[$module], 'authorized_mainer_id', 'default');
        unset($_SESSION[$mainer_id]);
    }
    
}