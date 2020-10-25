<?php

use yii\db\Migration;

/**
 * Handles the creation for table `customer_pform`.
 */
class m170629_134329_create_customer_pform_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('customer_pform', [
            'id' => $this->primaryKey(),
            'pform_uid' => $this->string(64)->notNull()->comment('用户表单ID'),
            'pform_field_id' =>  $this->integer()->notNull()->comment('字段ID'),
            'value' => $this->string(255)->comment('字段值'),
            'customer_pform_uid' => $this->string(64),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('customer_pform');
    }
}
