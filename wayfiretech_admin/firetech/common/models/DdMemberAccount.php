<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-07-09 16:30:08
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-29 01:54:42
 */
 

namespace common\models;

use Yii;

/**
 * This is the model class for table "dd_member_account".
 *
 * @property int $id
 * @property int|null $store_id 商户id
 * @property int|null $member_id 用户id
 * @property int|null $level 会员等级
 * @property float|null $user_money 当前余额
 * @property float|null $accumulate_money 累计余额
 * @property float|null $give_money 累计赠送余额
 * @property float|null $consume_money 累计消费金额
 * @property float|null $frozen_money 冻结金额
 * @property int|null $user_integral 当前积分
 * @property int|null $accumulate_integral 累计积分
 * @property int|null $give_integral 累计赠送积分
 * @property float|null $consume_integral 累计消费积分
 * @property int|null $frozen_integral 冻结积分
 * @property int|null $status 状态[-1:删除;0:禁用;1启用]
 */
class DdMemberAccount extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%member_account}}';
    }

    
    /**
     * 行为.
     */
    public function behaviors()
    {
        /*自动添加创建和修改时间*/
        return [
            [
                'class' => \common\behaviors\SaveBehavior::className(),
                'updatedAttribute' => 'update_time',
                'createdAttribute' => 'create_time',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['store_id','bloc_id', 'member_id', 'level', 'user_integral', 'accumulate_integral', 'give_integral', 'frozen_integral', 'status',
            'update_time','create_time'
            ], 'integer'],
            [['user_money', 'accumulate_money', 'give_money', 'consume_money', 'frozen_money', 'consume_integral'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'store_id' => '商户id',
            'bloc_id' => '公司id',
            'member_id' => '会员id',
            'level' => '会员等级',
            'user_money' => '当前余额',
            'accumulate_money' => '累计余额',
            'give_money' => '累计赠送余额',
            'consume_money' => '累计消费金额',
            'frozen_money' => '冻结金额',
            'user_integral' => '当前积分',
            'accumulate_integral' => '累计积分',
            'give_integral' => '累计赠送积分',
            'consume_integral' => '累计消费积分',
            'frozen_integral' => '冻结积分',
            'status' => 'Status',
        ];
    }
}
