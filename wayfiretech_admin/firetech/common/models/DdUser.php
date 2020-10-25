<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-04-13 16:25:31
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-10 19:32:33
 */


namespace common\models;

use Yii;

/**
 * This is the model class for table "dd_user".
 *
 * @property int $user_id
 * @property string $open_id
 * @property string $nickName
 * @property string $avatarUrl
 * @property int $gender
 * @property string $country
 * @property string $province
 * @property string $city
 * @property int $address_id
 * @property int $wxapp_id
 * @property int $create_time
 * @property int $update_time
 */
class DdUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['gender', 'address_id', 'wxapp_id', 'create_time', 'update_time'], 'integer'],
            [['open_id', 'nickName', 'avatarUrl'], 'string', 'max' => 255],
            [['country', 'province', 'city'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'open_id' => 'Open ID',
            'nickName' => 'Nick Name',
            'avatarUrl' => 'Avatar Url',
            'gender' => 'Gender',
            'country' => 'Country',
            'province' => 'Province',
            'city' => 'City',
            'address_id' => 'Address ID',
            'wxapp_id' => 'Wxapp ID',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
