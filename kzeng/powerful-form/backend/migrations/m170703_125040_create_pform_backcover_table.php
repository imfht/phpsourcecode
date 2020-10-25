<?php

use yii\db\Migration;

/**
 * Handles the creation for table `pform_backcover`.
 */
class m170703_125040_create_pform_backcover_table extends Migration
{
    public function up()
    {
        $this->createTable('pform_backcover', [
            'id' => $this->primaryKey(),
            'title' => $this->string(64)->notNull()->comment('标题'),
            'content' => $this->text()->comment('详情'),
            'pform_uid' => $this->string(64)->notNull()->comment('用户表单ID'),
        ]);
    }

    public function down()
    {
        $this->dropTable('pform_backcover');
    }


}
