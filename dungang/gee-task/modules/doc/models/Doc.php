<?php

namespace modules\doc\models;

use Yii;

/**
 * This is the model class for table "gt_doc".
 *
 * @property int $id
 * @property int $pid 上级
 * @property int $gid 组
 * @property int $project_id 项目
 * @property string $title 标题
 * @property string $content 内容
 * @property int $created_at 添加时间
 * @property int $updated_at 更新时间
 */
class Doc extends \app\core\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gt_doc';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pid','gid', 'project_id', 'created_at', 'updated_at'], 'integer'],
            [['title'], 'required'],
            [['content'], 'string'],
            [['title'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pid' => '上级',
            'gid' => '组',
            'project_id' => '项目',
            'title' => '标题',
            'content' => '内容',
            'created_at' => '添加时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * {@inheritdoc}
     * @return DocQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DocQuery(get_called_class());
    }
}
