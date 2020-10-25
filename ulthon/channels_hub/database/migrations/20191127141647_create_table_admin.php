<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateTableAdmin extends Migrator
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
        $table = $this->table('admin')->setComment('管理员帐号');

        $table->addColumn(Column::string('account',30)->setComment('帐号'));
        $table->addColumn(Column::string('password',32)->setComment('密码'));
        $table->addColumn(Column::string('salt',6)->setComment('密码盐'));
        $table->addIndex('account');
        $table->create();
    }
}
