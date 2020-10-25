<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-05 11:45:49
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-06-03 15:31:07
 */


namespace api\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use api\models\LoginForm;
use common\models\base\AccessToken;
use api\models\DdApiAccessToken;
use api\models\DdMember;
use common\helpers\ErrorsHelper;
use common\helpers\ResultHelper;
use common\models\enums\PostStatus;
use common\models\forms\PasswdForm;
use api\controllers\AController;
use common\models\forms\EdituserinfoForm;


class UserController extends AController
{
    public $modelClass = '';
    protected $authOptional = ['login', 'signup', 'repassword', 'sendcode', 'forgetpass'];

    /**
     * @SWG\Post(path="/user/signup",
     *     tags={"登录与注册"},
     *     summary="注册",
     *     @SWG\Response(
     *         response = 200,
     *         description = "注册",
     *     ),
     *     @SWG\Parameter(
     *      in="formData",
     *      name="username",
     *      type="string",
     *      description="用户名",
     *      required=true,
     *    ),
     *     @SWG\Parameter(
     *      in="formData",
     *      name="mobile",
     *      type="string",
     *      description="手机号",
     *      required=true,
     *    ),
     *     @SWG\Parameter(
     *      in="formData",
     *      name="password",
     *      type="string",
     *      description="密码",
     *      required=true,
     *    ),
     *
     * )
     */

    public function actionSignup()
    {
        $DdMember = new DdMember();
        $data = Yii::$app->request->post();
        $username = $data['username'];
        $mobile = $data['mobile'];
        $password = $data['password'];
        if (empty($username)) {
            return ResultHelper::json(401, '用户名不能为空', []);
        }
        if (empty($mobile)) {
            return ResultHelper::json(401, '手机号不能为空', []);
        }
        if (empty($password)) {
            return ResultHelper::json(401, '密码不能为空', []);
        }

        $res = $DdMember->signup($username, $mobile, $password);

        return ResultHelper::json(200, '注册成功', $res);
    }

    /**
     * @SWG\Post(path="/user/login",
     *     tags={"登录与注册"},
     *     summary="登录",
     *     @SWG\Response(
     *         response = 200,
     *         description = "登录",
     *     ),
     *     @SWG\Parameter(
     *      in="formData",
     *      name="username",
     *      type="string",
     *      description="用户名",
     *      required=true,
     *    ),
     *     @SWG\Parameter(
     *      in="formData",
     *      name="password",
     *      type="string",
     *      description="密码",
     *      required=true,
     *    ),
     *
     * )
     */

    public function actionLogin()
    {
        $model = new LoginForm();
        if ($model->load(Yii::$app->getRequest()->getBodyParams(), '') && $model->login()) {
            $userinfo = $model->login();
            return ResultHelper::json(200, '登录成功', $userinfo);
        } else {
            $message = ErrorsHelper::getModelError($model);
            return ResultHelper::json('401', $message);
        }
    }



    /**
     * @SWG\Post(path="/user/repassword",
     *     tags={"密码设置"},
     *     summary="重置密码",
     *     @SWG\Response(
     *         response = 200,
     *         description = "重置密码",
     *     ),
     *     @SWG\Parameter(
     *      in="formData",
     *      name="mobile",
     *      type="string",
     *      description="手机号",
     *      required=true,
     *    ),
     *     @SWG\Parameter(
     *      in="formData",
     *      name="code",
     *      type="string",
     *      description="验证码",
     *      required=false,
     *    ),
     *     @SWG\Parameter(
     *      in="formData",
     *      name=" ",
     *      type="string",
     *      description="密码",
     *      required=true,
     *    ),
     *     @SWG\Parameter(
     *      in="formData",
     *      name="password_reset_token",
     *      type="string",
     *      description="修改密码token",
     *      required=true,
     *    ),
     *
     * )
     */

    public function actionRepassword()
    {
        $model = new PasswdForm();
        if ($model->load(Yii::$app->request->post(), '')) {
            if (!$model->validate()) {
                $res = ErrorsHelper::getModelError($model);
                return ResultHelper::json(404, $res);
            }
            /* @var $member \common\models\backend\Member */
            $data = Yii::$app->request->post();
            $mobile = $data['mobile'];
            $code = $data['code'];
            $sendcode = Yii::$app->cache->get($mobile . '_code');
            if ($code != $sendcode) {
                return ResultHelper::json(401, '验证码错误');
            }

            $member = DdMember::findByMobile($data['mobile']);
            $member->password_hash = Yii::$app->security->generatePasswordHash($model->newpassword);
            $member->generatePasswordResetToken();
            if ($member->save()) {
                Yii::$app->user->logout();
                $service = Yii::$app->service;
                $service->namespace = 'api';
                $userinfo = $service->AccessTokenService->getAccessToken($member, 1);
                // 清除验证码
                Yii::$app->cache->delete($mobile . '_code');
                return ResultHelper::json(200, '修改成功', $userinfo);
            }
            return ResultHelper::json(404, $this->analyErr($member->getFirstErrors()));
        } else {
            $res = ErrorsHelper::getModelError($model);
            return ResultHelper::json(404, $res);
        }
    }

    /**
     * @SWG\Post(path="/user/userinfo",
     *     tags={"会员资料"},
     *     summary="获取会员资料",
     *     @SWG\Response(
     *         response = 200,
     *         description = "会员资料",
     *     ),
     *     @SWG\Parameter(
     *      name="access-token",
     *      type="string",
     *      in="query",
     *      required=true
     *    ),
     *    @SWG\Parameter(
     *      name="mobile",
     *      type="integer",
     *      in="formData",
     *      required=true
     *    )
     * )
     */
    public function actionUserinfo()
    {
        $data = Yii::$app->request->post();
        // findByMobile
        $access_token = $data['access-token'];
        $userobj = DdMember::findByMobile($data['mobile']);
        $service = Yii::$app->service;
        $service->namespace = 'api';
        $userinfo = $service->AccessTokenService->getAccessToken($userobj, 1);
        return ResultHelper::json(200, '获取成功', ['userinfo' => $userinfo]);
    }

    /**
     * @SWG\Post(path="/user/edituserinfo",
     *     tags={"会员资料"},
     *     summary="修改资料",
     *     @SWG\Response(
     *         response = 200,
     *         description = "修改资料",
     *     ),
     *     @SWG\Parameter(
     *      name="access-token",
     *      type="string",
     *      in="query",
     *      required=true
     *    ),
     *     @SWG\Parameter(
     *      in="formData",
     *      name="username",
     *      type="string",
     *      description="用户名",
     *      required=true,
     *    ),
     *     @SWG\Parameter(
     *      in="formData",
     *      name="mobile",
     *      type="integer",
     *      description="手机号",
     *      required=true,
     *    ),
     *     @SWG\Parameter(
     *      in="formData",
     *      name="nickName",
     *      type="string",
     *      description="微信昵称",
     *      required=true,
     *    ),
     *     @SWG\Parameter(
     *      in="formData",
     *      name="avatarUrl",
     *      type="string",
     *      description="头像",
     *      required=true,
     *    ),
     *     @SWG\Parameter(
     *      in="formData",
     *      name="gender",
     *      type="string",
     *      description="性别",
     *      required=true,
     *    ),
     * )
     */
    public function actionEdituserinfo()
    {

        $model = new EdituserinfoForm();
        if ($model->load(Yii::$app->request->post(), '')) {
            if (!$model->validate()) {
                $res = ErrorsHelper::getModelError($model);
                return ResultHelper::json(404, $res);
            }
            $userinfo =  $model->edituserinfo();
            if ($userinfo) {
                return ResultHelper::json(200, '修改成功', $userinfo);
            }
            return ResultHelper::json(404, $this->analyErr($model->getFirstErrors()));
        } else {
            $res = ErrorsHelper::getModelError($model);
            return ResultHelper::json(404, $res);
        }
    }

    /**
     * @SWG\Post(path="/user/forgetpass",
     *     tags={"密码设置"},
     *     summary="忘记密码",
     *     @SWG\Response(
     *         response = 200,
     *         description = "忘记密码",
     *     ),
     *     @SWG\Parameter(
     *      in="formData",
     *      name="mobile",
     *      type="integer",
     *      description="手机号",
     *      required=true,
     *    ),   
     *     @SWG\Parameter(
     *      in="formData",
     *      name="code",
     *      type="integer",
     *      description="验证码",
     *      required=true,
     *    ),   
     *     @SWG\Parameter(
     *      in="formData",
     *      name="password",
     *      type="string",
     *      description="密码",
     *      required=true,
     *    ),
     * )
     */

    public function actionForgetpass()
    {
        $data = Yii::$app->request->post();
        $mobile = $data['mobile'];
        $password = $data['password'];
        $code = $data['code'];
        $sendcode = Yii::$app->cache->get($mobile . '_code');
        if ($code != $sendcode) {
            return ResultHelper::json(401, '验证码错误');
        }
        $member = DdMember::findByMobile($mobile);
        $res = Yii::$app->service->apiAccessTokenService->forgetpassword($member, $mobile, $password);
        if ($res) {
            // 清除验证码
            Yii::$app->cache->delete($mobile . '_code');
            return ResultHelper::json(200, '修改成功', []);
        } else {
            return ResultHelper::json(401, '修改失败', []);
        }
    }

    /**
     * @SWG\Post(path="/user/sendcode",
     *     tags={"发送验证码"},
     *     summary="发送验证码",
     *     @SWG\Response(
     *         response = 200,
     *         description = "发送验证码",
     *     ),
     *     @SWG\Parameter(
      *      in="formData",
     *      name="mobile",
     *      type="string",
      *      description="手机号",
      *      required=true,
      *    ),
     * )
     */
    public function actionSendcode()
    {
        $data = Yii::$app->request->post();
        $mobile = $data['mobile'];
        if (empty($mobile)) {
            return ResultHelper::json(401, "手机号不能为空");
        }
        $code   = random_int(1000, 9999);
        Yii::$app->cache->set($mobile . '_code', $code);
        // $service = Yii::$app->service;
        // $service->namespace = 'api';
        $usage = '忘记密码验证';
        // $res = $service->Sms->send($mobile, $code, $usage);
        $res = Yii::$app->service->apiSmsService->send($mobile, $code, $usage);
        return ResultHelper::json(200, "发送成功{$code}", $res);
    }



    /**
     * @SWG\Post(path="/user/refresh",
     *     tags={"重置令牌"},
     *     summary="重置令牌",
     *     @SWG\Response(
     *         response = 200,
     *         description = "重置令牌",
     *     ),
     *     @SWG\Parameter(
      *      in="formData",
     *      name="refresh_token",
     *      type="string",
      *      description="刷新token令牌",
      *      required=true,
      *    ),
     * )
     */
    public function actionRefresh($refresh_token)
    {
        $user = DdApiAccessToken::find()
            ->where(['refresh_token' => $refresh_token])
            ->one();
        if (!$user) {
            throw new NotFoundHttpException('令牌错误，找不到用户!');
        }
        $service = Yii::$app->service;
        $service->namespace = 'api';
        return $service->AccessTokenService->RefreshToken($user['member_id'], $user['group']);
    }

    // ....可以是设置其他用户登陆
}
