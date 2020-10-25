<?php
/**
 * Created by PhpStorm.
 * User: rockyren
 * Date: 15/5/12
 * Time: 21:12
 */

class GroupSeeder extends Seeder {
  private $table = 'group';



  public function run(){

    //清空表
    //$this->db->truncate($this->table);

    //增加笔记本组
    $date = date('Y-m-d H:i:s', time());
    $data = array(
      'name' => 'group1' ,
      'creator_id' => 1 ,
      'created_at' => $date,
      'updated_at' => $date
    );

    $this->db->insert($this->table, $data);

    $data = array(
      'name' => 'group2' ,
      'creator_id' => 1 ,
      'created_at' => $date,
      'updated_at' => $date
    );

    $this->db->insert($this->table, $data);



  }


}

