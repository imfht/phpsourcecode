<?php

// +----------------------------------------------------------------------
// | LvyeCMS 插件商店
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.lvyecms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: 旅烨集团 <web@alvye.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use Common\Controller\AdminBase;

class AddonshopController extends AdminBase {

    protected function _initialize() {
        parent::_initialize();
        if (!isModuleInstall('Addons')) {
            $this->error('你还没有安装插件模块，无法使用插件商店！');
        }
    }

    //在线插件列表
    public function index() {
        if (IS_POST) {
            $this->redirect('index', $_POST);
        }
        $parameter = array(
            'page' => $_GET[C('VAR_PAGE')]? : 1,
            'paging' => 10,
        );
        $keyword = I('get.keyword', '', 'trim');
        if (!empty($keyword)) {
            $parameter['keyword'] = $keyword;
            $this->assign('keyword', $keyword);
        }
        if (IS_AJAX) {
            $data = $this->Cloud->data($parameter)->act('get.addons.list');
            if (false === $data) {
                exit($this->Cloud->getError());
            }
            $data['data'] = $data['data']? : array();
            foreach ($data['data'] as $sign => $rs) {
                $version = M('Addons')->where(array('sign' => $rs['sign']))->getField('version');
                if ($version && version_compare($version, $rs['version'], '<')) {
                    $data['data'][$sign]['upgrade'] = true;
                    $data['data'][$sign]['newVersion'] = $rs['version'];
                } else {
                    $data['data'][$sign]['upgrade'] = false;
                }
            }
            $page = $this->page($data['total'], $data['paging']);
            $this->assign('Page', $page->show());
            $this->assign('data', $data['data']);
            $this->display('ajax');
            return true;
        }
        $this->assign('page', $parameter['page']);
        $this->display();
    }

    //云端插件下载安装
    public function install() {
        $sign = I('get.sign', '', 'trim');
        if (empty($sign)) {
            $this->error('请选择需要安装的插件！');
        }
        $this->assign('stepUrl', U('public_step_1', array('sign' => $sign)));
        $this->assign('sign', $sign);
        $this->display();
    }

    //目录权限判断通过后获取下载地址进行插件下载
    public function public_step_1() {
        if (\Libs\System\RBAC::authenticate('install') !== true) {
            $this->errors('您没有该项权限！');
        }
        $sign = I('get.sign', '', 'trim');
        if (empty($sign)) {
            $this->error('请选择需要安装的插件！');
        }
        $cache = S('Cloud');
        if (!empty($cache)) {
            $this->error('已经有任务在执行，请稍后！');
        }
        //帐号权限检测
        if ($this->Cloud->competence() == false) {
            $this->errors($this->Cloud->getError());
        }
        //获取插件信息
        $data = $this->Cloud->data(array('sign' => $sign))->act('get.addons.info');
        if (false === $data) {
            $this->error($this->Cloud->getError());
        } else {
            S('Cloud', $data, 3600);
        }
        if (empty($data)) {
            $this->errors('获取不到需要安装的插件信息缓存！');
        }
        $path = PROJECT_PATH . 'Addon/' . $data['name'];
        //检查是否有同样的插件目录存在
        if (file_exists($path)) {
            $this->errors("目录：{$path} 已经存在，无法安装在同一目录！");
        }
        //获取下载地址
        $packageUrl = $this->Cloud->data(array('sign' => $sign))->act('get.addons.install.package.url');
        if (empty($packageUrl)) {
            $this->errors($this->Cloud->getError());
        }
        //开始下载
        if ($this->CloudDownload->storageFile($packageUrl) !== true) {
            $this->errors($this->CloudDownload->getError());
        }
        $this->success('文件下载完毕！', U('public_step_2', array('package' => $packageUrl)));
    }

    //移动目录到插件
    public function public_step_2() {
        if (\Libs\System\RBAC::authenticate('install') !== true) {
            $this->errors('您没有该项权限！');
        }
        $data = S('Cloud');
        if (empty($data)) {
            $this->errors('获取不到需要安装的插件信息缓存！');
        }
        $packageUrl = I('get.package');
        if (empty($packageUrl)) {
            $this->errors('package参数为空！');
        }
        //临时目录名
        $tmp = $this->CloudDownload->getTempFile($packageUrl);
        //插件安装目录
        $addonsPath = PROJECT_PATH . 'Addon/' . $data['name'] . '/';
        if ($this->CloudDownload->movedFile($tmp, $addonsPath, $packageUrl) !== true) {
            $this->errors($this->CloudDownload->getError());
        }
        $this->success('移动文件到插件目录中，等待安装！', U('public_step_3', array('name' => $data['name'])));
    }

    //安装插件
    public function public_step_3() {
        if (\Libs\System\RBAC::authenticate('install') !== true) {
            $this->errors('您没有该项权限！');
        }
        $name = I('get.name');
        S('Cloud', NULL);
        if (D('Addons/Addons')->installAddon($name)) {
            $this->success('插件安装成功！');
        } else {
            $error = D('Addons/Addons')->getError();
            //删除目录
            LvyeCMS()->Dir->delDir(PROJECT_PATH . 'Addon/' . $name);
            $this->error($error ? $error : '插件安装失败！');
        }
    }

    //插件升级
    public function upgrade() {
        $sign = I('get.sign', '', 'trim');
        if (empty($sign)) {
            $this->error('请选择需要升级的插件！');
        }
        $cache = S('Cloud');
        if (!empty($cache)) {
            $this->error('已经有任务在执行，请稍后！');
        }
        //帐号权限检测
        if ($this->Cloud->competence() == false) {
            $this->error($this->Cloud->getError());
        }
        //获取插件信息
        $data = $this->Cloud->data(array('sign' => $sign))->act('get.addons.info');
        if (false === $data) {
            $this->error($this->Cloud->getError());
        } else {
            $version = M('Addons')->where(array('sign' => $data['sign']))->getField('version');
            if ($version && !version_compare($version, $data['version'], '<')) {
                $this->error('该插件无需升级！');
            }
            S('Cloud', $data, 3600);
        }
        $this->assign('stepUrl', U('public_upgrade_1'));
        $this->display();
    }

    //目录权限判断通过后获取升级包下载地址进行插件下载
    public function public_upgrade_1() {
        if (\Libs\System\RBAC::authenticate('upgrade') !== true) {
            $this->errors('您没有该项权限！');
        }
        $data = S('Cloud');
        if (empty($data)) {
            $this->errors('获取不到需要升级的插件信息缓存！');
        }
        $sign = $data['sign'];
        //检查是否安装
        if (!D('Addons/Addons')->isInstall($data['name'])) {
            $this->errors("没有安装该插件无法升级！");
        }
        $config = M('Addons')->where(array('sign' => $sign))->find();
        if (empty($config)) {
            $this->errors("无法获取插件安装信息！");
        }
        //获取下载地址
        $packageUrl = $this->Cloud->data(array('sign' => $sign, 'version' => $config['version']))->act('get.addons.upgrade.package.url');
        if (empty($packageUrl)) {
            $this->errors($this->Cloud->getError());
        }
        //开始下载
        if ($this->CloudDownload->storageFile($packageUrl) !== true) {
            $this->errors($this->CloudDownload->getError());
        }
        $this->success('升级包文件下载完毕！', U('public_upgrade_2', array('package' => $packageUrl)));
    }

    //移动升级包到插件目录
    public function public_upgrade_2() {
        if (\Libs\System\RBAC::authenticate('upgrade') !== true) {
            $this->errors('您没有该项权限！');
        }
        $data = S('Cloud');
        if (empty($data)) {
            $this->errors('获取不到需要升级的插件信息缓存！');
        }
        $packageUrl = I('get.package');
        if (empty($packageUrl)) {
            $this->errors('package参数为空！');
        }
        //临时目录名
        $tmp = $this->CloudDownload->getTempFile($packageUrl);
        //插件安装目录
        $addonsPath = PROJECT_PATH . 'Addon/' . $data['name'] . '/';
        if ($this->CloudDownload->movedFile($tmp, $addonsPath, $packageUrl) !== true) {
            $this->errors($this->CloudDownload->getError());
        }
        $this->success('移动文件到插件目录成功，等待升级！', U('public_upgrade_3', array('name' => $data['name'])));
    }

    //升级插件
    public function public_upgrade_3() {
        if (\Libs\System\RBAC::authenticate('upgrade') !== true) {
            $this->errors('您没有该项权限！');
        }
        $name = I('get.name');
        S('Cloud', NULL);
        if (D('Addons/Addons')->upgradeAddon($name)) {
            LvyeCMS()->Dir->delDir(PROJECT_PATH . "Addon/{$name}/Upgrade/");
            $this->success('插件升级成功！');
        } else {
            $error = $this->Module->error;
            $this->error($error ? $error : '插件升级失败！');
        }
    }

    //获取插件使用说明
    public function public_explanation() {
        $sign = I('get.sign');
        if (empty($sign)) {
            $this->error('缺少参数！');
        }
        $parameter = array(
            'sign' => $sign
        );
        $data = $this->Cloud->data($parameter)->act('get.addons.explanation');
        if (false === $data) {
            $this->error($this->Cloud->getError());
        }
        $this->ajaxReturn(array('status' => true, 'sign' => $sign, 'data' => $data));
    }

    protected function errors($message = '', $jumpUrl = '', $ajax = false) {
        S('Cloud', NULL);
        $this->error($message, $jumpUrl, $ajax);
    }

}
