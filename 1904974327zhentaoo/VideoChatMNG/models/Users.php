<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property string $user_name
 * @property string $password
 * @property string $email
 * @property integer $id
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_name', 'password', 'email'], 'required'],
            [['user_name', 'password', 'email'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_name' => 'User Name',
            'password' => 'Password',
            'email' => 'Email',
            'id' => 'ID',
        ];
    }
}
