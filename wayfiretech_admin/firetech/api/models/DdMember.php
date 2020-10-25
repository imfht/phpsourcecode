<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-12 00:35:06
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-09-16 20:40:14
 */


namespace api\models;

use common\helpers\ErrorsHelper;
use common\helpers\FileHelper;
use common\models\DdMemberAccount;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\base\ErrorException;
use common\helpers\ResultHelper;

/**
 * This is the model class for table "dd_member".
 *
 * @property int $member_id
 * @property string $openid
 * @property string $nickName
 * @property string $avatarUrl
 * @property int $gender
 * @property string $country
 * @property string $province
 * @property string $city
 * @property int $address_id
 * @property int $wxapp_id
 * @property string|null $access_token
 * @property string|null $verification_token
 * @property int $create_time
 * @property int $update_time
 */

class DdMember extends ActiveRecord
{
    const STATUS_DELETED = 1; //删除
    const STATUS_INACTIVE = 2; //拉黑
    const STATUS_ACTIVE = 0; //正常

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%member}}';
    }



    /**
     * 行为
     */
    public function behaviors()
    {
        /*自动添加创建和修改时间*/
        return [
            [
                'class' => \common\behaviors\SaveBehavior::className(),
                'updatedAttribute' => 'update_time',
                'createdAttribute' => 'create_time',
            ]
        ];
    }

    /**
     * 关联api验证token
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAccesstoken()
    {
        return $this->hasOne(DdApiAccessToken::class, ['member_id' => 'member_id']);
    }

    /**
     * 关联用户资产
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(DdMemberAccount::class, ['member_id' => 'member_id']);
    }


    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     */
    public function signup($username, $mobile, $password)
    {
        $logPath = Yii::getAlias('@runtime/wechat/login/'.date('ymd').'.log');

        if (!$this->validate()) {
            FileHelper::writeLog($logPath, '登录日志:会员注册校验失败'.json_encode($this->validate()));
            return $this->validate();
        }
        
        /* 查看用户名是否重复 */
        // $userinfo = $this->find()->where(['username' => $username])->select('member_id')->one();
        // if (!empty($userinfo)) {
        //     return ResultHelper::json(401, '用户名重复');
        // }
        /* 查看手机号是否重复 */
        if($mobile){
            
            $userinfo = $this->find()->where(['mobile' => $mobile])
            ->andWhere(['<>', 'mobile', 0])->select('member_id')->one();
            if (!empty($userinfo)) {
                return ResultHelper::json(401, '手机号重复');
            }
         
        }
        FileHelper::writeLog($logPath, '登录日志:会员注册校验手机号'.json_encode($mobile));
        
       
        $this->username = $username;
        $this->mobile = $mobile;
        $this->level  = 1;
        $this->group_id  = 1;

        $this->setPassword($password);
        $this->generateAuthKey();
        $this->generateEmailVerificationToken();
        $this->generatePasswordResetToken();
        if ($this->save()) {
            $member_id = Yii::$app->db->getLastInsertID();
            /* 写入用户初始资产 */
            $DdMemberAccount = new DdMemberAccount();
            $DdMemberAccount->member_id = $member_id;
            $DdMemberAccount->status = 1;
            $DdMemberAccount->level  = 1;
            $DdMemberAccount->save();
            /* 写入用户apitoken */
            $service = Yii::$app->service;
            $service->namespace = 'api';
            $userinfo = $service->AccessTokenService->getAccessToken($this, 1);
            return $userinfo;
        }else{
            $msg = ErrorsHelper::getModelError($this);
            FileHelper::writeLog($logPath, '登录日志:会员注册失败错误'.json_encode($msg));
            return ResultHelper::json(401, $msg);
            
        }
    }


    /**
     * 生成accessToken字符串
     * @return string
     * @throws \yii\base\Exception
     */
    public function generateAccessToken()
    {
        $this->access_token =  Yii::$app->security->generateRandomString();
        return $this->access_token;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['member_id' => $id, 'status' => self::STATUS_ACTIVE]);
    }
    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::find()
            ->where(['and', ['or', " username = '{$username}'", "mobile='{$username}'"], 'status =' . self::STATUS_ACTIVE])
            ->one();
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByMobile($mobile)
    {
        return static::findOne([
            'mobile' => $mobile,
            'status' => self::STATUS_ACTIVE,
        ]);
    }


    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token)
    {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
     */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['gender', 'address_id', 'wxapp_id','group_id', 'create_time', 'update_time'], 'integer'],
            [['username', 'openid', 'nickName', 'avatarUrl', 'verification_token', 'address'], 'string', 'max' => 255],
            [['country', 'province', 'city'], 'string', 'max' => 100],
        ];
    }

    public function fields()
    {
        $fields = parent::fields();
        // 去掉一些包含敏感信息的字段
        unset($fields['auth_key'], $fields['password_hash'], $fields['verification_token']);
        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'member_id' => '用户id',
            'openid' => 'OpenID',
            'nickName' => '昵称',
            'group_id' => '用户组id',
            'avatarUrl' => '头像',
            'gender' => '性别',
            'country' => '国家',
            'province' => '省份',
            'city' => '城市',
            'address_id' => '地址',
            'wxapp_id' => 'Wxapp ID',
            'access_token' => 'Access Token',
            'verification_token' => '验证token',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
}
