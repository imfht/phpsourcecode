<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "gt_cron".
 *
 * @property int $id
 * @property string $task 任务
 * @property string $mhdmd 定时
 * @property string $job_script 脚本
 * @property string $param 参数
 * @property string $intro 介绍
 * @property string $token 安全key
 * @property string $error_msg 错误信息
 * @property bool $is_ok 正常
 * @property bool $is_active 激活
 * @property int $run_at 执行时刻
 * @property int $created_at 添加时间
 * @property int $updated_at 更新时间
 */
class Cron extends \app\core\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gt_cron';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mhdmd'], 'required'],
            [['is_ok', 'is_active'], 'boolean'],
            [['run_at', 'created_at', 'updated_at'], 'integer'],
            [['task'], 'string', 'max' => 64],
            [['mhdmd'], 'string', 'max' => 128],
            [['job_script', 'param', 'intro', 'error_msg'], 'string', 'max' => 255],
            [['token'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task' => '任务',
            'mhdmd' => '定时',
            'job_script' => '脚本',
            'param' => '参数',
            'intro' => '介绍',
            'token' => '安全key',
            'error_msg' => '错误信息',
            'is_ok' => '正常',
            'is_active' => '激活',
            'run_at' => '执行时刻',
            'created_at' => '添加时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * {@inheritdoc}
     * @return CronQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CronQuery(get_called_class());
    }
}
