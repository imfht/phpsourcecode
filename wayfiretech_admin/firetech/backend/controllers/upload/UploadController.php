<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-04-09 11:19:49
 * @Last Modified by:   Wang Chunsheng 2192138785@qq.com
 * @Last Modified time: 2020-04-09 14:57:51
 */

namespace backend\controllers\upload;

use  Yii;
use  backend\controllers\BaseController;
use common\addons\diandi_shop\models\DdGoods;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\components\Upload;
use common\models\UploadValidate;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 *
 */
class UploadController extends BaseController
{
    public $enableCsrfValidation = false;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionUploadimg()
    {
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


    public function actionUploadfile()
    {
        try {
            $Upload = new Upload();
            //实例化上传验证类，传入上传配置参数项名称
            $model = new UploadValidate('uploadFile');
            $field = Yii::$app->request->post('field');
            $path = Yii::$app->request->post('path', '');
            if (!$field) {
                exit(Json::htmlEncode([
                    'code' => 1,
                    'msg' => '必须指明上传的字段：field'
                ]));
            }
            if (!$path) {
                exit(Json::htmlEncode([
                    'code' => 1,
                    'msg' => 'path'
                ]));
            }
            //上传
            $info = $Upload::upFile($model, urldecode($field), urldecode($path));

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
