<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-04-09 11:20:54
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-06-27 21:26:47
 */

namespace common\components;

use common\helpers\FileHelper as HelpersFileHelper;
use common\helpers\ImageHelper;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use yii\base\Exception;
use yii\helpers\FileHelper;
use Faker\Provider\Uuid;
use yii\helpers\Json;

/**
 * 文件上传处理
 */
class Upload extends Model
{
    public $file;
    private $_appendRules;
    public function init()
    {
        parent::init();
        $extensions = Yii::$app->params['webuploader']['baseConfig']['accept']['extensions'];
        $this->_appendRules = [
            [['file'], 'file', 'extensions' => $extensions],
        ];
    }

    public function rules()
    {
        $baseRules = [];
        return array_merge($baseRules, $this->_appendRules);
    }

    /**
     *
     */
    public function upImage()
    {
        $model = new static;
        $model->file = UploadedFile::getInstanceByName('file');
        if (!$model->file) {
            return false;
        }
        $relativePath = $successPath = '';
        if ($model->validate()) {
            $relativePath = Yii::$app->params['imageUploadRelativePath'];
            $successPath = Yii::$app->params['imageUploadSuccessPath'];
            //$model->file->baseName
            $fileName = Uuid::uuid() . '.' . $model->file->extension;
            if (!is_dir($relativePath)) {
                HelpersFileHelper::mkdirs($relativePath);
            }
            $Res = $model->file->saveAs($relativePath . $fileName);
            if($Res){
              ImageHelper::uploadDb($fileName,$model->file->size,$model->file->type,$model->file->extension,$successPath . $fileName);
             }
            return [
                'code' => 0,
                'url' =>  ImageHelper::tomedia($successPath . $fileName),
                'attachment' => $successPath . $fileName
            ];
        } else {
            $errors = $model->errors;
            return [
                'code' => 1,
                'msg' => current($errors)[0]
            ];
        }
    }


    /**
     * 文件上传
     * ```
     *  $model = new UploadValidate($config_name);
     *  $result = CommonHelper::myUpload($model, $field, 'invoice');
     * ```
     *
     * @param  object $model \common\models\UploadValidate 验证上传文件
     * @param  string $field 上传字段名称
     * @param  string $path  文件保存路径
     *
     * @return bool|array
     */
    public static function upFile($model, $field, $path = '')
    {

        $upload_path = Yii::getAlias("@frontend/web/attachment/");
        $path = $path ? $path . "/" : '';
        if (\Yii::$app->request->isPost) {
            $file = UploadedFile::getInstanceByName($field);

            $model->file = $file;
            //文件上传存放的目录
            $dir = $upload_path . $path . date("Ymd");
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
                chmod($dir, 0777);
            }
            if ($model->validate()) {
                //生成文件名
                $rand_name = rand(1000, 9999);
                $fileName = date("YmdHis") . $rand_name . '_' . $model->file->baseName . "." . $model->file->extension;
                $save_dir = $dir . "/" . $fileName;
                $model->file->saveAs($save_dir);
                $uploadSuccessPath = $path . date("Ymd") . "/" . $fileName;
                $result['file_name'] = $model->file->baseName;
                $result['file_path'] = $uploadSuccessPath;
                return $result;
            } else {
                //上传失败记录日志
                $logPath = Yii::getAlias("@runtime/log/upload/" . date("YmdHis") . '.log');
                HelpersFileHelper::writeLog($logPath, Json::encode($model->errors));
                return false;
            }
        } else {
            return false;
        }
    }
}
