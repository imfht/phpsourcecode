<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contact".
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $subject
 * @property string $body
 * @property integer $created_at
 */
class Contact extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contact';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'email', 'subject', 'body'], 'required'],
            [['body'], 'string'],
            [['created_at'], 'integer'],
            [['name'], 'string', 'max' => 20],
            [['email'], 'string', 'max' => 50],
            [['subject'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '标识',
            'name' => '姓名',
            'email' => '电子邮箱',
            'subject' => '信件标题',
            'body' => '信件内容',
            'created_at' => '创建时间',
            'tel'   => '手机号码',
            'idcode'    => '身份证号',
            'address'   => '户口地址',
            'enclosure' => '附件上传',
            'open'  => '公开意愿',
        ];
    }
}
