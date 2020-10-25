<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-17 14:10:53
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-17 14:10:53
 */
 

namespace common\models\forms;

use Yii;
use yii\base\Model;
use api\models\DdMember;

class PasswdForm extends Model
{
    /**
     * 验证码
     */
    public $code;

    /**
     * 手机号
     */
    public $mobile;


    /**
     * 新密码
     */
    public $newpassword;

    /**
     * 修改密码token
     */
    public $password_reset_token;


    /**
     * @var Member
     */
    private $_user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['newpassword', 'code', 'mobile'], 'filter', 'filter' => 'trim'],
            [['newpassword', 'mobile', 'password_reset_token'], 'required'],
            [['newpassword'], 'string', 'min' => 6, 'max' => 15]
        ];
    }



    /**
     * 获取用户信息
     *
     * @return Member|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            /** @var DdMember $identity */
            $this->_user = DdMember::findByPasswordResetToken($this->password_reset_token);
        }
        return $this->_user;
    }

    public function attributeLabels()
    {
        return [
            'code' => '验证码',
            'mobile' => '手机号',
            'newpassword' => '新密码',
            'password_reset_token' => '修改密码token',
        ];
    }
}
