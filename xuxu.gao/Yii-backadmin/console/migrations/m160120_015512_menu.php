<?php

use yii\db\Schema;
use yii\db\Migration;
use console\models\MenuSeeder;
use console\models\UserSeeder;
use console\models\RoleSeeder;
class m160120_015512_menu extends Migration
{
    public function up()
    {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%menu}}', [

            'id'            => Schema::TYPE_PK,
            'parent_id'     => Schema::TYPE_INTEGER,
            'name'          => Schema::TYPE_STRING . '(100) NOT NULL DEFAULT "" COMMENT "菜单名称"',
            'slug'          => Schema::TYPE_STRING . '(255) NOT NULL DEFAULT "" COMMENT "权限标记"',
            'url'           => Schema::TYPE_STRING . '(255) NOT NULL DEFAULT "" COMMENT "菜单访问地址"',
            'description'   => Schema::TYPE_STRING . '(255) NOT NULL DEFAULT "" COMMENT "菜单描述"',
            'created_at'    => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at'    => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);

        MenuSeeder::initMenu();

    }

    public function down()
    {
        $this->dropTable('{{%menu}}');
    }
    
    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }
    
    public function safeDown()
    {
    }
    */
}
