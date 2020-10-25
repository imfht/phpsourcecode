<?php
/**
 * Created by PhpStorm.
 * User: rockyren
 * Date: 15/5/12
 * Time: 21:12
 */

class UserSeeder extends Seeder {
  private $table = 'user';



  public function run(){

    //清空表
    //$this->db->truncate($this->table);

    //增加用户
    $password = hash('sha256', 'admin1');
    $data = array(
      'username' => 'admin1',
      'password' => $password,
      'email' => '1234@qq.com'
    );

    $this->db->insert($this->table, $data);

    $password = hash('sha256', 'admin2');
    $data = array(
      'username' => 'admin2',
      'password' => $password,
      'email' => '1234@qq.com'
    );

    $this->db->insert($this->table, $data);





  }


}

