<?php

use yii\db\Migration;

class m170603_023421_contact_table extends Migration
{
    const CONTACT_TABLE = '{{%contact}}';

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable(self::CONTACT_TABLE, [
            'id' => $this->primaryKey(),
            'name' => $this->string(20)->notNull(),
            'email' => $this->string(50)->notNull(),
            'subject' => $this->string(100)->notNull(),
            'body' => $this->text()->notNull(),
            'created_at' => $this->integer(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable(self::CONTACT_TABLE);
    }
}