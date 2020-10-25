<?php
/**
 * Created by PhpStorm.
 * User: rockyren
 * Date: 15/5/12
 * Time: 21:12
 */

class NotebookSeeder extends Seeder {
  private $table = 'notebook';



  public function run(){

    //清空表
    //$this->db->truncate($this->table);

    //增加笔记本
    $date = date('Y-m-d H:i:s', time());
    $data = array(
      'name' => 'notebook1' ,
      'creator_id' => 1 ,
      'created_at' => $date,
      'updated_at' => $date,
      'group_id' => null,
      'default' => true
    );

    $this->db->insert($this->table, $data);

    $data = array(
      'name' => 'notebook2' ,
      'creator_id' => 1 ,
      'created_at' => $date,
      'updated_at' => $date,
      'group_id' => 1
    );

    $this->db->insert($this->table, $data);

    $data = array(
      'name' => 'notebook1' ,
      'creator_id' => 1 ,
      'created_at' => $date,
      'updated_at' => $date,
      'group_id' => 2
    );

    $this->db->insert($this->table, $data);

    $data = array(
      'name' => 'notebook2' ,
      'creator_id' => 1 ,
      'created_at' => $date,
      'updated_at' => $date,
      'group_id' => 2
    );

    $this->db->insert($this->table, $data);

    $data = array(
      'name' => 'notebook3' ,
      'creator_id' => 1 ,
      'created_at' => $date,
      'updated_at' => $date,
      'group_id' => 2
    );

    $this->db->insert($this->table, $data);




  }




}

