<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-17 14:10:10
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-17 14:10:10
 */
 

namespace common\models\forms;

use Yii;
use yii\base\Model;
use api\models\DdMember;

class EdituserinfoForm extends Model
{

    /**
     * 用户名
     */
    public $username;

    /**
     * 手机号
     */
    public $mobile;

  
    /**
     * 微信昵称
     */
    public $nickName;

    /**
     * 头像
     */
    public $avatarUrl;

     /**
     * 性别
     */
    public $gender;


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
            [['username', 'mobile','nickName','avatarUrl','gender'], 'filter', 'filter' => 'trim'],
            [['username', 'mobile','nickName','avatarUrl','gender'], 'required'],
            ['mobile','match','pattern'=>'/^[1][34578][0-9]{9}$/'],
            ['mobile', 'validateMobile'],
            ['username', 'validateUsername'],
        ];
    }

    public function validateMobile($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            $hasuser = DdMember::find()->where(['and',['=','mobile',$this->mobile],['!=','member_id',$user->member_id]])->select('member_id')->one();
            if ($hasuser) {
              return   $this->addError($attribute, '手机号已经被占用');
            }
        }

    }

    public function validateUsername($attribute, $params)
    {
           if (!$this->hasErrors()) {
                $user = $this->getUser();
                $hasuser = DdMember::find()->where(['and',['=','username',$this->username],['!=','member_id',$user->member_id]])->select('member_id')->one();
                if ($hasuser) {
                  return   $this->addError($attribute, '用户名已经被占用');
                }
            }
    }

      /**
     * 修改用户资料
     *
     * @return bool whether the user is logged in successfully
     */
    public function edituserinfo()
    {
        if ($this->validate()) {
            $userobj = $this->getUser();
            $userobj->load($this->attributes,'');
            if($userobj->save()){
                $service = Yii::$app->service;
                $service->namespace = 'api';
                $userinfo = $service->AccessTokenService->getAccessToken($userobj, 1);
                return $userinfo;
            }
        } else {
            return false;
        }
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
            $member_id = Yii::$app->user->identity->member_id;
            $this->_user = DdMember::findIdentity($member_id);
        }
        return $this->_user;
    }

    public function attributeLabels()
    {
        return [
            'username' => '用户名',
            'mobile' => '手机号',
            'avatarUrl' => '头像',
            'gender' => '性别',
            'nickName'=>'微信昵称'
        ];
    }
}