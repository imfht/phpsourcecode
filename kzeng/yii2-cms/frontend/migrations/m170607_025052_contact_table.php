<?php

use yii\db\Migration;

class m170607_025052_contact_table extends Migration
{
    const CONTACT_TABLE = '{{%contact_form}}';

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable(self::CONTACT_TABLE, [
            'id' => $this->primaryKey(),
            'name' => $this->string(64)->notNull(),
            'email' => $this->string(128)->notNull(),
            'subject' => $this->string(256)->notNull(),
            'body' => $this->string(1024),
            'verifycode' => $this->string(32)->notNull(),
            'created_at' => $this->integer(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable(self::CONTACT_TABLE);
    }


    //    public function up()
    //    {
    //
    //    }
    //
    //    public function down()
    //    {
    //        echo "m170607_025052_contact_table cannot be reverted.\n";
    //
    //        return false;
    //    }

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
