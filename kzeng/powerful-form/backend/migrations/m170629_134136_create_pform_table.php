<?php

use yii\db\Migration;

/**
 * Handles the creation for table `pform`.
 */
class m170629_134136_create_pform_table extends Migration
{
    /**
     * @inheritdoc
     */

    public function up()
    {
        $this->createTable('pform', [
            'id' => $this->primaryKey(),
            'uid' => $this->string(64)->notNull()->comment('唯一编码'),
            'title' => $this->string(255)->notNull()->comment('表单名称'),
            'created_at' =>  $this->integer()->notNull()->comment('创建时间'),
            'updated_at' =>  $this->integer()->notNull()->comment('更新时间'),
            'description' => $this->string()->comment('简要描述'),
            'detail' => $this->text()->comment('详情'),
            'form_img_url' => $this->string()->comment('页头图片'),
            'user_id' => $this->integer()->notNull()->comment('创建者'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('pform');
    }
}
