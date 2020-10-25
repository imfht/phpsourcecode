<?php
namespace Home\Controller;
use Think\Controller;

class IndexController extends Controller {

    /**
     * 闫嵩达 微信公众
     * @return [type] [description]
     */
    public function index(){
        $appid = '';
        $appsecret = '';
        $weixin = new \Common\Lib\Weixin\Weixin($appid, $appsecret);
        $typeData = $weixin->getTypedata();
        $typeData['met'] = 'index';
        $this->assign('data', $typeData);
        $this->display();
    }

}