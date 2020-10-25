<?php
/**
 * 管理中心欢迎页
 */
namespace Control\Controller;
use Core\Model\Utility;
use Think\Controller;
class WelcomeController extends Controller {
    public function _empty(){
        C('FRAME_ACTIVE', ACTION_NAME);
        $this->display('Welcome/control');
    }
    
    public function commonAction() {
        C('FRAME_ACTIVE', 'common');
        $this->display('Welcome/control');
    }
}