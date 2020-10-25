<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-05 08:26:29
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-09-07 10:18:05
 */

namespace api\modules\officialaccount\controllers;

use api\controllers\AController;
use Yii;

/**
 * login controller for the `officialaccount` module.
 */
class LoginController extends AController
{
    /**
     * @SWG\Post(path="/officialaccount/login/index",
     *     tags={"微信接口测试"},
     *     summary="微信接口测试",
     *     @SWG\Response(
     *         response = 200,
     *         description = "微信接口测试"
     *     ),
     *     @SWG\Parameter(
     *      in="formData",
     *      name="images",
     *      type="string",
     *      description="脸部图片路径",
     *      required=true,
     *    ),
     * @SWG\Parameter(
     *      name="access-token",
     *      type="string",
     *      in="query",
     *      required=true
     *  )
     * )
     */
    public function actionIndex()
    {
        if (Yii::$app->wechat->isWechat && !Yii::$app->wechat->isAuthorized()) {
            return Yii::$app->wechat->authorizeRequired()->send();
        }

        // 获取微信当前用户信息方法一
        Yii::$app->session->get('wechatUser');

        // 获取微信当前用户信息方法二
        Yii::$app->wechat->user;

        return $this->render('index');
    }
}
