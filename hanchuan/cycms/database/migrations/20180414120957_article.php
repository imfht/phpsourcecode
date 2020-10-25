<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Article extends Migrator
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
        $table = $this->table('article', array('id'=>'aid','engine'=>'InnoDB'));
        $table->addColumn('cid', 'integer', array('signed'=>false,'limit' => 11,'default'=>0,'comment'=>'分类ID'))
            ->addColumn('title', 'string', array('limit' => 255,'default'=>'','comment'=>'文章标题'))
            ->addColumn('keywords', 'string', array('limit' => 255,'default'=>'','comment'=>'文章关键词'))
            ->addColumn('description', 'string', array('limit' => 255,'default'=>'','comment'=>'文章描述'))
            ->addColumn('image', 'string', array('limit' => 255,'default'=>'','comment'=>'缩略图'))
            ->addColumn('content', 'text', array('comment'=>'内容'))
            ->addColumn('stick', 'boolean', array('limit' => 1,'default'=>0,'comment'=>'0普通，1置顶'))
            ->addColumn('t', 'integer', array('signed'=>false,'limit' => 10,'default'=>0,'comment'=>'发布时间'))
            ->addColumn('n', 'integer', array('signed'=>false,'limit' => 11,'default'=>0,'comment'=>'点击'))
            ->addIndex(array('cid'))
            ->create();
    }
}
