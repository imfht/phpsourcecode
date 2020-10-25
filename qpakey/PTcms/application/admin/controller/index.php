<?php

/**
 * @Author: 杰少Pakey
 * @Email : admin@ptcms.com
 * @File  : index.php
 */
class IndexController extends AdminController {

    public function init() {
        $this->config->set('layout', false);
        parent::init();
    }

    // 框架页
    public function indexAction() {

        $this->view->menu=$this->model('adminmenu')->getusermenu();
    }

    //欢迎页
    public function welcomeAction() {
        $tips = array();
        // success info warning danger
        if ($this->config->get('appid') == 'test') {
            $tips[] = array('type' => 'danger', 'content' => '您当前使用的APPID为test，请抓紧时间申请正式APPID，否则您可能会无法使用我们的API服务！<a href="' . U('admin.set.api') . '">点击这里更换</a>');
        }
        if ($this->config->get('adminpath') == 'admin') {
            $tips[] = array('type' => 'warning', 'content' => '您后台目录为默认的admin，为安全考虑，请您更改目录地址！<a href="' . U('admin.set.base') . '">点击这里更换</a>');
        }
        $usernum = $this->db('user')->count();
        $this->view->sitenum = 1;
        $this->view->usernum = $usernum;
        $this->view->adnum = 1;
        $this->view->friendlinknum = $this->db('friendlink')->count();
        $this->view->tips = $tips;
    }
}