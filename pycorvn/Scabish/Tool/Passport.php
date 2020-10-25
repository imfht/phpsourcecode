<?php

/**
 * 通行证状态组件
 * @author keluo<pycorvn@yeah.net>
 * @sine 2016-7-12 9:24:05
 */
class Passport {
	
    private static $info = null;
    
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * 
	 * 检查用户是否登录
	 * @return ID 或 false
	 */
	public static function Check() {
		$identify = SCS::Cookie()->Get('PASSPORT');
		if($identify && SCS::Identify()->CheckValid($identify)) {
			return SCS::Identify()->GetID($identify);
		}
		return false;
	}
	
	/**
	 * 
	 * 设置用户为登录状态
	 */
	public static function Login($customerID) {
		SCS::Cookie()->Set('PASSPORT', SCS::Identify()->SetID($customerID), 3600*24*30);
	}
	
	public static function Info() {
	    if(is_null(self::$info)) {
	        if(false != ($id = self::Check())) {
	            $customer = SCS::Curd('Customer')->Read($id, null, true);
	            if($customer) {
    	            self::$info = $customer;
    	            self::$info->fdSetting = self::$info->fdSetting ? explode(',', self::$info->fdSetting) : [];
    	            unset(self::$info->fdPassword);
	            } else {
	                self::Logout();
	            }
	        }
	    }
	    
	    return self::$info;
	}
	
	/**
	 * 
	 * 设置用户为退出状态
	 */
	public static function Logout() {
		SCS::Cookie()->Delete('PASSPORT');
		return true;
	}
} 