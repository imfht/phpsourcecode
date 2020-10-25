<?php
/**
 * Author: dungang
 * Date: 2017/4/12
 * Time: 14:52
 */

namespace dungang\luosimao;


use yii\helpers\Json;
use yii\validators\Validator;

class CaptchaValidator extends Validator
{
    public $messages = [
        '-500'=>'验证码服务器挂了',
        '-10'=>'API KEY 为空',
        '-11'=>'response为空',
        '-20'=>'response错误',
        '-40'=>'API_KEY使用错误',
    ];
    public function validateAttribute($model, $attribute)
    {
        if (empty(\Yii::$app->params['luosimao']) ||
            empty(\Yii::$app->params['luosimao']['apiKey'])) {
             $this->addError($model,$attribute,\Yii::t('app','Lost luosimao captcha config'));
        } else {
            $apiKey = \Yii::$app->params['luosimao']['apiKey'];
            if($response = \Yii::$app->request->post('luotest_response')) {

                $rst =  $this->request('https://captcha.luosimao.com/api/site_verify',true,[
                    'api_key'=>$apiKey,
                    'response'=>$response
                ]);
                if ($rst) {
                    $rst = Json::decode($rst);
                    if ($rst['res'] == 'success') {
                        return null;
                    } else {
                        $this->addError($model,$attribute,isset($this->message[$rst['error']])
                            ?$this->message[$rst['error']]
                            :$rst['msg']);
                    }
                }
            }

            $this->addError($model,$attribute,\Yii::t('app','Server Error'));
        }


    }


    /**
     * @param $url
     * @param $isPost
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    public function request($url,$isPost,$data=[])
    {
        $ch = curl_init();
        //set url
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

        //定义post
        if ($isPost) {
            curl_setopt($ch,CURLOPT_POST,true);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        }

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception(curl_error($ch), 0);
        } else {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 !== $httpStatusCode) {
                throw new \Exception($response, $httpStatusCode);
            }
        }
        curl_close($ch);
        return $response;

    }
}
