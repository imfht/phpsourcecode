<?php
// +----------------------------------------------------------------------
// | openWMS (开源wifi营销平台)
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2025 http://cnrouter.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.gnu.org/licenses/gpl-2.0.html )
// +----------------------------------------------------------------------
// | Author: PhperHong <phperhong@cnrouter.com>
// +----------------------------------------------------------------------
namespace admin\Controller;
use Think\Controller;
class BaseController extends Controller {
    function _initialize(){    
        
        //不需要登录的方法
        $notlogin_array = array(
            'Admin' => array('login', 'check'),
            'Merchant'=> array('get_code', 'reg_merchant'),
        );

        $is_not_login = false;

        foreach($notlogin_array as $k => $v){
            foreach($v as $key=>$val){
                if (CONTROLLER_NAME==$k && ACTION_NAME == $val){
                        $is_not_login = true;
                        break;
                }   
            }   
        }
        
        $adminid = session('adminid');
       
        if(!$is_not_login && empty($adminid)){

            redirect(U('Admin/login'));
            
        }
        $cop = C('COPYRIGHT');
    
        //获取商户信息
        $cop['web_site'] = C('WEB_SITE');
        $this->assign($cop);
        
    }
    public function _empty(){
           $this->display('Empty:index');
    }
}