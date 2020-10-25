<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-07-05 00:49:21
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-09-24 15:08:50
 */
 

namespace common\helpers;

use Yii;
use yii\web\Response;
use common\enums\AppEnum;
use yii\helpers\ArrayHelper;

/**
 * 格式化数据返回
 *
 * Class ResultHelper
 * @package common\helpers
 * @author chunchun <2192138785@qq.com>
 */
class ResultHelper
{
    /**
     * @param int $code
     * @param string $message
     * @param array $data
     * @return array|mixed
     */
    public static function json($code = 404, $message = '未知错误', $data = [])
    {
        if(!empty($data) && is_array($data)){
            if (array_key_exists('code', $data)) {
                return $data;
            }   
        }
        if (in_array(Yii::$app->id, ['api', 'OAUTH2'])) {
            return static::api($code, $message, $data);
        }

        return static::baseJson($code, $message, $data);
    }

    /**
     * 返回json数据格式
     *
     * @param int $code 状态码
     * @param string $message 返回的报错信息
     * @param array|object $data 返回的数据结构
     */
    protected static function baseJson($code, $message, $data)
    {
        if(Yii::$app->id !="app-console"){
            Yii::$app->response->format = Response::FORMAT_JSON;
        }
        
        $result = [
            'code' => strval($code),
            'message' => trim($message),
            'data' => $data ? ArrayHelper::toArray($data) : [],
        ];
        return $result;
    }

    /**
     * 返回 array 数据格式 api 自动转为 json
     *
     * @param int $code 状态码 注意：要符合http状态码
     * @param string $message 返回的报错信息
     * @param array|object $data 返回的数据结构
     */
    protected static function api($code, $message, $data)
    {
        Yii::$app->response->setStatusCode($code, $message);
        Yii::$app->response->data = $data ? ArrayHelper::toArray($data) : [];

        return Yii::$app->response->data;
    }
}
