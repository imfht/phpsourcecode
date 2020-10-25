<?php
/**
 * Created by PhpStorm.
 * User: rockyren
 * Date: 15/5/12
 * Time: 21:12
 */

class NoteSeeder extends Seeder {
  private $table = 'note';



  public function run(){

    //清空表
    //$this->db->truncate($this->table);

    //增加笔记
    $date = date('Y-m-d H:i:s', time());
    $data = array(
      'name' => 'note1' ,
      'creator_id' => 1 ,
      'created_at' => $date,
      'updated_at' => $date,
      'notebook_id' => 1,
      'content' => 'fuck1'
    );

    $this->db->insert($this->table, $data);

    $data = array(
      'name' => 'note2' ,
      'creator_id' => 1 ,
      'created_at' => $date,
      'updated_at' => $date,
      'notebook_id' => 1,
      'content' => 'fuck2'
    );

    $this->db->insert($this->table, $data);

    $data = array(
      'name' => 'note1' ,
      'creator_id' => 1 ,
      'created_at' => $date,
      'updated_at' => $date,
      'notebook_id' => 2,
      'content' => 'fuck3'
    );

    $this->db->insert($this->table, $data);

    $data = array(
      'name' => 'note2' ,
      'creator_id' => 1 ,
      'created_at' => $date,
      'updated_at' => $date,
      'notebook_id' => 2,
      'content' => 'fuck4'
    );

    $this->db->insert($this->table, $data);

    $data = array(
      'name' => 'note1' ,
      'creator_id' => 1 ,
      'created_at' => $date,
      'updated_at' => $date,
      'notebook_id' => 3,
      'content' => 'fuck5'
    );

    $this->db->insert($this->table, $data);

    $data = array(
      'name' => 'note2' ,
      'creator_id' => 1 ,
      'created_at' => $date,
      'updated_at' => $date,
      'notebook_id' => 3,
      'content' => 'fuck6'
    );

    $this->db->insert($this->table, $data);

    $data = array(
      'name' => 'note1' ,
      'creator_id' => 1 ,
      'created_at' => $date,
      'updated_at' => $date,
      'notebook_id' => 4,
      'content' => 'fuck7'
    );

    $this->db->insert($this->table, $data);

    $data = array(
      'name' => 'note1' ,
      'creator_id' => 1 ,
      'created_at' => $date,
      'updated_at' => $date,
      'notebook_id' => 5,
      'content' => 'fuck8'
    );

    $this->db->insert($this->table, $data);

    $data = array(
      'name' => 'note2' ,
      'creator_id' => 1 ,
      'created_at' => $date,
      'updated_at' => $date,
      'notebook_id' => 5,
      'content' => 'fuck9'
    );

    $this->db->insert($this->table, $data);



  }


}

