<?php

use think\migration\Migrator;
use think\migration\db\Column;

class UserGroup extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function up()
    {
        $table = $this->table('user_group', array('engine'=>'InnoDB'));
        $table->addColumn('title', 'string', array('limit' => 100,'default'=>'','comment'=>'组名'))
            ->addColumn('status', 'boolean', array('limit' => 1,'default'=>1,'comment'=>'组状态，0禁用，1启用'))
            ->addColumn('auth', 'text', array('comment'=>'组权限'))
            ->create();

        $user_group = array(
                'id'    => 1,
                'title'    => '超级管理员',
                'status'  => 1,
                'auth'  => implode(',', range(1, 1000)),
            );

        $table = $this->table('user_group');
        $table->insert($user_group);
        $table->saveData();
    }

    public function down()
    {
        $this->dropTable('user_group');
    }
}
