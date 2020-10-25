<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-14 01:25:51
 * @Last Modified by:   Wang Chunsheng 2192138785@qq.com
 * @Last Modified time: 2020-03-14 01:28:25
 */


namespace common\models\forms;

use Yii;
use yii\base\Model;

class Wechatpay extends Model
{
    /**
     * @var string application name
     */
    public $appId;

    /**
     * @var string admin email
     */
    public $mch_id;
    public $app_id;
    public $key;
    public $notify_url;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [[
                'mch_id',
                'app_id',
                'key',
                'notify_url',
            ], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'mch_id' => '支付商户号',
            'app_id' => 'AppId',
            'key' => '秘钥',
            'notify_url' => '回调地址',
        ];
    }
}
