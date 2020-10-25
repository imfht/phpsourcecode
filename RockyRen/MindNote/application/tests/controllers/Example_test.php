<?php
/**
 * Created by PhpStorm.
 * User: rockyren
 * Date: 15/5/13
 * Time: 10:07
 */

class Example_test extends TestCase
{
  public function testGetOne(){
    $one = $this->request('GET', ['example', 'getOne']);
    $this->assertEquals($one, 1);
  }


  public function test_group_get(){

    //$hello = $this->call();
    //var_dump($hello);
    //$example = new Example();
    //$name = $this->request('GET', ['group_api', 'group_get'], ['name' => 'rocky']);

    //$name = $this->request('POST', ['group_api', 'group_post'], ['name' => 'rocky']);

    //$name = $this->request('PUT', ['group_api', 'group_put'], ['name' => 'rocky']);

    //$name = $this->request('DELETE', ['group_api', 'group_delete'], ['name' => 'rocky']);

    //$name = $this->request('GET', ['Group_api', 'group_get'], ['name' => 'rocky']);

    //$this->assertEquals($name, 'rocky');
  }
}