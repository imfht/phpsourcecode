<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateTableHttpChannel extends Migrator
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
        $table = $this->table('http_channel')->setComment('http隧道');
        $table->addColumn(Column::tinyInteger('type')->setDefault(1)->setSigned(false)->setComment('类型，1：tcp，2：udp'));
        $table->addColumn(Column::bigInteger('client_id')->setSigned(false)->setComment('客户端id'));
        $table->addColumn(Column::string('domain',100)->setComment('代理的域名'));
        $table->addColumn(Column::string('local_target_ip',20)->setComment('代理的客户端网络ip'));
        $table->addColumn(Column::integer('local_target_port')->setSigned(false)->setComment('代理的客户端网络的端口'));
        $table->addColumn(Column::integer('create_time')->setSigned(false)->setComment('添加时间')->setDefault(0));
        $table->addColumn(Column::integer('update_time')->setSigned(false)->setComment('更新时间')->setDefault(0));
        $table->addColumn(Column::integer('delete_time')->setSigned(false)->setComment('删除时间')->setDefault(0));
        $table->addColumn(Column::tinyInteger('status')->setSigned(false)->setComment('状态：0:正常，1：禁用'));
        $table->addColumn(Column::string('name',20)->setComment('端口名称'));
        $table->addColumn(Column::string('comment',300)->setComment('注释'));
        $table->addIndex('type');
        $table->addIndex('status');
        $table->addIndex('delete_time');
        $table->addIndex('client_id');
        $table->create();
    }
}
