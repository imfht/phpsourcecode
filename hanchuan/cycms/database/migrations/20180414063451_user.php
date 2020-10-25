<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class User extends Migrator
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
        $table = $this->table('user', array('id'=>'uid','engine'=>'InnoDB'));
        $table->addColumn('ugid', 'integer', array('signed'=>false,'limit' => 11,'default'=>0,'comment'=>'用户组ID'))
            ->addColumn('username', 'string', array('limit' => 100,'default'=>'','comment'=>'用户名'))
            ->addColumn('password', 'string', array('limit' => 32,'default'=>'','comment'=>'用户密码'))
            ->addColumn('avatar', 'string', array('limit' => 255,'default'=>'','comment'=>'用户头像'))
            ->addColumn('sex', 'integer', array('signed'=>false,'limit' => MysqlAdapter::INT_TINY,'default'=>0,'comment'=>'姓别：0保密，1男，2女'))
            ->addColumn('birthday', 'integer', array('signed'=>false,'limit' => 10,'default'=>0,'comment'=>'生日'))
            ->addColumn('tel', 'string', array('limit' => 20,'default'=>'','comment'=>'电话号码'))
            ->addColumn('qq', 'string', array('limit' => 20,'default'=>'','comment'=>'QQ'))
            ->addColumn('email', 'string', array('limit' => 255,'default'=>'','comment'=>'电子邮箱'))
            ->addColumn('status', 'boolean', array('limit' => 1,'default'=>1,'comment'=>'用户状态，0禁用，1启用'))
            ->addColumn('identifier', 'string', array('limit' => 32,'default'=>'','comment'=>'登录标识'))
            ->addColumn('token', 'string', array('limit' => 32,'default'=>'','comment'=>'登录Token'))
            ->addColumn('salt', 'string', array('limit' => 10,'default'=>'','comment'=>'密码盐'))
            ->addColumn('skin', 'string', array('limit' => 20,'default'=>'no-skin','comment'=>'皮肤'))
            ->addColumn('create_time', 'integer', array('signed'=>false,'limit' => 10,'default'=>0,'comment'=>'创建时间'))
            ->addIndex(array('username'), array('unique' => true))
            ->create();

        $user = array(
                'uid'    => 1,
                'ugid'    => 1,
                'username'  => 'admin',
                'password'  => 'e62e76cff8e27165bbf2eb429506da72',
                'avatar'  => '',
                'sex'  => 0,
                'birthday'  => time(),
                'tel'  => '13800138000',
                'qq'  => '10000',
                'email'  => '10000@qq.com',
                'status'  => 1,
                'identifier'  => '',
                'token'  => '',
                'salt'  => '',
                'skin'  => 'no-skin',
                'create_time'  => time(),
            );

        $table = $this->table('user');
        $table->insert($user);
        $table->saveData();
    }

    public function down()
    {
        $this->dropTable('user');
    }
}
