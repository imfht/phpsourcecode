<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Log extends Migrator
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
    public function change()
    {
        $table = $this->table('log', array('engine'=>'InnoDB'));
        $table->addColumn('username', 'string', array('limit' => 100,'default'=>'','comment'=>'用户名'))
            ->addColumn('log', 'text', array('comment'=>'日志'))
            ->addColumn('ip', 'string', array('limit' => 16,'default'=>'','comment'=>'IP'))
            ->addColumn('t', 'integer', array('signed'=>false,'limit' => 10,'default'=>0,'comment'=>'时间'))
            ->create();
    }
}
