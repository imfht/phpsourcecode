<?php

use think\migration\Migrator;
use think\migration\db\Column;

class UserAuth extends Migrator
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
        $table = $this->table('user_auth', array('engine'=>'InnoDB'));
        $table->addColumn('uid', 'integer', array('signed'=>false,'limit' => 11,'default'=>0,'comment'=>'用户ID'))
            ->create();
    }

    public function down()
    {
        $this->dropTable('user_auth');
    }
}
