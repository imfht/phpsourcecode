<?php

class UsersController extends Controller
{

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/

    /**
     * 用户登录
     *
     */
    public function actionLogin(){
        if(Yii::app()->request->isPostRequest)
        {
            $res = Main::apiCodeInit(0);

            $model = new LoginForm();
            $model->username =@$_POST['email'];
            $model->password =@$_POST['password'];
            if($model->validate() && $model->login()) {
                $res = Main::apiCodeInit(1);
                $res['data'] =   Users::model()->find("email=:email",array('email'=>$model->username));
                die(CJSON::encode($res));
            }
            else
            {
                $err = $model->getErrors();

                $res['status']['errorinfo'] =  Main::getErrors($err);
                die(CJSON::encode($res));
            }


        }
    }

    /**
     * 用户注册
     */
    public function actionRegister(){
        $res = Main::apiCodeInit(0);

        if(Yii::app()->request->isPostRequest)
        {
            $model = new Users('create');
            $model->email = @$_POST['email'];
            if(@$_POST['password'])
                $model->password = Main::myMd5(@$_POST['password']);
            $model->username = preg_replace("#@[^\n]+#","",$model->email);
            $model->createtime = $model->updatetime = date("Y-m-d H:i:s");
            if($model->save())
            {
                $res = Main::apiCodeInit(1);
                $res['data'] =   $model;
                die(CJSON::encode($res));


            }
            else
            {
                $err = $model->getErrors();

                $res['status']['errorinfo'] =  Main::getErrors($err);
                die(CJSON::encode($res));

            }


        }
    }
}