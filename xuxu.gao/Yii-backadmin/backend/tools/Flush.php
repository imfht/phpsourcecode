<?php
/**
 * 消息提示辅助类
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/23
 * Time: 14:34
 */

namespace backend\tools;

use Yii;

class Flush {


    /**
     * 成功提示方法
     * @param string $msg
     */
    public static function success($msg = ''){

        $session = static::openSession();
        $session->set('type','success');
        $session->set('msg',$msg);

    }

    /**
     * 正常信息输出
     * @param string $msg
     */
    public static function info($msg = ''){

        $session = static::openSession();

        $session->set('type','info');
        $session->set('msg',$msg);
    }

    /**
     * 警告信息
     * @param string $msg
     */
    public static function warning($msg = ''){

        $session = static::openSession();

        $session->set('type','warning');
        $session->set('msg',$msg);

    }

    /**
     * 危险警告信息
     * @param string $msg
     */
    public static function danger($msg = ''){

        $session = static::openSession();
        $session->set('type','danger');
        $session->set('msg',$msg);
    }

    /**
     * 检查session是否开启
     * @return mixed|\yii\web\Session
     */
    public static function openSession(){

        $session = Yii::$app->session;
        // 检查session是否开启
        if ($session->isActive){

            $session->open();
        }
        return $session;
    }

}