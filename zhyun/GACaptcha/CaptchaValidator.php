<?php
/**
 * Created by PhpStorm.
 * User: xin
 * Date: 16/12/22
 * Time: 下午4:10
 */

namespace ga\captcha;

use Yii;
use yii\validators\Validator;
use yii\base\InvalidConfigException;

class CaptchaValidator extends Validator{


    public $skipOnEmpty = false;

    public $caseSensitive = false;

    public $captchaAction = 'site/captcha';

    public function init(){

        parent::init();
        if($this->message == null){
            //TODO:修改
//            $this->message = Yii::t('ga', 'The verification code is error.');
            $this->message = 'The verification code is error.';
        }
    }

    public function validateValue($value){

        $ca = $this->createCaptchaAction();
        return $ca->validate($value, $this->caseSensitive) === true ? null : [$this->message ,[]];
    }

    public function createCaptchaAction(){

        $ca = Yii::$app->createController($this->captchaAction);
        if ($ca !== false) {
            /* @var $controller \yii\base\Controller */
            list($controller, $actionID) = $ca;
            $action = $controller->createAction($actionID);
            if ($action !== null) {
                return $action;
            }
        }
        throw new InvalidConfigException('Invalid CAPTCHA action ID: ' . $this->captchaAction);
    }

}
