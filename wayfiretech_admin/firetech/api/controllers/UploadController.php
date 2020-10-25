<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-19 18:05:45
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-06-27 21:46:52
 */


namespace api\controllers;

use Yii;
use api\controllers\AController;
use yii\filters\VerbFilter;
use common\components\Upload;
use common\helpers\ResultHelper;
use yii\helpers\Json;
use yii\rest\ActiveController;


class UploadController extends AController
{
    public $modelClass = '';

    public $enableCsrfValidation = false;
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'delete' => ['POST'],
            ],
        ];
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            \Yii::$app->response->setStatusCode(204);
            \Yii::$app->end(0);
        }
        return $behaviors;
    }

    /**
     * @SWG\Post(path="/upload/images",
     *     tags={"资源上传"},
     *     summary="上传图片",
     *     @SWG\Response(
     *         response = 200,
     *         description = "上传图片",
     *     ),
     *     @SWG\Parameter(
     *      in="formData",
     *      name="images",
     *      type="string",
     *      description="需要上传的图片",
     *      required=true,
     *    ),
     *    @SWG\Parameter(
     *      name="access-token",
     *      type="string",
     *      in="query",
     *      required=true
     *     )
     * )
     */
    public function actionImages()
    {
        global $_GPC;
        try {
            $model = new Upload();
            $info = $model->upImage();
            $info && is_array($info) ?
                exit(Json::htmlEncode($info)) :
                exit(Json::htmlEncode([
                    'code' => 1,
                    'msg' => 'error'
                ]));
        } catch (\Exception $e) {
            exit(Json::htmlEncode([
                'code' => 1,
                'msg' => $e->getMessage()
            ]));
        }
    }
}
