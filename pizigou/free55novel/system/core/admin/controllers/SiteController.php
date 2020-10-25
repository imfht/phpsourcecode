<?php

class SiteController extends Controller
{
	public $defaultAction='login';

	public function actions()
	{
		return array(
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
		);
	}
	public function actionIndex()
	{
		if(Yii::app()->user->isGuest){
			Yii::app()->user->setFlash('actionInfo','您尚未登录系统！');
			$this->redirect(array('site/login'));
		}
		$this->redirect(array('system/index'));
	}
	public function actionLogin()
	{
		if(!Yii::app()->user->isGuest){
			$this->redirect(array('site/index'));
		}
		$this->layout = 'main-login';
		$model=new LoginForm;
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			$identity=new UserIdentity($model->username,$model->password);
			if($model->validate()){
				if($identity->authenticate()){
				Yii::app()->user->login($identity);
				$this->redirect(array('site/index'));
				}else{
					Yii::app()->user->setFlash('actionInfo','用户名或密码错误！');
					$this->refresh();
				}
			}
			
		}
		//$this->render('login',array('model'=>$model));
		$this->render('login',array('model'=>$model));
	}
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(array('site/login'));
	}
}