<?php
/**
 * Created by PhpStorm.
 * User: rockyren
 * Date: 15/5/14
 * Time: 22:03
 */

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH.'/libraries/REST_Controller.php';

class Example_api extends REST_Controller{
  public function example_get(){
    $user_id = $this->get('user_id');

    $users = array(
      "1" => array("id" => 1, "username" => "Rocky", "email" => "Rocky@163.com"),
      "2" => array("id" => 2, "username" => "Ben", "email" => "Ben@163.com"),
      "3" => array("id" => 3, "username" => "Lily", "email" => "Lily@163.com"),
    );

    $user = $users[$user_id];

    //$this->response需要return才能测试
    return $this->response($user, 200);
  }

  public function examples_get(){

    $users = array(
      "1" => array("id" => 1, "username" => "Rocky", "email" => "Rocky@163.com"),
      "2" => array("id" => 2, "username" => "Ben", "email" => "Ben@163.com"),
      "3" => array("id" => 3, "username" => "Lily", "email" => "Lily@163.com"),
    );

    return $this->response($users, 200);

  }

  public function example_post(){
    $username = $this->post('username');
    $user = array("id" => 4, "username" => $username, "email" => "Cherry@163.com");

    //$this->response需要return才能测试
    return $this->response($user, 200);
  }

  public function example_put(){
    $username = $this->put('username');
    $userId = $this->get('user_id');

    $user = array("id" => $userId, "username" => $username, "email" => "Rocky@163.com");

    return $this->response($user, 200);
  }

  public function example_delete(){
    $userId = $this->get('user_id');

    if($userId == 2){
      return $this->response(array("status" => "success"), 200);
    }
  }
}