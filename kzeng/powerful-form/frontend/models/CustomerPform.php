<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "customer_pform".
 *
 * @property integer $id
 * @property string $pform_uid
 * @property integer $pform_field_id
 * @property string $value
 */
class CustomerPform extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_pform';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pform_uid', 'pform_field_id', 'value'], 'required'],
            [['pform_field_id'], 'integer'],
            [['pform_uid'], 'string', 'max' => 64],
            [['value'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pform_uid' => 'Pform Uid',
            'pform_field_id' => 'Pform Field ID',
            'value' => '字段内容',
        ];
    }
}
