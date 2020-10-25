<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-04 00:28:50
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-09-19 10:57:30
 */


namespace frontend\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;


/**
 * @SWG\Swagger(
 *     schemes={"http"},
 *     host="www.ai.com",
 *     basePath="/api/",
 *     produces={"application/json"},
 *     consumes={"application/x-www-form-urlencoded"},
 *     @SWG\Info(version="1.0", title="店滴AI接口文档",
 *     description="店滴AI接口文档",
 *     @SWG\Parameter(
 *      in="query",
 *      name="code",
 *      type="string",
 *      description="微信授权code",
 *      required=true,
 *    ),
 *     @SWG\Contact(
 *        name="王春生",
 *        email="2192138785@qq.com"
 *     )),
 * )
 */
class DocController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => 'yii2mod\swagger\SwaggerUIRenderer',
                // 'restUrls' => Url::to(['doc/json-schema']),
                'restUrl' => [
                    ['url' => Url::to(['doc/json-inits']), 'name' => '基础接口'],
                    ['url' => Url::to(['doc/json-wechat']), 'name' => '小程序接口'],
                    ['url' => Url::to(['doc/json-officialaccount']), 'name' => '公众号接口'],
                ],
                'view' => '@frontend/views/apidoc/index'
            ],
            // 小程序接口
            'json-officialaccount' => [
                'class' => 'yii2mod\swagger\OpenAPIRenderer',
                'scanDir' => [
                    Yii::getAlias('@frontend/controllers'),
                    Yii::getAlias('@api/modules/officialaccount/controllers'),
                    // Yii::getAlias('@api/models/Definition'),
                ],
                'cacheKey' => 'swagger-wechat',
            ],
            // 小程序接口
            'json-wechat' => [
                'class' => 'yii2mod\swagger\OpenAPIRenderer',
                'scanDir' => [
                    Yii::getAlias('@frontend/controllers'),
                    Yii::getAlias('@api/modules/wechat/controllers'),
                    // Yii::getAlias('@api/models/Definition'),
                ],
                'cacheKey' => 'swagger-wechat',
            ],
            /* 基础接口:登录注册、人脸识别 */
            'json-inits' => [
                'class' => 'yii2mod\swagger\OpenAPIRenderer',
                'scanDir' => [
                    Yii::getAlias('@frontend/controllers'),
                    Yii::getAlias('@api/controllers'),
                    // Yii::getAlias('@api/models/Definition'),
                ],
                'cacheKey' => 'swagger-inits',
            ]
        ];
    }
}
