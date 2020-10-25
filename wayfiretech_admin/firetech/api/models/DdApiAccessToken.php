<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-12 16:40:19
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-09-16 22:42:40
 */

namespace api\models;

use Yii;
use common\models\DdMemberGroup;
use yii\web\UnauthorizedHttpException;
use yii\web\IdentityInterface;
use yii\db\ActiveRecord;
use yii\filters\RateLimitInterface;

/**
 * This is the model class for table "dd_api_access_token".
 *
 * @property int         $id
 * @property string|null $refresh_token 刷新令牌
 * @property string|null $access_token  授权令牌
 * @property int|null    $member_id     用户id
 * @property string|null $openid        授权对象openid
 * @property string|null $group         组别
 * @property int|null    $status        状态[-1:删除;0:禁用;1启用]
 * @property int|null    $create_time   创建时间
 * @property int|null    $updated_time  修改时间
 */
class DdApiAccessToken extends ActiveRecord implements IdentityInterface, RateLimitInterface
{
    const STATUS_DELETED = 1; //删除
    const STATUS_INACTIVE = 2; //拉黑
    const STATUS_ACTIVE = 0; //正常

    // 次数限制
    public  $rateLimit;

    // 时间范围
    public  $timeLimit;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%api_access_token}}';
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
                'updatedAttribute' => 'create_time',
                'createdAttribute' => 'update_time',
            ],
        ];
    }

    public function getRateLimit($request, $action)
    {
        $this->rateLimit = Yii::$app->params['api']['rateLimit'];
        $this->timeLimit = Yii::$app->params['api']['timeLimit'];
      
        return [$this->rateLimit, $this->timeLimit];
    }

    public function loadAllowance($request, $action)
    {
        $allowance = Yii::$app->cache->get($this->getCacheKey('api_rate_allowance'));
        $timestamp = Yii::$app->cache->get($this->getCacheKey('api_rate_timestamp'));

        if ($allowance === false) {
            return [$this->timeLimit, time()];
        }

        return [$allowance, $timestamp];
    }

    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        Yii::$app->cache->set($this->getCacheKey('api_rate_allowance'), $allowance, $this->timeLimit);
        Yii::$app->cache->set($this->getCacheKey('api_rate_timestamp'), $timestamp, $this->timeLimit);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['group_id', 'status', 'allowance', 'allowance_updated_at', 'create_time', 'updated_time'], 'integer'],
            [['refresh_token', 'access_token'], 'string', 'max' => 60],
            [['openid'], 'string', 'max' => 50],
            [['access_token'], 'unique'],
            [['refresh_token'], 'unique'],
        ];
    }

    /**
     * @param mixed $token
     * @param null  $type
     *
     * @return array|mixed|ActiveRecord|\yii\web\IdentityInterface|null
     *
     * @throws UnauthorizedHttpException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // 判断验证token有效性是否开启
        if (Yii::$app->params['user.accessTokenValidity'] === true) {
            $timestamp = (int) substr($token, strrpos($token, '_') + 1);
            $expire = Yii::$app->params['user.accessTokenExpire'];

            // 验证有效期
            if ($timestamp + $expire <= time()) {
                throw new UnauthorizedHttpException('您的登录验证已经过期，请重新登录');
            }
        }
        $service = Yii::$app->service;
        $service->namespace = 'api';
        // 优化版本到缓存读取用户信息 注意需要开启服务层的cache
        return $service->AccessTokenService->getTokenToCache($token, $type);
    }

    /**
     * @param $token
     * @param null $group
     *
     * @return AccessToken|\common\models\base\User|null
     */
    public static function findIdentityByRefreshToken($token, $group = null)
    {
        return static::findOne(['group_id' => $group, 'refresh_token' => $token, 'status' => 1]);
    }

    /**
     * 关联用户.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(DdMember::class, ['member_id' => 'member_id']);
    }

    /**
     * 关联授权角色.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMemberGroup()
    {
        return $this->hasOne(DdMemberGroup::class, ['group_id' => 'group_id'])
            ->where(['type' => Yii::$app->id]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['member_id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @return int|string 当前用户ID
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @return string 当前用户的（cookie）认证密钥
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @param string $authKey
     *
     * @return bool if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @param $key
     *
     * @return array
     */
    public function getCacheKey($key)
    {
        return [__CLASS__, $this->getId(), $key];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'refresh_token' => 'Refresh Token',
            'access_token' => 'Access Token',
            'member_id' => '用户id',
            'openid' => 'Openid',
            'group_id' => '用户组',
            'status' => '用户状态',
            'create_time' => 'Create Time',
            'updated_time' => 'Updated Time',
        ];
    }
}
