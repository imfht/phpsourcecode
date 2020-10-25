<?php

/**
 * 订单设置
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\sale\admin;


class ConfigAdmin extends \app\system\admin\SystemAdmin {

    /**
     * 模块信息
     */
    protected function _infoModule(){
        return array(
            'info' => array(
                'name' => '推广设置',
                'description' => '配置推广基本功能',
            ),
        );
    }

    /**
     * 基本设置
     */
    public function index() {
        if(!isPost()) {
            $info = target('SaleConfig')->getConfig();
            $this->assign('info', $info);
            $this->systemDisplay();
        }else{
            if(target('SaleConfig')->saveInfo()){
                $this->success('功能配置成功！');
            }else{
                $this->error('功能配置失败');
            }
        }
    }

    /**
     * 申请设置
     */
    public function apply() {
        if(!isPost()) {
            $info = target('SaleConfig')->getConfig();
            $info['apply_where'] = unserialize($info['apply_where']);
            $this->assign('info', $info);
            $this->systemDisplay();
        }else{
            $_POST['apply_where'] = serialize($_POST['apply_where']);
            if(target('SaleConfig')->saveInfo()){
                $this->success('功能配置成功！');
            }else{
                $this->error('功能配置失败');
            }
        }
    }

    /**
     * 升级设置
     */
    public function level() {
        if(!isPost()) {
            $info = target('SaleConfig')->getConfig();
            $this->assign('info', $info);
            $this->systemDisplay();
        }else{
            if(target('SaleConfig')->saveInfo()){
                $this->success('功能配置成功！');
            }else{
                $this->error('功能配置失败');
            }
        }
    }

    /**
     * 二维码设置
     */
    public function qrcode() {
        if(!isPost()) {
            $info = target('SaleConfig')->getConfig();
            $this->assign('info', $info);
            $this->systemDisplay();
        }else{
            if(target('SaleConfig')->saveInfo()){
                $this->success('功能配置成功！');
            }else{
                $this->error('功能配置失败');
            }
        }
    }

    /**
     * 消息设置
     */
    public function notice() {
        if(!isPost()) {
            $info = target('SaleConfig')->getConfig();
            $sendClass = target('tools/ToolsSendConfig')->classList();
            $this->assign('info', $info);
            $this->assign('sendClass', $sendClass);
            $this->systemDisplay();
        }else{

            $config = target('SaleConfig')->getConfig();
            foreach ($_POST as $key => $value) {
                $where = array();
                $where['name'] = $key;
                $data = array();
                if(is_array($value)) {
                    $data['content'] = serialize($value);
                }else{
                    $data['content'] = html_in($value);
                }
                if(isset($config[$key])) {
                    $status = target('SaleConfig')->data($data)->where($where)->update();
                }else {
                    $data['name'] = $key;
                    $status = target('SaleConfig')->data($data)->insert();
                }
                if(!$status){
                    $this->error('功能配置失败');
                }
            }
            $this->success('功能配置成功！');
        }
    }


}