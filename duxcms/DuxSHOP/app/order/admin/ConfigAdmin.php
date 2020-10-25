<?php

/**
 * 订单设置
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\order\admin;


class ConfigAdmin extends \app\system\admin\SystemAdmin {

    /**
     * 模块信息
     */
    protected function _infoModule(){
        return array(
            'info' => array(
                'name' => '订单设置',
                'description' => '配置订单基本功能',
            ),
        );
    }

    /**
     * 订单设置
     */
    public function index() {
        if(!isPost()) {

            $orderConfig = target('tools/Tools', 'service')->getConfig('order', 'service');
            $info = target('OrderConfig')->getConfig();
            $waybillList = target('order/OrderConfigWaybill')->typeList();

            $this->assign('info', $info);
            $this->assign('waybillList', $waybillList);
            $this->systemDisplay();
        }else{
            if(target('OrderConfig')->saveInfo()){
                $this->success('功能配置成功！');
            }else{
                $this->error('功能配置失败');
            }
        }
    }

    /**
     * 售后信息
     */
    public function info() {
        if(!isPost()) {
            $info = target('OrderConfig')->getConfig();
            $this->assign('info', $info);
            $this->systemDisplay();
        }else{
            if(target('OrderConfig')->saveInfo()){
                $this->success('功能配置成功！');
            }else{
                $this->error('功能配置失败');
            }
        }
    }

    /**
     * 通知设置
     */
    public function notice() {
        if(!isPost()) {
            $info = target('OrderConfig')->getConfig();
            $sendClass = target('tools/ToolsSendConfig')->classList();
            $this->assign('info', $info);
            $this->assign('sendClass', $sendClass);
            $this->systemDisplay();
        }else{
            if(target('OrderConfig')->saveInfo()){
                $this->success('功能配置成功！');
            }else{
                $this->error('功能配置失败');
            }
        }
    }
}