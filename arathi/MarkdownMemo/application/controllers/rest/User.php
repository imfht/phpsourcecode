<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: Arathi
 * Date: 2015/7/26
 * Time: 19:51
 */
class User extends CI_Controller{
    public function __construct(){
        parent::__construct();
         $this->load->model('user_model');
    }

    public function login(){
        $username = $this->input->get('username');
        $hashedPWD = $this->input->get('password');
        $loginResults = $this->user_model->login($username,$hashedPWD);
        $jsonArray = array();
        if ($loginResults == false)
        {
            //登陆失败
            $jsonArray = array(
                'logged' => false,
                'username' => $username,
                'uid' => 0
            );
        }
        else{
            //登陆成功
            $loginResult = $loginResults[0];
            $uid = $loginResult['uid'];
            $jsonArray = array(
                'logged' => true,
                'username' => $username,
                'uid' => $uid
            );
        }
        $json = json_encode($jsonArray);
        $json = str_replace("\r\n", "\n", $json);
        $json = str_replace("\r", "\n", $json);
        $json = str_replace("\n", "\\n", $json);
        //$this->output->enable_profiler(TRUE);
        $this->output
            ->set_content_type('application/json')
            ->set_output($json)
            ->_display();
        exit;
    }
}
