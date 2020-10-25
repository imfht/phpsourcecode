<?php
/**
 * Created by PhpStorm.
 * User: rockyren
 * Date: 15/5/12
 * Time: 20:42
 */

class Tool extends CI_Controller {
  public function migrate( $dbConfig = 'default', $version = null){

    $this->load->database($dbConfig);
    $this->load->library('migration');


    if($version === null){
      if ($this->migration->latest() === FALSE)
      {
        show_error($this->migration->error_string());
      }
    }else{
      if ($this->migration->version($version) === FALSE)
      {
        show_error($this->migration->error_string());
      }
    }

    //var_dump($this->db->database);
  }

  public function seed($dbConfig='default'){
    $this->load->database($dbConfig);

    $this->load->library('Seeder');
    $this->seeder->call('UserSeeder');
    $this->seeder->call('GroupSeeder');
    $this->seeder->call('NotebookSeeder');
    $this->seeder->call("NoteSeeder");
  }

  /*
   * 刷新数据库
   */

  public function refresh($dbConfig='default'){
    $this->migrate($dbConfig, 0);
    $this->migrate($dbConfig);
    $this->seed($dbConfig);
  }

}