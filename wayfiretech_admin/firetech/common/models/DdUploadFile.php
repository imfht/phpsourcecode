<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-07-29 01:55:30
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-29 01:55:32
 */
 

namespace common\models;

use Yii;

/**
 * This is the model class for table "dd_upload_file".
 *
 * @property int $file_id
 * @property string $storage
 * @property int $group_id
 * @property string $file_url
 * @property string $file_name
 * @property int $file_size
 * @property string $file_type
 * @property string $extension
 * @property int $is_delete
 * @property int $wxapp_id
 * @property int $create_time
 */
class DdUploadFile extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%upload_file}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['group_id', 'file_size', 'is_delete', 'wxapp_id', 'create_time'], 'integer'],
            [['storage', 'file_type', 'extension'], 'string', 'max' => 20],
            [['file_url', 'file_name'], 'string', 'max' => 255],
            [['file_name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'file_id' => 'File ID',
            'storage' => 'Storage',
            'group_id' => 'Group ID',
            'file_url' => 'File Url',
            'file_name' => 'File Name',
            'file_size' => 'File Size',
            'file_type' => 'File Type',
            'extension' => 'Extension',
            'is_delete' => 'Is Delete',
            'wxapp_id' => 'Wxapp ID',
            'create_time' => 'Create Time',
        ];
    }
}
