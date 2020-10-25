<?php
/**
 * 用户控制器基类
 *
 * @author andery
 */
class userbaseAction extends frontendAction {

    public function _initialize(){
        parent::_initialize();
        //访问者控制
        $seoconfig=$this->_config_seo(C('wkcms_seo_config.ucenter'));
        $this->assign('seoconfig',$seoconfig);
        
        unset($_GET['_URL_']);
        $param = array();
        $param['refer'] = base64_encode(U(false, $_GET));
        if(MODULE_NAME == 'doc'){
       if (!$this->visitor->is_login && !in_array(ACTION_NAME, array('index', 'cont')))
       {
       	    IS_AJAX && $this->ajaxReturn(0, L('login_please'));

       	   // $url=U('user/login');
       	   // redirect($url);
            $this->redirect('user/login', $param);
       }
        }else{
        if (!$this->visitor->is_login && !in_array(ACTION_NAME, array('login', 'register', 'binding','uploadImg'))) {
            IS_AJAX && $this->ajaxReturn(0, L('login_please'));
            //$url=U('user/login');
       	    //redirect($url);
            $this->redirect('user/login', $param);
        }
        }
        
    }


    
}