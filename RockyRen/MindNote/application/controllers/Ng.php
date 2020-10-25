<?php
/**
 * Created by PhpStorm.
 * User: rockyren
 * Date: 15/5/16
 * Time: 10:32
 */

class Ng extends CI_Controller{
  function __construct(){
    parent::__construct();
    $this->load->helper('url');
    $this->load->library('session');
  }

  public function index(){

    if($this->isLogined()){
      $data['username'] = $this->session->userdata('username');
      $this->load->view('ng', $data);

    }else{
      redirect('home');
    }

  }

  /**
   * 检查是否已经登录(是否已经设置session)
   */
  private function isLogined()
  {
    return  $this->session->userdata('userId') && $this->session->userdata('username');

  }
}