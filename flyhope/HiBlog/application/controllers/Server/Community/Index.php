<?php
/**
 * 社区
 *
 * @package Controller
 * @author  chengxuan <i@chengxuan.li>
 */
class Server_Community_IndexController extends AbsController {

    /**
     * 允许未登录访问
     *
     * @var boolean
     */
    protected $_need_login = true;

    public function indexAction() {
        //默认跳转附近的极客
        return $this->redirect(\Comm\View::path('server/community/near'));
    }
}
