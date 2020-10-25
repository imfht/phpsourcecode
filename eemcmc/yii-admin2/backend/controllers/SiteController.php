<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use backend\models\LoginForm;
use yii\filters\VerbFilter;

/**
 * Site controller
 */
class SiteController extends BaseController
{

	/**
	 * @inheritdoc
	 */
	public function actions()
	{
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
		];
	}

	/**
	 * 首页
	 */
	public function actionIndex()
	{
		if (empty($this->uid))
		{
			return $this->redirect('site/login');
		}
		return $this->render('index');
	}

	/**
	 * 登录
	 */
	public function actionLogin()
	{
		// echo Yii::$app->security->generatePasswordHash('111111');exit;
		if (!\Yii::$app->user->isGuest)
		{
			return $this->goHome();
		}

		$model = new LoginForm();
		if ($model->load(Yii::$app->request->post()) && $model->login())
		{
			return $this->goHome();
		}
		else
		{
			return $this->render('login', [
						'model' => $model,
			]);
		}
	}

	/**
	 * 退出登录
	 */
	public function actionLogout()
	{
		Yii::$app->user->logout();

		return $this->redirect('/site/login');
	}

}
