<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/15
 * Time: 22:10
 */

namespace backend\controllers\Admin;


use backend\controllers\BaseController;
use Yii;
class AuthController extends BaseController{

    public  $layout = false;
    private $authService;

    /**
     * 初始化
     */
    public function init()
    {
        $this->authService = Yii::createObject('authservice');
    }
    /**
     * 登录界面
     * @return string
     */

    public function actionLogin(){

        return $this->render('login');
    }

    /**
     * 登录处理
     * @return string
     */
    public function actionAuthlogin(){

        $request = Yii::$app->request;
        if($request->isPost){

                $model = $this->authService->Login($request->post());

                if(is_object($model)){

                    return $this->render('login',['model'=>$model,'error'=>$model->errors]);

                }else{

                    return $this->redirect('/Admin/main/main');
                }
        }
    }
}