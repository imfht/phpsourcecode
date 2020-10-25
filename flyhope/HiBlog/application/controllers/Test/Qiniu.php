<?php
/**
 * 测试七牛是否好用
 *
 * @package Controller
 * @author chengxuan <i@chengxuan.li>
 */
class Test_QiniuController extends \Yaf_Controller_Abstract {


    public function indexAction() {

        $source_file = ROOT_PATH . '/static/img/manage/header-bg.jpg';
        $api = new \Api\Qiniu();
        var_dump($api->upload('flyhope', 'debug/debug.jpg', $source_file));
        return false;
    }

}


