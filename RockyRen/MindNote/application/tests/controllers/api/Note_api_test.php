<?php
///**
// * Created by PhpStorm.
// * User: wakeup333
// * Date: 15-5-17
// * Time: 下午10:20
// */
//
//class Note_api_test extends TestCase{
//
//  public function setUp(){
//
//    $this->CI = & get_instance();
//
////    $this->CI->load->model('Group_model', 'group');
//    //$this->obj = $this->CI->user;
//
//    $this->CI->load->database('testing');
//    $this->CI->load->library('session');
//
//    //使Group_api获得参数
//    $this->CI->session->set_userdata( array('userId' => 1) );
//
//  }
//
//  public function test_note_post(){
//    /**
//     * 覆盖添加成功的路径
//     */
//
//    //需要指定format json才能获取json数据
//    $noteCode = $this->request('POST', ['Note_api', 'note_post'],
//      ['name' => 'NewAddNote', 'content' => 'Addfuck1'], ['format' => 'json']);
//    //由于返回了一个为未解码的json字符串，所以需要解码
//    $note = json_decode($noteCode, TRUE);
//
//    //var_dump($note);
//
//    $expectedNote = 'NewAddNote';
//
//    $this->assertEquals($expectedNote, $note['name']);
//
//
//    /**
//     * 覆盖获取参数失败的路径
//     */
//
//    //需要指定format json才能获取json数据
//    //不传参数导致获取参数失败
//    $noteCode = $this->request('POST', ['Note_api', 'note_post'],
//      ['name' => 'NewAddNote'], ['format' => 'json']);
//    //由于返回了一个为未解码的json字符串，所以需要解码
//    $note = json_decode($noteCode, TRUE);
//
//    //var_dump($note);
//
//    $expectedNote = '获取参数失败';
//
//    $this->assertEquals($expectedNote, $note['message']);
//
//
//    /**
//     * 覆盖与已有的笔记名重合的路径
//     */
//
//    //需要指定format json才能获取json数据
//    //把添加的 从属于notebook_id为1的笔记本的 笔记名设置成note1
//    $noteCode = $this->request('POST', ['Note_api', 'note_post'],
//      ['name' => 'note1', 'content' => 'Addfuck1'], ['notebook_id' => '1', 'format' => 'json']);
//    //由于返回了一个为未解码的json字符串，所以需要解码
//    $note = json_decode($noteCode, TRUE);
//
//    //var_dump($note);
//
//    $expectedNote = '要添加的笔记与已有笔记名重合';
//
//    $this->assertEquals($expectedNote, $note['message']);
//
//  }
//
//  /**
//   * @depends test_note_post
//   */
//  public function test_note_delete(){
//
//    /**
//     * 覆盖删除成功的路径
//     */
//
//    $noteCode = $this->request('DELETE', ['Note_api', 'note_delete'],
//      [], ['notebook_id' => 1, 'note_id' => '10', 'format'=>'json']);
//    //由于返回了一个为未解码的json字符串，所以需要解码
//    $note = json_decode($noteCode, TRUE);
//
//    //var_dump($note);
//
//    $expectedNote = null;
//
//    $this->assertEquals($expectedNote, $note);
//
//
//    /**
//     * 获取参数失败
//     */
//
//    //不传参数导致获取参数失败
//    $noteCode = $this->request('DELETE', ['Note_api', 'note_delete'],
//      [], ['format'=>'json']);
//    //由于返回了一个为未解码的json字符串，所以需要解码
//    $note = json_decode($noteCode, TRUE);
//
//    //var_dump($note);
//
//    $expectedNote = '获取参数失败';
//
//    $this->assertEquals($expectedNote, $note['message']);
//
//  }
//
//
//  public function test_note_get(){
//
//    /**
//     * 覆盖查找正确的路径
//     */
//
//    //需要指定format json才能获取json数据
//    $noteCode = $this->request('GET', ['Note_api', 'note_get'],
//      ['notebook_id' => 1, 'note_id' => 1, 'format' => 'json']);
//    //由于返回了一个为未解码的json字符串，所以需要解码
//    $note = json_decode($noteCode, TRUE);
//
//    //var_dump($note);
//
//    $expectedNote = 1;
//
//    $this->assertEquals($expectedNote, $note['id']);
//
//
//    /**
//     * 覆盖无法获得参数的路径(不传参数group_id)
//     */
//
//    //需要指定format json才能获取json数据
//    $noteCode = $this->request('GET', ['Note_api', 'note_get'],
//      ['format' => 'json']);
//    //由于返回了一个为未解码的json字符串，所以需要解码
//    $note = json_decode($noteCode, TRUE);
//
//    //var_dump($notebook);
//
//    $expectedNote = '获取参数失败';
//
//    $this->assertEquals($expectedNote, $note['message']);
//
//
//    /**
//     * 覆盖查找失败的路径(传错误参数notebook_id)
//     */
//
//    //需要指定format json才能获取json数据
//    $noteCode = $this->request('GET', ['Note_api', 'note_get'],
//      ['notebook_id' => '1', 'note_id' => '999', 'format' => 'json']);
//    //由于返回了一个为未解码的json字符串，所以需要解码
//    $note = json_decode($noteCode, TRUE);
//
//    //var_dump($notebook);
//
//    $expectedNote = '不能找到该笔记';
//
//    $this->assertEquals($expectedNote, $note['message']);
//
//  }
//
//  public function test_notes_get(){
//
//    /**
//     * 覆盖有参数$notebook_id的路径
//     */
//
//    $noteCode = $this->request('GET', ['Note_api', 'notes_get'],
//      ['notebook_id' => 2, 'format' => 'json']);
//    $note = json_decode($noteCode, TRUE);
//
//    //var_dump($note);
//
//    $expectedNote = 4;
//
//    $this->assertEquals($expectedNote, $note[1]['id']);
//
//
//    /**
//     * 覆盖有参数group_id,无参数notebook_id的路径
//     */
//
//    //需要指定format json才能获取json数据
//    $noteCode = $this->request('GET', ['Note_api', 'notes_get'],
//      ['group_id' => 2, 'format' => 'json']);
//    //由于返回了一个为未解码的json字符串，所以需要解码
//    $note = json_decode($noteCode, TRUE);
//
//    //var_dump($note);
//
//    $expectedNote = 9;
//
//    //$note[2][1]['id']的意思是获得第3个该笔记本组的第3个笔记本里的第二篇笔记
//    $this->assertEquals($expectedNote, $note[4]['id']);
//
//
//    /**
//     * 覆盖参数group_id和参数notebook_id都没有的路径
//     */
//
//    //需要指定format json才能获取json数据
//    //不传参数
//    $noteCode = $this->request('GET', ['Note_api', 'notes_get'],
//      ['format' => 'json']);
//    //由于返回了一个为未解码的json字符串，所以需要解码
//    $note = json_decode($noteCode, TRUE);
//
//    //var_dump($note);
//
//    $expectedNote = '无法获取group_id和notebook_id';
//
//    $this->assertEquals($expectedNote, $note['message']);
//
//
//    /**
//     * 覆盖不能找到笔记的路径
//     */
//
//    $noteCode = $this->request('GET', ['Note_api', 'notes_get'],
//      ['notebook_id' => 999, 'format' => 'json']);
//    $note = json_decode($noteCode, TRUE);
//
//    //var_dump($note);
//
//    $expectedNote = '不能找到笔记';
//
//    $this->assertEquals($expectedNote, $note['message']);
//
//  }
//
//
//
//  public function test_note_put(){
//    /**
//     * 覆盖修改成功的路径
//     */
//    $noteCode = $this->request('PUT', ['Note_api', 'note_put'],
//      ['name' => 'cc', 'content' => 'Changefuck'], ['notebook_id' => 2 , 'note_id' => 3, 'format' => 'json']);
//
//    $note = json_decode($noteCode, TRUE);
//    //var_dump($note);
//
//    $expectedNote = 'cc';
//
//    $this->assertEquals($expectedNote, $note['name']);
//
//    /*
//     * 修改回原来的数据
//     */
//    $noteCode = $this->request('PUT', ['Note_api', 'note_put'],
//      ['name' => 'note1', 'content' => 'fuck3'], ['notebook_id' => 2, 'note_id' => 3, 'format' => 'json']);
//
//    $note = json_decode($noteCode, TRUE);
//    //var_dump($note);
//
//    $expectedNote = 'note1';
//
//    $this->assertEquals($expectedNote, $note['name']);
//
//
//    /**
//     * 覆盖获取参数失败的路径()
//     */
//
//    $noteCode = $this->request('PUT', ['Note_api', 'note_put'],
//      ['name' => 'cc'], ['format' => 'json']);
//
//    $note = json_decode($noteCode, TRUE);
//    //var_dump($note);
//
//    $expectedNote = '获取参数失败';
//
//    $this->assertEquals($expectedNote, $note['message']);
//
//
//    /**
//     * 覆盖与已有的笔记本组名重合的路径
//     */
//
//    //把note1的名字改成note2的名字导致重名
//    $noteCode = $this->request('PUT', ['Note_api', 'note_put'],
//      ['name' => 'note2', 'content' => 'fuck3'], ['notebook_id' => 2, 'note_id' => 3, 'format' => 'json']);
//
//    $note = json_decode($noteCode, TRUE);
//    //var_dump($note);
//
//    $expectedNote = '修改成的笔记名与已有笔记名重合';
//
//    $this->assertEquals($expectedNote, $note['message']);
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