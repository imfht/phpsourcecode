<?php
///**
// * Created by PhpStorm.
// * User: wakeup333
// * Date: 15-5-16
// * Time: 下午8:32
// */
//
//class Group_api_test extends TestCase{
//
//  public function setUp(){
//
//    $this->CI = & get_instance();
//    $this->CI->load->database('testing');
////    $this->CI->load->model('Group_model', 'group');
//    //$this->obj = $this->CI->user;
//
//    $this->CI->load->library('session');
//
//    //使Group_api获得参数
//    $this->CI->session->set_userdata( array('userId' => 1) );
//
//  }
//
//  public function test_group_post(){
//    /**
//     * 覆盖添加成功的路径
//     */
//
//    //需要指定format json才能获取json数据
//    $groupCode = $this->request('POST', ['Group_api', 'group_post'],
//      ['name' => 'NewAdd'], ['format' => 'json']);
//    //由于返回了一个为未解码的json字符串，所以需要解码
//    $group = json_decode($groupCode, TRUE);
//
//    //var_dump($group);
//
//    $expectedGroup = 'NewAdd';
//
//    $this->assertEquals($expectedGroup, $group['name']);
//
//
//    /**
//     * 覆盖获取参数失败的路径
//     */
//
//    //需要指定format json才能获取json数据
//    $groupCode = $this->request('POST', ['Group_api', 'group_post'],
//      [], ['format' => 'json']);
//    //由于返回了一个为未解码的json字符串，所以需要解码
//    $group = json_decode($groupCode, TRUE);
//
//    //var_dump($group);
//
//    $expectedGroup = '获取参数失败';
//
//    $this->assertEquals($expectedGroup, $group['message']);
//
//
//    /**
//     * 覆盖与已有的笔记本组名重合的路径
//     */
//
//    //需要指定format json才能获取json数据
//    //把添加的笔记本组名设置成group2
//    $groupCode = $this->request('POST', ['Group_api', 'group_post'],
//      ['name' => 'group2'], ['format' => 'json']);
//    //由于返回了一个为未解码的json字符串，所以需要解码
//    $group = json_decode($groupCode, TRUE);
//
//    //var_dump($group);
//
//    $expectedGroup = '与已有的笔记本组名重合';
//
//    $this->assertEquals($expectedGroup, $group['message']);
//
//  }
//
//  /**
//   * @depends test_group_post
//   */
//  public function test_group_delete(){
//
//    /**
//     * 覆盖删除成功的路径
//     */
//
//    $groupCode = $this->request('DELETE', ['Group_api', 'group_delete'],
//      [], ['group_id' => 3, 'format'=>'json']);
//    $group = json_decode($groupCode, TRUE);
//
//    //var_dump($group);
//
//    $expectedGroup = null;
//
//    $this->assertEquals($expectedGroup, $group);
//
//
//    /**
//     * 获取参数失败
//     */
//
//    //不传参数group_id
//    $groupCode = $this->request('DELETE', ['Group_api', 'group_delete'],
//      [], ['format'=>'json']);
//    $group = json_decode($groupCode, TRUE);
//
//    //var_dump($group);
//
//    $expectedGroup = '获取参数失败';
//
//    $this->assertEquals($expectedGroup, $group['message']);
//
//  }
//
//  //测试example api的get方法
//  public function test_group_get(){
//
//    /**
//     * 覆盖查找正确的路径
//     */
//
//    //需要指定format json才能获取json数据
//    $groupCode = $this->request('GET', ['Group_api', 'group_get'], ['group_id' => '1', 'format' => 'json']);
//    //由于返回了一个为未解码的json字符串，所以需要解码
//    $group = json_decode($groupCode, TRUE);
//
//    //var_dump($group);
//
//    $expectedGroup = 1;
//
//    $this->assertEquals($expectedGroup, $group['id']);
//
//
//    /**
//     * 覆盖无法获得参数的路径(不传参数group_id)
//     */
//
//    //需要指定format json才能获取json数据
//    $groupCode = $this->request('GET', ['Group_api', 'group_get'], ['format' => 'json']);
//    //由于返回了一个为未解码的json字符串，所以需要解码
//    $group = json_decode($groupCode, TRUE);
//
//    //var_dump($group);
//
//    $expectedGroup = 'fail';
//
//    $this->assertEquals($expectedGroup, $group['status']);
//
//
//    /**
//     * 覆盖查找失败的路径(传错误参数group_id)
//     */
//
//    //需要指定format json才能获取json数据
//    $groupCode = $this->request('GET', ['Group_api', 'group_get'], ['group_id' => '999', 'format' => 'json']);
//    //由于返回了一个为未解码的json字符串，所以需要解码
//    $group = json_decode($groupCode, TRUE);
//
//    //var_dump($group);
//
//    $expectedGroup = 'fail';
//
//    $this->assertEquals($expectedGroup, $group['status']);
//
//  }
//
//  public function test_groups_get(){
//
//    /**
//     * 覆盖查找正确的路径
//     */
//
//    $groupsCode = $this->request('GET', ['Group_api', 'groups_get'], ['format' => 'json']);
//    $groups = json_decode($groupsCode, TRUE);
//
//    $expectedGroups = 2;
//
//    $this->assertEquals($expectedGroups, $groups[1]['id']);
//
//    /**
//     * 覆盖无法获得参数的路径
//     */
//
//    //需要指定format json才能获取json数据
//    $groupCode = $this->request('GET', ['Group_api', 'group_get'], ['format' => 'json']);
//    //由于返回了一个为未解码的json字符串，所以需要解码
//    $group = json_decode($groupCode, TRUE);
//
//    //var_dump($group);
//
//    $expectedGroup = 'fail';
//
//    $this->assertEquals($expectedGroup, $group['status']);
//
//    /**
//     * 覆盖查找失败的路径
//     */
//
//    //需要指定format json才能获取json数据
//    $groupCode = $this->request('GET', ['Group_api', 'group_get'], ['format' => 'json']);
//    //由于返回了一个为未解码的json字符串，所以需要解码
//    $group = json_decode($groupCode, TRUE);
//
//    //var_dump($group);
//
//    $expectedGroup = 'fail';
//
//    $this->assertEquals($expectedGroup, $group['status']);
//
//  }
//
//
//
//  public function test_group_put(){
//    /**
//     * 覆盖修改成功的路径
//     */
//    $groupCode = $this->request('PUT', ['Group_api', 'group_put'],
//      ['name' => 'cc'], ['group_id' => 1 ,'format' => 'json']);
//
//    $group = json_decode($groupCode, TRUE);
//    //var_dump($group);
//
//    $expectedGroup = 'cc';
//
//    $this->assertEquals($expectedGroup, $group['name']);
//
//    /*
//     * 修改回原来的数据
//     */
//    $groupCode = $this->request('PUT', ['Group_api', 'group_put'],
//      ['name' => 'group1'], ['group_id' => 1 ,'format' => 'json']);
//
//    $group = json_decode($groupCode, TRUE);
//    //var_dump($group);
//
//    $expectedGroup = 'group1';
//
//    $this->assertEquals($expectedGroup, $group['name']);
//
//
//    /**
//     * 覆盖获取参数失败的路径(不传参数group_id)
//     */
//
//    $groupCode = $this->request('PUT', ['Group_api', 'group_put'],
//      ['name' => 'cc'], ['format' => 'json']);
//
//    $group = json_decode($groupCode, TRUE);
//    //var_dump($group);
//
//    $expectedGroup = '获取参数失败';
//
//    $this->assertEquals($expectedGroup, $group['message']);
//
//
//    /**
//     * 覆盖与已有的笔记本组名重合的路径
//     */
//
//    //把group1的名字改成group2的名字导致重名
//    $groupCode = $this->request('PUT', ['Group_api', 'group_put'],
//      ['name' => 'group2'], ['group_id' => 1, 'format' => 'json']);
//
//    $group = json_decode($groupCode, TRUE);
//    //var_dump($group);
//
//    $expectedGroup = '与已有的笔记本组名重合';
//
//    $this->assertEquals($expectedGroup, $group['message']);
//
//
////    /**
////     * 覆盖修改失败的路径
////     */
////
////    $groupCode = $this->request('PUT', ['Group_api', 'group_put'],
////      ['name' => 'cc'], ['group_id' => 999, 'format' => 'json']);
////
////    $group = json_decode($groupCode, TRUE);
////    //var_dump($group);
////
////    $expectedGroup = '修改失败';
////
////    $this->assertEquals($expectedGroup, $group['message']);
//
//  }
//
//}