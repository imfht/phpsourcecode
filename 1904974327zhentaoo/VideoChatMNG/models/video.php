<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "video".
 *
 * @property string $name
 * @property string $password
 * @property string $creator
 * @property string $online_number
 * @property integer $id
 */
class video extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'video';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'password', 'creator', 'online_number'], 'required'],
            [['name', 'password', 'creator', 'online_number'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Name',
            'password' => 'Password',
            'creator' => 'Creator',
            'online_number' => 'Online Number',
            'id' => 'ID',
        ];
    }
}
