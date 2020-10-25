<?php

class Captcha{
  public function __construct(){
    $this->CI = & get_instance();
    $this->CI->load->helper('captcha');
    //$this->CI->load->library('session');
  }

  /**
   * 生成验证码图片
   */
  public function build(){
    $vals = array(
      'img_path' => './public/captcha/',
      'img_url' => base_url().'public/captcha/',
      'img_width' => '150',
      'img_height' => '34',
      'expiration' => 60,
      'word_length' => 4
     );

    //生成验证码图片
    $captcha = create_captcha($vals);


    //设置sesion
    $this->CI->session->set_userdata('captcha', $captcha['word']);

    //返回验证码
    return $captcha['image'];
   }

  public function isValidCaptcha($word){
    if(strcasecmp( $word, $this->CI->session->userdata('captcha') ) == 0){
      return true;
    }else{
      return false;
    }
  }
  private $CI;   //引用CI中的超级对象
}