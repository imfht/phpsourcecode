<?php

namespace common\helpers;

use Yii;
use yii\base\Model;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;

class ErrorsHelper extends BaseObject
{
    /**
     * function_description.
     *
     * @param int|null post
     * @return string
     * @throws NotFoundHttpException
     */
    public static function getModelError($model)
    {
        $errors = $model->getErrors();    //得到所有的错误信息
        if (!is_array($errors)) return '';
        $firstError = array_shift($errors);
        if (!is_array($firstError)) return '';
        return array_shift($firstError);
    }
}
