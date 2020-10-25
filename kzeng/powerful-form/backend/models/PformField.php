<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "pform_field".
 *
 * @property integer $id
 * @property string $title
 * @property integer $type
 * @property string $value
 * @property string $placeholder
 * @property integer $sort
 * @property string $pform_uid
 */
class PformField extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pform_field';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'type', 'value', 'placeholder', 'pform_uid'], 'required'],
            [['type', 'sort'], 'integer'],
            [['title'], 'string', 'max' => 32],
            [['value', 'placeholder'], 'string', 'max' => 255],
            [['pform_uid'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '字段ID',
            'title' => '字段名称',
            'type' => '类型',
            'value' => '取值范围',
            'placeholder' => '提示语',
            'sort' => '排序',
            'pform_uid' => '用户表单ID',
        ];
    }
}
