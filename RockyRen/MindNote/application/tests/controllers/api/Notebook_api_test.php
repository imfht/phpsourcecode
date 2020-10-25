<?php
///**
// * Created by PhpStorm.
// * User: wakeup333
// * Date: 15-5-17
// * Time: 下午8:06
// */
//
//class Notebook_api_test extends TestCase{
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
//
//
//  public function test_notebook_post(){
//    /**
//     * 覆盖添加成功的路径
//     */
//
//    //需要指定format json才能获取json数据
//    $notebookCode = $this->request('POST', ['Notebook_api', 'notebook_post'],
//      ['name' => 'NewAddNotebook'], ['group_id' => '1', 'format' => 'json']);
//    //由于返回了一个为未解码的json字符串，所以需要解码
//    $notebook = json_decode($notebookCode, TRUE);
//
//    //var_dump($notebook);
//
//    $expectedNotebook = 'NewAddNotebook';
//
//    $this->assertEquals($expectedNotebook, $notebook['name']);
//
//
//    /**
//     * 覆盖获取参数失败的路径
//     */
//
//    //需要指定format json才能获取json数据
//    $notebookCode = $this->request('POST', ['Notebook_api', 'notebook_post'],
//      ['name' => 'NewAddNotebook'], ['format' => 'json']);
//    //由于返回了一个为未解码的json字符串，所以需要解码
//    $notebook = json_decode($notebookCode, TRUE);
//
//    //var_dump($notebook);
//
//    $expectedNotebook = '获取参数失败';
//
//    $this->assertEquals($expectedNotebook, $notebook['message']);
//
//
//    /**
//     * 覆盖与已有的笔记本名重合的路径
//     */
//
//    //需要指定format json才能获取json数据
//    //把添加的笔记本名设置成notebook2
//    $notebookCode = $this->request('POST', ['Notebook_api', 'notebook_post'],
//      ['name' => 'notebook2'], ['group_id' =>'2', 'format' => 'json']);
//    //由于返回了一个为未解码的json字符串，所以需要解码
//    $notebook = json_decode($notebookCode, TRUE);
//
//    //var_dump($notebook);
//
//    $expectedNotebook = '与已有的笔记本名重合';
//
//    $this->assertEquals($expectedNotebook, $notebook['message']);
//
//  }
//
//  /**
//   * @depends test_notebook_post
//   */
//  public function test_notebook_delete(){
//
//    /**
//     * 覆盖删除成功的路径
//     */
//
//    $notebookCode = $this->request('DELETE', ['Notebook_api', 'notebook_delete'],
//      [], ['group_id' => '1','notebook_id' => '6' , 'format'=>'json']);
//    $notebook = json_decode($notebookCode, TRUE);
//
//    //var_dump($notebook);
//
//    $expectedNotebook = null;
//
//    $this->assertEquals($expectedNotebook, $notebook);
//
//
//    /**
//     * 获取参数失败
//     */
//
//    //不传参数group_id
//    $notebookCode = $this->request('DELETE', ['Notebook_api', 'notebook_delete'],
//      [], ['format'=>'json']);
//    $notebook = json_decode($notebookCode, TRUE);
//
//    //var_dump($notebook);
//
//    $expectedNotebook = '获取参数失败';
//
//    $this->assertEquals($expectedNotebook, $notebook['message']);
//
//
//    /**
//     * 覆盖删除失败（删除默认笔记本）的路径
//     */
//
//    $notebookCode = $this->request('DELETE', ['Notebook_api', 'notebook_delete'],
//      [], ['notebook_id' => '1' , 'format'=>'json']);
//    $notebook = json_decode($notebookCode, TRUE);
//
//    //var_dump($notebook);
//
//    $expectedNotebook = '删除失败';
//
//    $this->assertEquals($expectedNotebook, $notebook['message']);
//
//  }
//
//
//  public function test_notebook_get(){
//
//    /**
//     * 覆盖查找正确的路径
//     */
//
//    //需要指定format json才能获取json数据
//    $notebookCode = $this->request('GET', ['Notebook_api', 'notebook_get'],
//      ['group_id' => '1', 'notebook_id' => '2' , 'format' => 'json']);
//    //由于返回了一个为未解码的json字符串，所以需要解码
//    $notebook = json_decode($notebookCode, TRUE);
//
//    //var_dump($notebook);
//
//    $expectedNotebook = 2;
//
//    $this->assertEquals($expectedNotebook, $notebook['id']);
//
//
//    /**
//     * 覆盖无法获得参数的路径(不传参数group_id)
//     */
//
//    //需要指定format json才能获取json数据
//    $notebookCode = $this->request('GET', ['Notebook_api', 'notebook_get'],
//      ['format' => 'json']);
//    //由于返回了一个为未解码的json字符串，所以需要解码
//    $notebook = json_decode($notebookCode, TRUE);
//
//    //var_dump($notebook);
//
//    $expectedNotebook = '获取参数失败';
//
//    $this->assertEquals($expectedNotebook, $notebook['message']);
//
//
//    /**
//     * 覆盖查找失败的路径(传错误参数notebook_id)
//     */
//
//    //需要指定format json才能获取json数据
//    $notebookCode = $this->request('GET', ['Notebook_api', 'notebook_get'],
//      ['group_id' => '1', 'notebook_id' => '999', 'format' => 'json']);
//    //由于返回了一个为未解码的json字符串，所以需要解码
//    $notebook = json_decode($notebookCode, TRUE);
//
//    //var_dump($notebook);
//
//    $expectedNotebook = '不能找到该笔记本';
//
//    $this->assertEquals($expectedNotebook, $notebook['message']);
//
//  }
//
//  public function test_notebooks_get(){
//
//    /**
//     * 覆盖查找正确的路径
//     */
//
//    $notebookCode = $this->request('GET', ['Notebook_api', 'notebooks_get'],
//      ['group_id' => '2', 'format' => 'json']);
//    $notebook = json_decode($notebookCode, TRUE);
//
////    var_dump($notebook);
//
//    $expectedNotebook = 4;
//
//    $this->assertEquals($expectedNotebook, $notebook[1]['id']);
//
//    /**
//     * 覆盖无法获得参数的路径
//     */
//
//    //需要指定format json才能获取json数据
//    $notebookCode = $this->request('GET', ['Notebook_api', 'notebooks_get'],
//      ['format' => 'json']);
//    //由于返回了一个为未解码的json字符串，所以需要解码
//    $notebook = json_decode($notebookCode, TRUE);
//
//    //var_dump($notebook);
//
//    $expectedNotebook = '获取参数失败';
//
//    $this->assertEquals($expectedNotebook, $notebook['message']);
//
//    /**
//     * 覆盖查找失败的路径
//     */
//
//    //需要指定format json才能获取json数据
//    //传一个错误的group_id
//    $notebookCode = $this->request('GET', ['Notebook_api', 'notebooks_get'],
//      ['group_id' => '999', 'format' => 'json']);
//    //由于返回了一个为未解码的json字符串，所以需要解码
//    $notebook = json_decode($notebookCode, TRUE);
//
//    //var_dump($notebook);
//
//    $expectedNotebook = '不能找到笔记本';
//
//    $this->assertEquals($expectedNotebook, $notebook['message']);
//
//  }
//
//  public function test_group_put(){
//    /**
//     * 覆盖修改默认笔记本成功的路径
//     */
//    $notebookCode = $this->request('PUT', ['Notebook_api', 'notebook_put'],
//      ['name' => 'notebook2', 'default' => 'true'], ['group_id' => 2 , 'notebook_id' => 4, 'format' => 'json']);
//
//    $notebook = json_decode($notebookCode, TRUE);
//    //var_dump($notebook);
//
//    $expectedNotebook = 'notebook2';
//
//    $this->assertEquals($expectedNotebook, $notebook['name']);
//
//
//    /**
//     * 覆盖修改笔记本名成功的路径
//     */
//    $notebookCode = $this->request('PUT', ['Notebook_api', 'notebook_put'],
//      ['name' => 'cc', 'default' => 'false'], ['group_id' => 2 , 'notebook_id' => 5, 'format' => 'json']);
//
//    $notebook = json_decode($notebookCode, TRUE);
//    //var_dump($notebook);
//
//    $expectedNotebook = 'cc';
//
//    $this->assertEquals($expectedNotebook, $notebook['name']);
//
//    /*
//     * 修改回原来的数据
//     */
//    $notebookCode = $this->request('PUT', ['Notebook_api', 'notebook_put'],
//      ['name' => 'notebook3', 'default' => 'false'], ['group_id' => 2, 'notebook_id' => 5, 'format' => 'json']);
//
//    $notebook = json_decode($notebookCode, TRUE);
//    //var_dump($notebook);
//
//    $expectedNotebook = 'notebook3';
//
//    $this->assertEquals($expectedNotebook, $notebook['name']);
//
//
//    /**
//     * 覆盖获取参数失败的路径()
//     */
//
//    $notebookCode = $this->request('PUT', ['Notebook_api', 'notebook_put'],
//      ['name' => 'cc', 'default' => 'false'], ['format' => 'json']);
//
//    $notebook = json_decode($notebookCode, TRUE);
//    //var_dump($notebook);
//
//    $expectedNotebook = '获取参数失败';
//
//    $this->assertEquals($expectedNotebook, $notebook['message']);
//
//
//    /**
//     * 覆盖与已有的笔记本组名重合的路径
//     */
//
//    //把group1的名字改成group2的名字导致重名
//    $notebookCode = $this->request('PUT', ['Notebook_api', 'notebook_put'],
//      ['name' => 'notebook1', 'default' => 'false'], ['group_id' => 2, 'notebook_id' => '4',  'format' => 'json']);
//
//    $notebook = json_decode($notebookCode, TRUE);
//    //var_dump($notebook);
//
//    $expectedNotebook = '与已有的笔记本名重合';
//
//    $this->assertEquals($expectedNotebook, $notebook['message']);
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
//
//
//}