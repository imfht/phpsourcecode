<?php
///**
// * Created by PhpStorm.
// * User: rockyren
// * Date: 15/5/12
// * Time: 21:24
// */
//
//
//
//class Notebook_model_test extends PHPUnit_Framework_TestCase{
//
//
//  public function setUp(){
//
//    $this->CI = & get_instance();
//    $this->CI->load->database('testing');
//    $this->CI->load->model('Notebook_model', 'notebook');
//    //$this->obj = $this->CI->user;
//
//  }
//
//  public function test_add_notebook(){
//
//    //测试插入成功
//    $expectedName = true;
//
//    $user_id = 1;
//    $group_id =2;
//    $notebook_name = 'notebook4';
//    $bool = $this->CI->notebook->add_notebook($user_id, $group_id, $notebook_name);
//    //var_dump($group);
//    $this->assertEquals($expectedName, $bool);
//
//
//  }
//
//  /**
//   * @depends test_add_notebook
//   */
//
//  public function test_delete_notebook(){
//
//    //测试删除成功
//    $expectedName = true;
//
//    $user_id = 1;
//    $group_id = 2;
//    $notebook_id = 7;
//    $bool = $this->CI->notebook->delete_notebook($user_id, $group_id, $notebook_id);
//    //var_dump($group);
//    $this->assertEquals($expectedName, $bool);
//
//
//  }
//
//  public function test_change_notebook(){
//
//    //测试修改成功
//    $expectedName = true;
//
//    $user_id = 1;
//    $group_id = 1;
//    $notebook_id = 2;
//    $notebook_name = 'notebook10';
//    $default ='true';
//    $bool = $this->CI->notebook->change_notebook($user_id, $group_id, $notebook_id, $notebook_name );
//    //var_dump($group);
//    $this->assertEquals($expectedName, $bool);
//
//    //还原修改前的状态
//    $expectedName = true;
//
//    $user_id = 1;
//    $group_id = 1;
//    $notebook_id = 2;
//    $notebook_name = 'notebook2';
//    $default ='true';
//    $bool = $this->CI->notebook->change_notebook($user_id, $group_id, $notebook_id, $notebook_name );
//    //var_dump($group);
//    $this->assertEquals($expectedName, $bool);
//
//
//  }
//
//  public function test_get_notebook(){
//
//    //测试查找成功
//    $expectedName = 'notebook1';
//
//    $user_id = 1;
//    $group_id = 2;
//    $notebook_id = 3;
//    $notebook = $this->CI->notebook->get_notebook($user_id, $group_id, $notebook_id);
//    //var_dump($notebook);
//    $this->assertEquals($expectedName, $notebook['name']);
//
//    //测试查找失败
//    $expectedName = null;
//
//    $user_id = 1;
//    $group_id = 2;
//    $notebook_id = 99;
//    $notebook = $this->CI->notebook->get_notebook($user_id, $group_id, $notebook_id);
//    //var_dump($group);
//    $this->assertEquals($expectedName, $notebook);
//
//  }
//
//  public function test_get_notebook_by_name(){
//
//    //测试查找成功
//    $expectedName = 'notebook2';
//
//    $user_id = 1;
//    $group_id = 2;
//    $notebook_name = 'notebook2';
//    $notebook = $this->CI->notebook->get_notebook_by_name($user_id, $group_id, $notebook_name);
//    //var_dump($notebook);
//    $this->assertEquals($expectedName, $notebook['name']);
//
//    //测试查找失败
//    $expectedName = null;
//
//    $user_id = 1;
//    $group_id = 2;
//    $notebook_name = 'notebook4';
//    $notebook = $this->CI->notebook->get_notebook_by_name($user_id, $group_id, $notebook_name);
//    //var_dump($group);
//    $this->assertEquals($expectedName, $notebook);
//
//  }
//
//  public function test_get_notebooks(){
//
//    //测试查找成功
//    $expectedName = 'notebook2';
//
//    $user_id = 1;
//    $group_id = 2;
//    $notebooks = $this->CI->notebook->get_notebooks($user_id, $group_id);
//    //var_dump($notebooks);
//    $this->assertEquals($expectedName, $notebooks[1]['name']);
//
//    //测试查找失败
//    $expectedName = null;
//
//    $user_id = 10;
//    $group_id = 2;
//    $notebooks = $this->CI->notebook->get_notebooks($user_id, $group_id);
//    //var_dump($group);
//    $this->assertEquals($expectedName, $notebooks);
//
//  }
//
//
//  public function duplicate_name_verification(){
//
//    //测试存在重名
//    $expectedName = false;
//
//    $user_id = 1;
//    $group_id =2;
//    $notebook_name = 'notebook1';
//    $bool = $this->CI->notebook->duplicate_name_verification($user_id, $group_id, $notebook_name);
//    //var_dump($group);
//    $this->assertEquals($expectedName, $bool);
//
//    //测试不存在重名
//    $expectedName = true;
//
//    $user_id = 1;
//    $group_id =2;
//    $notebook_name = 'notebook99';
//    $bool = $this->CI->notebook->duplicate_name_verification($user_id, $group_id, $notebook_name);
//    //var_dump($group);
//    $this->assertEquals($expectedName, $bool);
//
//
//  }
//
//
//
//}