<?php
/**
 * 管理中心欢迎页
 */
namespace Control\Controller;
use Core\Model\Utility;
use Think\Controller;

class NotifyController extends Controller {
    
    public function _initialize(){
        C('FRAME_ACTIVE', 'member');
    }
    
    public function settingAction() {
        $this->display('Welcome/control');
    }
}