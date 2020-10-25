<?php
/**
 * Created by PhpStorm.
 * User: jay
 * Date: 15-5-13
 * Time: 下午8:30
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_Add_group extends CI_Migration{
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
      )
    ));

    //设置主键
    $this->dbforge->add_key('id', TRUE);
    $attributes = array('ENGINE' => 'InnoDB');
    $this->dbforge->create_table('group', FALSE, $attributes);
    //$this->dbforge->create_table('group');

    //设置外键
    //alter table site_group add constraint 'fk_site_group' foreign key(creator_id) references site_user(id) ,
    //$CI->db->query("ALTER TABLE site_group ADD FOREIGN KEY('creator_id') REFERENCES site_user('id') ON DELETE CASCADE ON UPDATE CASCADE");

    //$CI->db->query("ALTER TABLE site_group ADD FOREIGN KEY('creator_id') REFERENCES site_user('id') ON DELETE CASCADE ON UPDATE CASCADE");

    $CI->db->query("alter table site_group add foreign key(creator_id) references site_user(id) on delete cascade on update cascade");
  }

  public function down(){
    $this->dbforge->drop_table('group');
  }
}