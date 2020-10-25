<?php
/**
 * Created by PhpStorm.
 * User: xu.gao
 * Date: 2016/2/2
 * Time: 10:42
 */

namespace backend\behaviors;


use yii\base\Behavior;
use yii\base\Controller;
use Yii;
use yii\web\Response;
use yii\helpers\Url;
class AuthBehaviors extends Behavior{

    public $ZM = '';

    //定义事件
    public function events()
    {
        return [Controller::EVENT_BEFORE_ACTION =>'beforeAuth'];
    }
    public function beforeAuth($event){

        $uid = Yii::$app->user->getId();
        $url = Url::current();
        //防止重定向循环
        if($url != '/auth/login') {

            if (!$uid) {

                return $this->ZM->redirect('/auth/login');

            }/*else{

                //判断 是否有该权限
                $auth_item_name = str_replace('/' , '.', strtolower(Yii::$app->controller->route));


                if(Yii::$app->user->can($auth_item_name)){

                    return true;
                }else{

                     if($auth_item_name == 'admin.main.main' || $auth_item_name == 'admin.user.logout'){

                         return true;
                     }

                    throw new \yii\web\UnauthorizedHttpException('对不起，您现在还没获此操作的权限');
                }

            }*/
        }
    }
}