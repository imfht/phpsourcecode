<?php

/**
 * 会员设置
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\member\admin;


class MemberConfigAdmin extends \app\system\admin\SystemAdmin {

    /**
     * 模块信息
     */
    protected function _infoModule(){
        return array(
            'info' => array(
                'name' => '会员设置',
                'description' => '设置会员中心信息',
            ),
        );
    }

    /**
     * 站点设置
     */
    public function index() {
        if(!isPost()) {
            $fieldList = target('MemberInfo')->loadList([], 0, 'info_id asc');
            $this->assign('fieldList', $fieldList);
            $this->systemDisplay();
        }else{
            if(target('MemberInfo')->saveInfo()){
                $this->success('会员信息配置成功！');
            }else{
                $this->error('会员信息配置失败');
            }
        }
    }

    /**
     * 注册设置
     */
    public function reg() {
        if(!isPost()) {
            $info = target('MemberConfig')->getConfig();
            $this->assign('info', $info);
            $this->assign('roleList', target('member/MemberRole')->loadList());
            $this->systemDisplay();
        }else{
            if(target('MemberConfig')->saveInfo()){
                $this->success('会员配置成功！');
            }else{
                $this->error('会员配置失败');
            }
        }
    }

    /**
     * 财务设置
     */
    public function pay() {
        if(!isPost()) {
            $info = target('MemberConfig')->getConfig();
            $this->assign('info', $info);
            $this->systemDisplay();
        }else{
            if(target('MemberConfig')->saveInfo()){
                $this->success('会员配置成功！');
            }else{
                $this->error('会员配置失败');
            }
        }
    }

    /**
     * 验证码设置
     */
    public function verify() {
        if(!isPost()) {
            $info = target('MemberConfig')->getConfig();
            $this->assign('info', $info);
            $this->systemDisplay();
        }else{
            if(target('MemberConfig')->saveInfo()){
                $this->success('会员配置成功！');
            }else{
                $this->error('会员配置失败');
            }
        }
    }

    /**
     * 通知设置
     */
    public function notice() {
        if(!isPost()) {
            $info = target('MemberConfig')->getConfig();
            $sendClass = target('tools/ToolsSendConfig')->classList();
            $this->assign('info', $info);
            $this->assign('sendClass', $sendClass);
            $this->systemDisplay();
        }else{
            $config = target('MemberConfig')->getConfig();
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
                    $status = target('MemberConfig')->data($data)->where($where)->update();
                }else {
                    $data['name'] = $key;
                    $status = target('MemberConfig')->data($data)->insert();
                }
                if(!$status){
                    $this->error('功能配置失败');
                }
            }
            $this->success('功能配置成功！');
        }
    }

}