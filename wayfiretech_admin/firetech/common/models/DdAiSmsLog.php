<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-07-29 01:51:44
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-29 01:51:46
 */
 

namespace common\models;

use Yii;

/**
 * This is the model class for table "dd_ai_sms_log".
 *
 * @property int $id
 * @property int|null $member_id 用户id
 * @property string|null $mobile 手机号码
 * @property string|null $code 验证码
 * @property string|null $content 内容
 * @property int|null $error_code 报错code
 * @property string|null $error_msg 报错信息
 * @property string|null $error_data 报错日志
 * @property string|null $usage 用途
 * @property int|null $used 是否使用[0:未使用;1:已使用]
 * @property int|null $use_time 使用时间
 * @property string|null $ip ip地址
 * @property int $status 状态(-1:已删除,0:禁用,1:正常)
 * @property int|null $created_at 创建时间
 * @property int|null $updated_at 修改时间
 */
class DdAiSmsLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%ai_sms_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_id', 'error_code', 'used', 'use_time', 'status', 'created_at', 'updated_at'], 'integer'],
            [['error_data'], 'string'],
            [['mobile', 'usage'], 'string', 'max' => 20],
            [['code'], 'string', 'max' => 6],
            [['content'], 'string', 'max' => 500],
            [['error_msg'], 'string', 'max' => 200],
            [['ip'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => 'Member ID',
            'mobile' => 'Mobile',
            'code' => 'Code',
            'content' => 'Content',
            'error_code' => 'Error Code',
            'error_msg' => 'Error Msg',
            'error_data' => 'Error Data',
            'usage' => 'Usage',
            'used' => 'Used',
            'use_time' => 'Use Time',
            'ip' => 'Ip',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
