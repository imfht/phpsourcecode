<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-12 01:50:17
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-09-16 22:24:17
 */


namespace common\services\api;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UnprocessableEntityHttpException;
use common\helpers\ArrayHelper;
use common\services\BaseService;
use api\models\DdMember;
use api\models\DdApiAccessToken;
use common\helpers\ErrorsHelper;
use yii\web\UnauthorizedHttpException;

/**
 * Class AccessTokenService
 * @package services\api
 * @author wangchunsheng <2192138785@qq.com>
 */
class AccessTokenService extends BaseService
{
    /**
     * 是否加入缓存
     *
     * @var bool
     */
    public $cache = true;

    /**
     * 缓存过期时间
     *
     * @var int
     */
    public $timeout;

    /**
     * 获取token
     *
     * @param DdMember $member
     * @param $group
     * @param int $cycle_index 重新获取次数
     * @return array
     * @throws \yii\base\Exception
     */
    public function getAccessToken(DdMember $member, $group_id, $cycle_index = 1)
    {
        $model = $this->findModel($member->id, $group_id);

        $model->member_id = $member->id;
        
        $model->group_id  = $group_id;

        /* 是否到期，到期就重置 */
        if ($this->isPeriod($model->access_token) || empty($model->access_token)) {
            // 删除缓存
            !empty($model->access_token) && Yii::$app->cache->delete($this->getCacheKey($model->access_token));
            $model->refresh_token = Yii::$app->security->generateRandomString() . '_' . time();
            $model->access_token = Yii::$app->security->generateRandomString() . '_' . time();
            $model->status = 1;
            if (!$model->save()) {
                if ($cycle_index <= 3) {
                    $cycle_index++;
                    return self::getAccessToken($member, $group_id, $cycle_index);
                }
                $errorshelper = new ErrorsHelper();
                throw new UnprocessableEntityHttpException($errorshelper->getModelError($model));
            }
        }


        $result = [];
        $result['refresh_token'] = $model->refresh_token;
        $result['access_token'] = $model->access_token;
        $result['expiration_time'] = Yii::$app->params['user.accessTokenExpire'];
        // 关联账号信息
        $account = $member->account;
        $member = ArrayHelper::toArray($member);
        $result['member'] = $member;
        $result['member']['account'] = ArrayHelper::toArray($account);

        // 写入缓存
        $this->cache === true && Yii::$app->cache->set($this->getCacheKey($model->access_token), $model, $this->timeout);

        return $result;
    }

    /**
     * 忘记密码.
     *
     * @param int|null post
     * @return string
     * @throws NotFoundHttpException
     */
    public function forgetpassword(DdMember $member, $mobile, $password)
    {
        $member->generatePasswordResetToken();
        $member->setPassword($password);
        $member->generateAuthKey();
        $member->generateEmailVerificationToken();
        return  $member->save(false);
    }

    /**
     * 判断有效期.
     *
     * @param int|null post
     * @return 到期：true
     * @throws NotFoundHttpException
     */
    public static function isPeriod($token, $type = null)
    {
        // 判断验证token有效性是否开启
        if (Yii::$app->params['user.accessTokenValidity'] === true) {
            $timestamp = (int) substr($token, strrpos($token, '_') + 1);
            $expire = Yii::$app->params['user.accessTokenExpire'];
            // 验证有效期
            if ($timestamp + $expire <= time()) {
                // 过期
                return true;
            }
        }
        // 未到期
        return false;
    }

    /**
     * 修改accesstoken.
     *
     * @param int|null post
     * @return string
     * @throws NotFoundHttpException
     */
    public function RefreshToken($member_id, $group_id = 1)
    {
        $model = $this->findModel($member_id, $group_id);

        !empty($model->access_token) && Yii::$app->cache->delete($this->getCacheKey($model->access_token));
        $model->access_token = Yii::$app->security->generateRandomString() . '_' . time();
        if ($model->save()) {
            return $model->access_token;
        } else {
            return '修改失败';
        }
    }


    /**
     * @param $token
     * @param $type
     * @return array|mixed|null|ActiveRecord
     */
    public function getTokenToCache($token, $type)
    {
        if ($this->cache == false) {
            return $this->findByAccessToken($token);
        }

        $key = $this->getCacheKey($token);
        if (!($model = Yii::$app->cache->get($key))) {
            $model = $this->findByAccessToken($token);
            Yii::$app->cache->set($key, $model, $this->timeout);
        }

        return $model;
    }

    /**
     * 禁用token
     *
     * @param $access_token
     */
    public function disableByAccessToken($access_token)
    {
        $this->cache === true && Yii::$app->cache->delete($this->getCacheKey($access_token));

        if ($model = $this->findByAccessToken($access_token)) {
            $model->status = 1;
            return $model->save();
        }

        return false;
    }

    /**
     * 获取token
     *
     * @param $token
     * @return array|null|ActiveRecord|AccessToken
     */
    public function findByAccessToken($token)
    {
        return DdApiAccessToken::find()
            ->where(['access_token' => $token, 'status' => 1])
            ->one();
    }

    /**
     * @param $access_token
     * @return string
     */
    protected function getCacheKey($access_token)
    {
        return $access_token;
    }

    /**
     * 返回模型
     *
     * @param $member_id
     * @param $group
     * @return array|AccessToken|null|ActiveRecord
     */
    protected function findModel($member_id, $group_id)
    {
        if (empty(($model = DdApiAccessToken::find()->where([
            'member_id' => $member_id,
            'group_id' => $group_id
        ])->one()))) {
            $model = new DdApiAccessToken();
            return $model->loadDefaultValues();
        }

        return $model;
    }
}
