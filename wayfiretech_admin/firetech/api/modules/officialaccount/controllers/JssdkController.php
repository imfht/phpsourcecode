<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-09 01:32:28
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-09-23 23:34:47
 */

namespace api\modules\officialaccount\controllers;

use Yii;
use api\controllers\AController;
use common\helpers\ArrayHelper;
use common\helpers\FileHelper;
use common\helpers\ResultHelper;
use common\models\DdCorePaylog;
use yii\helpers\Json;

/**
 * Default controller for the `officialaccount` module.
 */
class JssdkController extends AController
{
    protected $authOptional = [];
    public $modelClass = 'api\modules\wechat\models\DdWxappFans';

     /**
     * @SWG\Post(path="/officialaccount/jssdk/config",
     *     tags={"jssdl配置参数获取"},
     *     summary="配置参数",
     *     @SWG\Response(
     *         response = 200,
     *         description = "jssdl配置参数获取"
     *     ),
     *     @SWG\Parameter(
     *      in="query",
     *      name="access-token",
     *      type="string",
     *      description="access-token",
     *      required=true,
     *    ),
     *    @SWG\Parameter(
     *      name="bloc-id",
     *      type="integer",
     *      in="header",
     *      description="公司id",
     *      required=true
     *    ),
     *    @SWG\Parameter(
     *      name="store-id",
     *      type="integer",
     *      in="header",
     *      description="商户id",
     *      required=true
     *    )
     * )
     */
    public function actionConfig()
    {
        $app = Yii::$app->wechat->app;
        $APIs = [
            'checkJsApi',
            'onMenuShareTimeline',
            'onMenuShareAppMessage',
            'onMenuShareQQ',
            'onMenuShareWeibo',
            'hideMenuItems',
            'showMenuItems',
            'hideAllNonBaseMenuItem',
            'showAllNonBaseMenuItem',
            'translateVoice',
            'startRecord',
            'stopRecord',
            'onRecordEnd',
            'playVoice',
            'pauseVoice',
            'stopVoice',
            'uploadVoice',
            'downloadVoice',
            'chooseImage',
            'previewImage',
            'uploadImage',
            'downloadImage',
            'getNetworkType',
            'openLocation',
            'getLocation',
            'hideOptionMenu',
            'showOptionMenu',
            'closeWindow',
            'scanQRCode',
            'chooseWXPay',
            'openProductSpecificView',
            'addCard',
            'chooseCard',
            'openCard',
            'openAddress'
        ];
        // $app->jssdk->buildConfig(array $APIs, $debug = false, $beta = false, $json = true, array $openTagList = []);
        $configs = $app->jssdk->buildConfig($APIs,false,false,false);
        
        return ResultHelper::json(200,'获取成功',$configs);
    }

}