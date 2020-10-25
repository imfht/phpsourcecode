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
//class Group_model_test extends PHPUnit_Framework_TestCase{
//
//
//  public function setUp(){
//
//    $this->CI = & get_instance();
//    $this->CI->load->database('testing');
//    $this->CI->load->model('Group_model', 'group');
//    //$this->obj = $this->CI->user;
//
//  }
//
//
//
//
//  public function test_add_group(){
//
//    //测试插入成功
//    $expectedName = true;
//
//    $user_id = 1;
//    $group_name = 'group3';
//    $bool = $this->CI->group->add_group($user_id, $group_name);
//    //var_dump($group);
//    $this->assertEquals($expectedName, $bool);
//
//
//  }
//
//  /**
//   * @depends test_add_group
//   */
//
//  public function test_delete_group(){
//
//    //测试删除成功
//    $expectedName = true;
//
//    $user_id = 1;
//    $group_id = 4;
//    $bool = $this->CI->group->delete_group($user_id, $group_id);
//    //var_dump($group);
//    $this->assertEquals($expectedName, $bool);
//
//
//  }
//
//
//
//
//
//  public function test_change_group(){
//
//    //测试修改成功
//    $expectedName = true;
//
//    $user_id = 1;
//    $group_id = 2;
//    $group_name = 'group10';
//    $bool = $this->CI->group->change_group($user_id, $group_id, $group_name);
//    //var_dump($group);
//    $this->assertEquals($expectedName, $bool);
//
//    //还原修改前的状态
//    $expectedName = true;
//
//    $user_id = 1;
//    $group_id = 2;
//    $group_name = 'group2';
//    $bool = $this->CI->group->change_group($user_id, $group_id, $group_name);
//    //var_dump($group);
//    $this->assertEquals($expectedName, $bool);
//
//
//  }
//
//  public function test_get_group(){
//
//    //测试查找成功
//    $expectedName = 1;
//
//    $user_id = 1;
//    $group_id = 1;
//    $group = $this->CI->group->get_group($user_id, $group_id);
//    //var_dump($group);
//    $this->assertEquals($expectedName, $group['id']);
//
//    //测试查找失败
//    $expectedName = null;
//
//    $user_id = 1;
//    $group_id = 99;
//    $group = $this->CI->group->get_group($user_id, $group_id);
//    //var_dump($group);
//    $this->assertEquals($expectedName, $group);
//
//  }
//
//  public function test_get_group_by_name(){
//
//    //测试查找成功
//    $expectedName = 'group1';
//
//    $user_id = 1;
//    $group_name = 'group1';
//    $group = $this->CI->group->get_group_by_name($user_id, $group_name);
//    //var_dump($group);
//    $this->assertEquals($expectedName, $group['name']);
//
//    //测试查找失败
//    $expectedName = null;
//
//    $user_id = 10;
//    $group_name = 'group1';
//    $group = $this->CI->group->get_group_by_name($user_id, $group_name);
//    //var_dump($group);
//    $this->assertEquals($expectedName, $group);
//
//  }
//
//  public function test_get_groups(){
//
//    //测试查找成功
//    $expectedName = 'group2';
//
//    $user_id = 1;
//    $groups = $this->CI->group->get_groups($user_id);
//    //var_dump($groups);
//    $this->assertEquals($expectedName, $groups[1]['name']);
//
//    //测试查找失败
//    $expectedName = null;
//
//    $user_id = 10;
//    $groups = $this->CI->group->get_groups($user_id);
//    //var_dump($group);
//    $this->assertEquals($expectedName, $groups);
//
//  }
//
//
//
//  public function test_group_duplicate_name_verification(){
//
//    //测试存在重名
//    $expectedName = false;
//
//    $user_id = 1;
//    $group_name = 'group1';
//    $bool = $this->CI->group->group_duplicate_name_verification($user_id, $group_name);
//    //var_dump($group);
//    $this->assertEquals($expectedName, $bool);
//
//    //测试不存在重名
//    $expectedName = true;
//
//    $user_id = 1;
//    $group_name = 'group99';
//    $bool = $this->CI->group->group_duplicate_name_verification($user_id, $group_name);
//    //var_dump($group);
//    $this->assertEquals($expectedName, $bool);
//
//
//  }
//
//
//
//
//
//
//}