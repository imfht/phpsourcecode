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
//class Note_model_test extends PHPUnit_Framework_TestCase{
//
//
//  public function setUp(){
//
//    $this->CI = & get_instance();
//    $this->CI->load->database('testing');
//    $this->CI->load->model('Note_model', 'note');
//    //$this->obj = $this->CI->user;
//
//  }
//
//  public function test_add_note(){
//
//    //测试插入成功
//    $expectedName = true;
//
//    $user_id = 1;
//    $notebook_id = 5;
//    $name = "note3";
//    $content = 'fuck';
//    $bool = $this->CI->note->add_note($user_id, $notebook_id, $name, $content);
//    //var_dump($group);
//    $this->assertEquals($expectedName, $bool);
//
//
//  }
//
//  /**
//   * @depends test_add_note
//   */
//
//  public function test_delete_note(){
//
//    //测试删除成功
//    $expectedName = true;
//
//    $user_id = 1;
//    $notebook_id = 5;
//    $note_id = 11;
//    $bool = $this->CI->note->delete_note($user_id, $notebook_id, $note_id);
//    //var_dump($group);
//    $this->assertEquals($expectedName, $bool);
//
//
//  }
//
//  public function test_change_note(){
//
//    //测试修改成功
//    $expectedName = true;
//
//    $user_id = 1;
//    $notebook_id = 5;
//    $note_id = 9;
//    $name = "note10";
//    $content = 'fuck_you';
//    $bool = $this->CI->note->change_note($user_id, $notebook_id, $note_id, $name, $content);
//    //var_dump($group);
//    $this->assertEquals($expectedName, $bool);
//
//    //还原修改前的状态
//    $expectedName = true;
//
//    $user_id = 1;
//    $notebook_id = 5;
//    $note_id = 9;
//    $name = "note2";
//    $content = 'fuck';
//    $bool = $this->CI->note->change_note($user_id, $notebook_id, $note_id, $name, $content);
//    //var_dump($group);
//    $this->assertEquals($expectedName, $bool);
//
//
//
//  }
//
//  public function test_get_note(){
//
//    //测试查找成功
//    $expectedName = 'note2';
//
//    $user_id = 1;
//    $notebook_id = 5;
//    $note_id = 9;
//    $note = $this->CI->note->get_note($user_id, $notebook_id, $note_id);
//    //var_dump($note);
//    $this->assertEquals($expectedName, $note['name']);
//
//    //测试查找失败
//    $expectedName = null;
//
//    $user_id = 1;
//    $notebook_id = 5;
//    $note_id = 99;
//    $note = $this->CI->note->get_note($user_id, $notebook_id, $note_id);
//    //var_dump($notebook);
//    $this->assertEquals($expectedName, $note['name']);
//
//  }
//
//  public function test_get_note_by_name(){
//
//    //测试查找成功
//    $expectedName = 'note2';
//
//    $user_id = 1;
//    $notebook_id = 5;
//    $note_name = 'note2';
//    $note = $this->CI->note->get_note_by_name($user_id, $notebook_id, $note_name);
//    //var_dump($note);
//    $this->assertEquals($expectedName, $note['name']);
//
//    //测试查找失败
//    $expectedName = null;
//
//    $user_id = 1;
//    $notebook_id = 5;
//    $note_name = 'note22';
//    $note = $this->CI->note->get_note_by_name($user_id, $notebook_id, $note_name);
//    //var_dump($notebook);
//    $this->assertEquals($expectedName, $note['name']);
//
//  }
//
//  public function test_get_notes(){
//
//    //测试查找成功
//    $expectedName = 'note2';
//
//    $user_id = 1;
//    $notebook_id = 5;
//    $notes = $this->CI->note->get_notes($user_id, $notebook_id);
//    //var_dump($notes);
//    $this->assertEquals($expectedName, $notes[1]['name']);
//
//    //测试查找失败
//    $expectedName = null;
//
//    $user_id = 1;
//    $notebook_id = 6;
//    $notes = $this->CI->note->get_notes($user_id, $notebook_id);
//    //var_dump($notebooks);
//    $this->assertEquals($expectedName, $notes[1]['name']);
//
//  }
//
//  public function note_duplicate_name_verification(){
//
//    //测试存在重名
//    $expectedName = false;
//
//    $user_id = 1;
//    $notebook_id = 5;
//    $note_name = 'note2';
//    $bool = $this->CI->note->note_duplicate_name_verification($user_id, $notebook_id, $note_name);
//    //var_dump($group);
//    $this->assertEquals($expectedName, $bool);
//
//    //测试不存在重名
//    $expectedName = true;
//
//    $user_id = 1;
//    $notebook_id = 5;
//    $note_name = 'note3';
//    $bool = $this->CI->note->note_duplicate_name_verification($user_id, $notebook_id, $note_name);
//    //var_dump($group);
//    $this->assertEquals($expectedName, $bool);
//
//
//  }
//
//
//
//
//}