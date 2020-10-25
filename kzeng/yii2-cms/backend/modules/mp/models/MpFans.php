<?php
namespace backend\modules\mp\models;

use yeesoft\helpers\AuthHelper;
use yeesoft\helpers\YeeHelper;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

class MpFans extends \yeesoft\db\ActiveRecord {
    public static function tableName() {
        return '{{%mp_fans}}';
    }

    public function rules() {
        return [
            [['openid'], 'required'],
            [['subscribe', 'subscribe_time', 'sex', 'groupid'], 'integer'],
            [['city', 'country', 'province', 'language'], 'string', 'max' => 32],
            [['openid'], 'string', 'max' => 64],
            [['nickname', 'headimgurl', 'remark'], 'string', 'max' => 255]
        ];
    }

    public function attributeLabels() {
        return [
            'id' => '标识',
            'headimgurl' => '头像',
            'nickname' => '昵称',
            'openid' => 'OpenID',
            'city' => '城市',
            'province' => '省份',
            'country' => '国家',
            'sex' => '性别',
            'groupid' => '分组标识',
            'language' => '语言',
            'subscribe_time' => '关注时间',

            // 'id' => Yii::t('yee', 'ID'),
            // 'nickname' => Yii::t('beesoft', 'Nickname'),
            // 'openid' => Yii::t('beesoft', 'Openid'),
            // 'city' => Yii::t('beesoft', 'City'),
            // 'privince' => Yii::t('beesoft', 'Province'),
            // 'country' => Yii::t('beesoft', 'Country'),
            // 'sex' => Yii::t('beesoft', 'Sex'),
            // 'groupid' => Yii::t('beesoft', 'GroupId'),
            // 'language' => Yii::t('beesoft', 'Language'),
        ];
    }
}