<?php

namespace backend\models;

use Yii;


/**
 * This is the model class for table "pform_backcover".
 *
 * @property integer $id
 * @property string $content
 * @property string $pform_uid
 */
class PformBackcover extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pform_backcover';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content', 'pform_uid', 'title'], 'required'],
            [['content'], 'string'],
            [['pform_uid', 'title'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'content' => '内容',
            'pform_uid' => 'Pform Uid',
        ];
    }
}
