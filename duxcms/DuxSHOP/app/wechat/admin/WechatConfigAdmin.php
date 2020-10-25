<?php

/**
 * 微信设置
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\wechat\admin;


class WechatConfigAdmin extends \app\system\admin\SystemAdmin {

    /**
     * 模块信息
     */
    protected function _infoModule(){
        return array(
            'info' => array(
                'name' => '微信设置',
                'description' => '设置微信配置信息',
            ),
        );
    }

    /**
     * 注册设置
     */
    public function index() {
        if(!isPost()) {
            $info = target('WechatConfig')->getConfig();
            $this->assign('info', $info);
            $this->systemDisplay();
        }else{
            if(target('WechatConfig')->saveInfo()){
                $this->success('微信配置成功！');
            }else{
                $this->error('微信配置失败');
            }
        }
    }


}