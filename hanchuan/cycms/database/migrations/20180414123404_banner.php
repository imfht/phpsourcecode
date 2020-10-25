<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Banner extends Migrator
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
        $table = $this->table('banner', array('engine'=>'InnoDB'));
        $table->addColumn('title', 'string', array('limit' => 255,'default'=>'','comment'=>'标题'))
            ->addColumn('url', 'string', array('limit' => 255,'default'=>'','comment'=>'链接地址'))
            ->addColumn('image', 'string', array('limit' => 255,'default'=>'','comment'=>'Banner图'))
            ->addColumn('o', 'integer', array('limit' => 11,'default'=>0,'comment'=>'排序，越小越靠前'))
            ->addColumn('status', 'boolean', array('limit' => 1,'default'=>1,'comment'=>'0禁止，1显示'))
            ->addIndex(array('o'))
            ->create();
    }
}
