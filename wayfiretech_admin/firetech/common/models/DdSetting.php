<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-07-29 01:55:09
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-29 01:55:11
 */
 

namespace common\models;

use Yii;

/**
 * This is the model class for table "dd_setting".
 *
 * @property string $key
 * @property string $describe
 * @property string $values
 * @property int $wxapp_id
 * @property int $update_time
 */
class DdSetting extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%setting}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['key', 'values', 'wxapp_id'], 'required'],
            [['values'], 'string'],
            [['wxapp_id', 'update_time'], 'integer'],
            [['key'], 'string', 'max' => 30],
            [['describe'], 'string', 'max' => 255],
            [['key', 'wxapp_id'], 'unique', 'targetAttribute' => ['key', 'wxapp_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'key' => 'Key',
            'describe' => 'Describe',
            'values' => 'Values',
            'wxapp_id' => 'Wxapp ID',
            'update_time' => 'Update Time',
        ];
    }
}
