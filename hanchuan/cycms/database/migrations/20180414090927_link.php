<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Link extends Migrator
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
        $table = $this->table('link', array('engine'=>'InnoDB'));
        $table->addColumn('title', 'string', array('limit' => 255,'default'=>'','comment'=>'名称'))
            ->addColumn('url', 'string', array('limit' => 255,'default'=>'','comment'=>'地址'))
            ->addColumn('logo', 'string', array('limit' => 255,'default'=>'','comment'=>'站标'))
            ->addColumn('ip', 'string', array('limit' => 16,'default'=>'','comment'=>'IP'))
            ->addColumn('o', 'integer', array('limit' => 11,'default'=>0,'comment'=>'排序'))
            ->create();
    }
}
