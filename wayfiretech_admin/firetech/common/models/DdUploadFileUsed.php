<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-07-29 01:55:39
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-29 01:55:42
 */
 

namespace common\models;

use Yii;

/**
 * This is the model class for table "dd_upload_file_used".
 *
 * @property int $used_id
 * @property int $file_id
 * @property int $from_id
 * @property string $from_type
 * @property int $wxapp_id
 * @property int $create_time
 */
class DdUploadFileUsed extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%upload_file_used}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['file_id', 'from_id', 'wxapp_id', 'create_time'], 'integer'],
            [['from_type'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'used_id' => 'Used ID',
            'file_id' => 'File ID',
            'from_id' => 'From ID',
            'from_type' => 'From Type',
            'wxapp_id' => 'Wxapp ID',
            'create_time' => 'Create Time',
        ];
    }
}
