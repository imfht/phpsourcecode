<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class SiteController extends Controller{
	public function actions()
	{
		return array(
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
                'maxLength'=>4,       // 最多生成几个字符 
                'minLength'=>4,       // 最少生成几个字符 
				'height'=>'29px',
				'width'=>'63px',
				
			),
			
		);
	}
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		$model=new User;
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('@'),
				//'users'=>$model->getAuthorStatus(3),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('login','captcha','logout','error','gii'),
				'users'=>array('*'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>$model->getAuthorStatus(3),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	public function actionIndex(){
		$data=strtotime(date('Y-m-d',time()));
		$ArticleTotal=Article::model()->findAllByAttributes(array('display'=>1));
		$DayArticle=Article::model()->findAllBySql("select id from renyu_article where display=1 and create_time>:create_time",array(':create_time'=>$data));
		$UpArticle=Article::model()->findAllBySql("select id from renyu_article where display=1 and up_time>:up_time",array(':up_time'=>$data));
		$DayOrder=Order::model()->findAllBySql("select * from renyu_order where pay=1 and pay_time>:pay_time",array(':pay_time'=>$data));
		$OrderTotal=Order::model()->findAllByAttributes(array('pay'=>1));
		$MemberTotal=Member::model()->findAll();
		if($_GET['game']){
			$GameArticle=Article::model()->findAllByAttributes(array('display'=>1,'gid'=>$_GET['game']));
			$DayGameArticle=Article::model()->findAllBySql("select id from renyu_article where display=1 and gid=".$_GET['game']." and create_time>:create_time",array(':create_time'=>$data));
			$UpGameArticle=Article::model()->findAllBySql("select id from renyu_article where display=1 and gid=".$_GET['game']." and up_time>:up_time",array(':up_time'=>$data));
			$OrderGame=Order::model()->findAllByAttributes(array('pay'=>1,'gid'=>$_GET['game']));
			$DayOrderGame=Order::model()->findAllBySql("select * from renyu_order where pay=1 and gid=".$_GET['game']." and pay_time>:pay_time",array(':pay_time'=>$data));
		}else{
			$GameArticle=Article::model()->findAllByAttributes(array('display'=>1,'gid'=>1));
			$DayGameArticle=Article::model()->findAllBySql("select id from renyu_article where display=1 and gid=1 and create_time>:create_time",array(':create_time'=>$data));
			$UpGameArticle=Article::model()->findAllBySql("select id from renyu_article where display=1 and gid=1 and up_time>:up_time",array(':up_time'=>$data));
			$OrderGame=Order::model()->findAllByAttributes(array('pay'=>1,'gid'=>1));
			$DayOrderGame=Order::model()->findAllBySql("select * from renyu_order where pay=1 and gid=1 and pay_time>:pay_time",array(':pay_time'=>$data));
		}
		$DayOrder=CHtml::listData($DayOrder, 'id', 'price');
		$OrderTotal=CHtml::listData($OrderTotal, 'id', 'price');
		$OrderGame=CHtml::listData($OrderGame, 'id', 'price');
		$DayOrderGame=CHtml::listData($DayOrderGame, 'id', 'price');
		$model['articletotal']=count($ArticleTotal);
		$model['dayarticle']=count($DayArticle);
		$model['uparticle']=count($UpArticle);
		$model['gamearticle']=count($GameArticle);
		$model['daygamearticle']=count($DayGameArticle);
		$model['upgamearticle']=count($UpGameArticle);
		$model['dayorder']=count($DayOrder);
		$model['dayordergame']=count($DayOrderGame);
		$model['membertotal']=count($MemberTotal);
		$model['dayorderprice']=Order::model()->getNumOrder($DayOrder);
		$model['ordertotalprice']=Order::model()->getNumOrder($OrderTotal);
		$model['ordergameprice']=Order::model()->getNumOrder($OrderGame);
		$model['dayordergameprice']=Order::model()->getNumOrder($DayOrderGame);
		$ArticleModel=Article::model()->findAllBySql("select id from renyu_article where display=1 and create_time<:create_time",array(':create_time'=>$data));
		$this->render('index',array('model'=>$model));
	}
	public function actionLogin(){
		
		if(!Yii::app()->user->isGuest){
			$this->redirect(array('site/index'));
		}
		$model=new LoginForm;
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			
			Yii::app()->end();
		}

		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			if($model->validate() && $model->login()){
				$this->redirect(Yii::app()->user->returnUrl);
			}else{
				
					Yii::app()->user->setFlash('actionInfo','用户名或密码错误！');
					$this->refresh();
				
			}
		}
		
		$this->renderPartial('login',array('model'=>$model));
	}
	public function actionLogout()
	{
		Yii::app()->user->logout($identity);
		$this->redirect(array('site/login'));
	}
	public function actionError()
	{
		$layout='//layouts/emailvalidate';
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}
}
