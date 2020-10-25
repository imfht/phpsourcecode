<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-12 17:49:24
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-29 01:56:11
 */


namespace common\models;

use Yii;

/**
 * This is the model class for table "dd_user_address".
 *
 * @property int $address_id
 * @property string $name
 * @property string $phone
 * @property int $province_id
 * @property int $city_id
 * @property int $region_id
 * @property string $detail
 * @property int $user_id
 * @property int $wxapp_id
 * @property int $create_time
 * @property int $update_time
 */
class DdUserAddress extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_address}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['province_id', 'city_id', 'region_id', 'user_id', 'wxapp_id', 'create_time', 'update_time'], 'integer'],
            [['name'], 'string', 'max' => 30],
            [['phone'], 'string', 'max' => 20],
            [['detail'], 'string', 'max' => 255],
        ];
    }


    /* 获取分类 */
    public function getRegions()
    {
        return $this->hasOne(DdRegion::className(), ['id' => 'region_id']);
    }

      /* 获取分类 */
      public function getProvince()
      {
          return $this->hasOne(DdRegion::className(), ['id' => 'province_id']);
      }

        /* 获取分类 */
        public function getCity()
        {
            return $this->hasOne(DdRegion::className(), ['id' => 'city_id']);
        }

      /* 获取分类 */
      public function getRegion()
      {
          return $this->hasOne(DdRegion::className(), ['id' => 'region_id']);
      }

    /**
     * 行为
     */
    public function behaviors()
    {
        /*自动添加创建和修改时间*/
        return [
            [
                'class' => \common\behaviors\SaveBehavior::className(),
                'updatedAttribute' => 'create_time',
                'createdAttribute' => 'update_time',
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'address_id' => 'Address ID',
            'name' => 'Name',
            'phone' => 'Phone',
            'province_id' => 'Province ID',
            'city_id' => 'City ID',
            'region_id' => 'Region ID',
            'detail' => 'Detail',
            'user_id' => 'User ID',
            'wxapp_id' => 'Wxapp ID',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
