<?php

class globalAction extends backendAction {

 public function _initialize() {
        parent::_initialize();
        $this->_mod = D('global');
    }

    public function index() {
        $type = $this->_get('type', 'trim', 'index');

        $this->display($type);
    }
    
    public function user() {
        $this->display();
    }

    public function edit() {
        $setting = $this->_post('setting', ',');
        foreach ($setting as $key => $val) {
            $val = is_array($val) ? serialize($val) : $val;
            $this->_mod->where(array('name' => $key))->save(array('data' => $val));
        }
        $type = $this->_post('type', 'trim', 'index');
        $this->success(L('operation_success'));
    }

    public function ajax_mail_test() {
        // print_r($_POST);die;

        $email = $this->_get('email', 'trim');
        !$email && $this->ajaxReturn(0);
        //发送
        $mailer = mailer::get_instance();
        if ($mailer->send($email, L('send_test_email_subject'), L('send_test_email_body'))) {
            $this->ajaxReturn(1);
        } else {
            $this->ajaxReturn(0);
        }
    }
}