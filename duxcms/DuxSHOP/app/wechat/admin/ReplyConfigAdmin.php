<?php

/**
 * 回复设置
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\wechat\admin;


class ReplyConfigAdmin extends \app\system\admin\SystemAdmin {

    /**
     * 模块信息
     */
    protected function _infoModule(){
        return array(
            'info' => array(
                'name' => '回复设置',
                'description' => '设置回复基本信息',
            ),
        );
    }

    /**
     * 回复设置
     */
    public function index() {
        if(!isPost()) {
            $info = target('WechatReplyConfig')->getConfig();
            $this->assign('info', $info);
            $this->systemDisplay();
        }else{
            if(target('WechatReplyConfig')->saveInfo()){
                $this->success('回复设置配置成功！');
            }else{
                $this->error('回复设置配置失败');
            }
        }
    }

}