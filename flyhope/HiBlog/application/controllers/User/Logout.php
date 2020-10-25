<?php
/**
 * 用户退出
 *
 * @author chengxuan <i@chengxuan.li>
 */
class User_LogoutController extends AbsController {
    
    public function indexAction() {
        session_destroy();
        return $this->redirect(Comm\View::path(''));
    }
    
}
