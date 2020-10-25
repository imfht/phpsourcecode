<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-24 12:17:15
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-27 18:32:20
 */

namespace common\components\wechat;

use yii\base\Component;

/**
 * Class WechatUser.
 */
class WechatUser extends Component
{
    /**
     * @var string
     */
    public $id;
    /**
     * @var string
     */
    public $nickname;
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $email;
    /**
     * @var string
     */
    public $avatar;
    /**
     * @var array
     */
    public $original;
    /**
     * @var \Overtrue\Socialite\AccessToken
     */
    public $token;
    /**
     * @var string
     */
    public $provider;

    /**
     * @return string
     */
    public function getOpenId()
    {
        return isset($this->original['openid']) ? $this->original['openid'] : '';
    }
}
