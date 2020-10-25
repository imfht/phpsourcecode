<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-07-29 01:55:55
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-29 01:55:58
 */
 

namespace common\models;

use Yii;

/**
 * This is the model class for table "dd_upload_group".
 *
 * @property int $group_id
 * @property string $group_type
 * @property string $group_name
 * @property int $sort
 * @property int $wxapp_id
 * @property int $create_time
 * @property int $update_time
 */
class DdUploadGroup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%upload_group}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sort', 'wxapp_id', 'create_time', 'update_time'], 'integer'],
            [['group_type'], 'string', 'max' => 10],
            [['group_name'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'group_id' => 'Group ID',
            'group_type' => 'Group Type',
            'group_name' => 'Group Name',
            'sort' => 'Sort',
            'wxapp_id' => 'Wxapp ID',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
