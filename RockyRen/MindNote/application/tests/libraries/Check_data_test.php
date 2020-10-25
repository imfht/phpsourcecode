<?php
/**
 * Created by PhpStorm.
 * User: jay
 * Date: 15-5-24
 * Time: 上午11:34
 */

class Group_model_test extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->CI = &get_instance();
    $this->CI->load->library('check_data');
  }


  public function test_check_need_data()
  {
    //测试返回true的情况
    $expectedName = true;
    $need_data = array();
    $need_data['test1'] = 'test1';
    $need_data['test2'] = 'test2';

    $bool = $this->CI->check_data->check_need_data($need_data);

    $this->assertEquals($expectedName, $bool);

    //测试返回false的情况
    $expectedName = false;
    $need_data = array();
    $need_data['test1'] = 'test1';
    $need_data['test2'] = 'test2';
    $need_data['test3'] = false;

    $bool = $this->CI->check_data->check_need_data($need_data);

    $this->assertEquals($expectedName, $bool);
  }

  public function test_check_optional_data()
  {
    //测试返回true的情况
    $expectedName = true;
    $optional_data = array();
    $optional_data['test1'] = 'test1';
    $optional_data['test2'] = false;

    $bool = $this->CI->check_data->check_optional_data($optional_data);

    $this->assertEquals($expectedName, $bool);

    //测试返回false的情况
    $expectedName = false;
    $optional_data = array();
    $optional_data['test1'] = false;
    $optional_data['test2'] = false;

    $bool = $this->CI->check_data->check_optional_data($optional_data);

    $this->assertEquals($expectedName, $bool);
  }
}