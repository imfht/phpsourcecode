<?php
namespace Home\Controller;

/**
 * PublicController
 * 首页页面访问接口
 */
class PublicController extends CommonController {


    /**
     * 首页
     * @return
     */
    public function index() {
        $this->assign('set_float','none');
        $this->assign('index_nav','yes');
        $this->display('Default/index');
    }


}
