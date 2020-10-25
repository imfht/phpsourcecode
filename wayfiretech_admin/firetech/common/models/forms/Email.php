<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-17 08:54:39
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-17 08:54:43
 */
namespace common\models\forms;

use Yii;
use yii\base\Model;

class Email extends Model
{

    /**
     * @var string application name
     */
    public $host;
    public $port;
    public $username;
    public $password;
    public $title;
    public $encryption;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['host', 'port', 'username', 'password','title','encryption'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'host' => 'smtp地址',
            'port' => '端口',
            'username' => '邮箱账号',
            'password' => '邮箱密码',
            'title' => '发送者名称',

        ];
    }
}
