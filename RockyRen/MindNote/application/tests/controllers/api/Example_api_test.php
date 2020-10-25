<?php
/**
 * Created by PhpStorm.
 * User: rockyren
 * Date: 15/5/14
 * Time: 22:07
 */


class Example_api_test extends TestCase{
  //测试example api的get方法
  public function test_example_get(){
    //需要指定format json才能获取json数据
    $userCode = $this->request('GET', ['example_api', 'example_get'], ['user_id' => '1', 'format' => 'json']);
    //由于返回了一个为未解码的json字符串，所以需要解码
    $user = json_decode($userCode, TRUE);

    $expectedUser = array("id" => 1, "username" => "Rocky", "email" => "Rocky@163.com");

    $this->assertEquals($expectedUser, $user);

  }

  public function test_examples_get(){
    $usersCode = $this->request('GET', ['example_api', 'examples_get'], ['format' => 'json']);
    $users = json_decode($usersCode, TRUE);

    $expectedUsers = array(
      "1" => array("id" => 1, "username" => "Rocky", "email" => "Rocky@163.com"),
      "2" => array("id" => 2, "username" => "Ben", "email" => "Ben@163.com"),
      "3" => array("id" => 3, "username" => "Lily", "email" => "Lily@163.com"),
    );

    $this->assertEquals($expectedUsers, $users);

  }

  public function test_example_post(){
    $userCode = $this->request('POST', ['example_api', 'example_post'],
      ['username' => 'Cherry'], ['format' => 'json']);

    $user = json_decode($userCode, TRUE);
    //var_dump($user);

    $expectedUser = array("id" => 4, "username" => "Cherry", "email" => "Cherry@163.com");

    $this->assertEquals($expectedUser, $user);
  }

  public function test_example_put(){
    $userCode = $this->request('PUT', ['example_api', 'example_put'],
      ['username' => 'cc'], ['user_id' => 1 ,'format' => 'json']);

    $user = json_decode($userCode, TRUE);
    //var_dump($user);

    $expectedUser = array("id" => 1, "username" => "cc", "email" => "Rocky@163.com");

    $this->assertEquals($expectedUser, $user);
  }

  public function test_example_delete(){
    $statusCode = $this->request('DELETE', ['example_api', 'example_delete'],
      [], ['user_id' => 2, 'format'=>'json']);
    $status = json_decode($statusCode, TRUE);

    $expectedStatus = array("status" => "success");
    $this->assertEquals($expectedStatus, $status);
  }
}