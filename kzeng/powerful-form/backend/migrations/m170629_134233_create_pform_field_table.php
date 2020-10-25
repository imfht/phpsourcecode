<?php

use yii\db\Migration;

/**
 * Handles the creation for table `pform_field`.
 */
class m170629_134233_create_pform_field_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('pform_field', [
            'id' => $this->primaryKey(),
            'title' => $this->string(128)->notNull()->comment('字段名称'),
            'type' =>  $this->integer()->notNull()->comment('字段类型'),
            'value' => $this->string(255)->notNull()->comment('取值范围'),
            'placeholder' => $this->string(255)->comment('提示语'),
            'sort' =>  $this->integer()->defaultValue(255)->comment('排序'),
            'pform_uid' => $this->string(64)->notNull()->comment('用户表单ID'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('pform_field');
    }
}
