<?php
/**
 * 更新附近的极客信息
 *
 * @package Controller
 * @author  chengxuan <i@chengxuan.li>
 */
class Server_Near_UpdateController extends AbsController {
    
    public function indexAction() {
        $access_token = $this->getRequest()->getPost('access_token');
        Yaf_Registry::set('access_token', $access_token);
        $result = \Model\Near::update();
        Comm\Response::jsonp(100000, '保存成功', ['result' => $result]);
    }
}
