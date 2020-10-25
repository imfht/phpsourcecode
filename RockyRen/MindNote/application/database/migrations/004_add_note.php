<?php
/**
 * Created by PhpStorm.
 * User: jay
 * Date: 15-5-13
 * Time: 下午9:22
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_Add_note extends CI_Migration{
  public function up()
  {
    $CI = & get_instance();
    $this->dbforge->add_field(array(
      'id' => array(
        'type' => 'INT',
        'auto_increment' => TRUE,
        'unsigned' => TRUE,
        'constraint' => 5,
      ),
      'name' => array(
        'type' => 'VARCHAR',
        'constraint' => '30',
      ),
      'creator_id' => array(
        'type' => 'INT',
        'unsigned' => TRUE,
        'constraint' => 5
      ),
      'created_at' => array(
        'type' => 'DATETIME'
      ),
      'updated_at' => array(
        'type' => 'DATETIME'
      ),
      'notebook_id' => array(
        'type' => 'INT',
        'unsigned' => TRUE,
        'constraint' => 5
      ),
      'content' => array(
        'type' => 'TEXT',
        'null' => TRUE
      ),
      'type' => array(
        'type' => 'VARCHAR',
        'constraint' => '4',
        'default' => 'note'
      )

      //"CONSTRAINT `fk_site_group` FOREIGN KEY('creator_id') REFERENCES site_user('id') ON DELETE CASCADE ON UPDATE CASCADE"
      //"foreign key('creator_id') references site_user('id') on delete cascade on update cascade"
    ));

    //设置主键
    $this->dbforge->add_key('id', TRUE);
    $attributes = array('ENGINE' => 'InnoDB');
    $this->dbforge->create_table('note', FALSE, $attributes);
    //$this->dbforge->create_table('note');

    //设置外键
    $CI->db->query("alter table site_note add foreign key(creator_id) references site_user(id) on delete cascade on update cascade");
    $CI->db->query("alter table site_note add foreign key(notebook_id) references site_notebook(id) on delete cascade on update cascade");
  }

  public function down(){
    $this->dbforge->drop_table('note');
  }
}