<?php
/**
 * Created by PhpStorm.
 * User: wakeup333
 * Date: 15-4-27
 * Time: 下午11:00
 */
  class Auth_verification
  {
    public function __construct()
    {
      $this->CI = & get_instance();

      $this->CI->load->library('form_validation');


      $this->CI->form_validation->set_message('required', '%s不能为空!');
      $this->CI->form_validation->set_message('min_length', '%s不能少于6位!');
      $this->CI->form_validation->set_message('max_length', '%s不能多于30位!');
      $this->CI->form_validation->set_message('is_unique', '此%s已存在!');
      $this->CI->form_validation->set_message('matches', '%s和密码确认不同!');
      $this->CI->form_validation->set_message('check_user', '用户不存在或密码错误');
      $this->CI->form_validation->set_message('check_cap', '验证码错误');

      //设置错误定界符
      $this->CI->form_validation->set_error_delimiters
      ('<div class="alert alert-danger alert-dismissible authority-error"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>', '</div>');
    }

    /**
     * 登录表单验证
     */
    public function login_verification()
    {
      //使用一个数组设置表单验证规则
      $login_rules = array(
        array(
          'field' => 'username',
          'label' => '用户名',
          'rules' => 'required|min_length[6]|max_length[30]|callback_check_user'
        ),
        array(
          'field' => 'password',
          'label' => '密码',
          'rules' => 'required|min_length[6]|max_length[30]'
        )
      );

      $this->CI->form_validation->set_rules($login_rules);

      return $this->CI->form_validation->run();
    }

    /**
     * 注册表单验证
     */

    public function signin_verification()
    {
      //使用一个数组设置表单验证规则
      $signin_rules = array(
        array(
          'field' => 'username',
          'label' => '用户名',
          'rules' => 'required|min_length[6]|max_length[30]|is_unique[user.username]'
        ),
        array(
          'field' => 'password',
          'label' => '密码',
          'rules' => 'required|matches[password_confirmation]|min_length[6]|max_length[30]'
        ),
        array(
          'field' => 'password_confirmation',
          'label' => '密码确认',
          'rules' => 'required'
        ),
        array(
          'field' => 'captcha',
          'label' => '验证码',
          'rules' => 'required|callback_check_cap'
        )
      );


      $this->CI->form_validation->set_rules($signin_rules);

      return $this->CI->form_validation->run();
    }


    private $CI;
  }