<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/15
 * Time: 22:30
 */

namespace backend\controllers;


use backend\behaviors\AuthBehaviors;
use yii\web\Controller;
use Yii;
class BaseController extends Controller{

    public $layout   = "mylayouts/main"; //设置使用的布局文件

    public function behaviors(){


        return [

            'myBehavior'=>[

                'class'=>AuthBehaviors::className(),
                'ZM'=>$this
            ]
        ];
    }

    /**
     * 重写父类的render方法
     * @param string $view
     * @param array $params
     * @return string
     */
    public  function render($view, $params = [])
    {
        $param           = ['model' =>[],'error'=>[]];
        $params          = array_merge($param,$params);
        return parent::render($view, $params);
    }



}