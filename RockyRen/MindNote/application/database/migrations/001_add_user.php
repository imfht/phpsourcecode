<?php
/**
 * Created by PhpStorm.
 * User: rockyren
 * Date: 15/5/12
 * Time: 20:18
 */


defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_Add_user extends CI_Migration {
  public function up()
  {
    $this->dbforge->add_field(array(
      'id' => array(
        'type' => 'INT',
        'auto_increment' => TRUE,
        'unsigned' => TRUE,
        'constraint' => 5
      ),
      'username' => array(
        'type' => 'VARCHAR',
        'constraint' => '30'

      ),
      'password' => array(
        'type' => 'VARCHAR',
        'constraint' => '64'
      ),
      'email' => array(
        'type' => 'VARCHAR',
        'constraint' => '255'
      )
    ));

    //设置主键
    $this->dbforge->add_key('id', TRUE);
    $attributes = array('ENGINE' => 'InnoDB');
    $this->dbforge->create_table('user', FALSE, $attributes);
    //$this->dbforge->create_table('user');
  }

  public function down(){
    $this->dbforge->drop_table('user');
  }
}