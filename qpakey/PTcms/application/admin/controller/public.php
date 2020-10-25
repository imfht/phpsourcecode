<?php

class PublicController extends AdminController {

    public function init() {
        $this->config->set('layout', false);
        $this->session->start();
    }

    // 登录操作
    public function loginAction() {

        if ($this->request->ispost()) {
            if (true===$res=$this->model('adminuser')->login()){
                $this->success('登录成功',U('admin.index.index'));
            }else{
                $this->error($res);
            }
        }
    }

    // 退出操作
    public function logoutAction() {
        $this->model('adminuser')->delLoginStatus();
        $this->success('您已经成功退出系统',U('Admin.Index.index'));
    }

    /**
     * 验证码
     */
    public function verifyAction() {
        verify::buildImageVerify(6, 1, 'png', 70, 30);
    }
}