<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "manager".
 *
 * @property string $password
 * @property string $name
 * @property integer $id
 */
class Manager extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'manager';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password', 'name'], 'required'],
            [['password', 'name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'password' => 'Password',
            'name' => 'Name',
            'id' => 'ID',
        ];
    }
}
