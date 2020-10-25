<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Category extends Migrator
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
        $table = $this->table('category', array('engine'=>'InnoDB'));
        $table->addColumn('pid', 'integer', array('signed'=>false,'limit' => 11,'default'=>0,'comment'=>'分类ID'))
            ->addColumn('type', 'integer', array('limit' => 8,'default'=>0,'comment'=>'类型：0普通，1单页，2外链'))
            ->addColumn('name', 'string', array('limit' => 100,'default'=>'','comment'=>'分类名称'))
            ->addColumn('keywords', 'string', array('limit' => 255,'default'=>'','comment'=>'文章关键词'))
            ->addColumn('description', 'string', array('limit' => 255,'default'=>'','comment'=>'文章描述'))
            ->addColumn('content', 'text', array('comment'=>'内容'))
            ->addColumn('url', 'string', array('limit' => 255,'default'=>'','comment'=>'外链地址'))
            ->addColumn('listtemplate', 'string', array('limit' => 100,'default'=>'','comment'=>'列表模版'))
            ->addColumn('contenttemplate', 'string', array('limit' => 100,'default'=>'','comment'=>'内容模版'))
            ->addColumn('o', 'integer', array('limit' => 11,'default'=>0,'comment'=>'排序，越小越靠前'))
            ->addIndex(array('pid','o'))
            ->create();
    }
}
