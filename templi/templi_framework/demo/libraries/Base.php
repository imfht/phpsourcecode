<?php
/**
 * 前台 基础 控制器类
 * @author 七殇酒
 * @qq 739800600
 * @email 739800600@qq.com
 * @date 2013-07-09
 */


namespace demo\libraries;

class Base extends \framework\core\Controller
{
    
    private static $_member_info = array(); //登录用户信息
    private static $_group_info = array();  //用户组信息
    /** @var  \framework\session\Session */
    private $session;
    function init(){
        $this->session = new \framework\session\Session();
    }
    
    /**
     * 获取用户信息
     */
    protected function get_member_info(){

    }
    /**
     * 获取用户组信息
     *
     * @param int $group_id 用户组id
     *
     * @return array
     */
    protected function get_group_info($group_id=''){

    }

}