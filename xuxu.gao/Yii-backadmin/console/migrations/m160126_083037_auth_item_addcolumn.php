<?php

use yii\db\Schema;
use yii\db\Migration;
use console\models\UserSeeder;
use console\models\RoleSeeder;
class m160126_083037_auth_item_addcolumn extends Migration
{
    public function up()
    {
        $this->addColumn('{{%auth_item}}',
                        'typename',
                        Schema::TYPE_STRING . '(100) NOT NULL DEFAULT "" COMMENT "权限类型名称"'
               );

        RoleSeeder::initRole();
        RoleSeeder::initPermission();
        UserSeeder::initUser();

    }

    public function down()
    {
        $this->dropTable('{{%auth_item}}');
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
