<?php

/**
 * 站点设置
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\site\admin;


class ConfigAdmin extends \app\system\admin\SystemAdmin {

    /**
     * 模块信息
     */
    protected function _infoModule(){
        return array(
            'info' => array(
                'name' => '站点配置',
                'description' => '配置站点基本信息',
            ),
        );
    }

    /**
     * 站点信息
     */
    public function index() {
        if(!isPost()) {
            $info = target('SiteConfig')->getConfig();
            $this->assign('info', $info);
            $this->systemDisplay();
        }else{
            if(target('SiteConfig')->saveInfo()){
                $this->success('信息配置成功！');
            }else{
                $this->error('信息配置失败');
            }
        }
    }

    /**
     * 站点设置
     */
    public function config() {
        if(!isPost()) {
            $info = target('SiteConfig')->getConfig();
            $this->assign('info', $info);
            $this->systemDisplay();
        }else{
            if(target('SiteConfig')->saveInfo()){
                $this->success('站点配置成功！');
            }else{
                $this->error('站点配置失败');
            }
        }
    }

    /**
     * 模板设置
     */
    public function tpl(){
        if(!isPost()) {
            $info = target('SiteConfig')->getConfig();
            $this->assign('info', $info);
            $this->assign('dirs', target('SiteTpl')->tplDir());
            $this->systemDisplay();
        }else{
            if(target('SiteConfig')->saveInfo()){
                $this->success('模板配置成功！');
            }else{
                $this->error('模板配置失败');
            }
        }
    }
}