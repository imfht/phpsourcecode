<?php
/**
 * @Author: Wang chunsheng  &#60;2192138785@qq.com&#62;
 * @Date:   2020-04-29 17:21:04
 * @Last Modified by:   Wang chunsheng  <2192138785@qq.com>
 * @Last Modified time: 2020-04-29 17:21:22
 */
 

namespace common\models\forms;

use Yii;
use yii\base\Model;

class Sms extends Model
{

    /**
     * @var string application name
     */
    public $access_key_id;

    public $access_key_secret;

    /**
     * @var string admin email
     */
    public $sign_name;

    public $template_code;




    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['access_key_id', 'access_key_secret', 'sign_name'], 'string'],
            ['template_code', 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'access_key_id' => 'AccessKey ID',
            'access_key_secret' => 'Access Key Secret',
            'sign_name' => '签名',
            'template_code' => '模板code'
        ];
    }
}
