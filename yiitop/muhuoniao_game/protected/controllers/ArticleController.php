<?php

class ArticleController extends Controller
{
	public function actions()
	{
		return array(
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xC3C3C3,    //背景颜色
				'padding'=>1,              //文字周边填充大小
				//'foreColor'=>0x204000,   字体颜色
				'offset'=>-2,        //设置字符偏移量
				'maxLength'=>4,       // 最多生成几个字符 
				'minLength'=>4,       // 最少生成几个字符 
				'height'=>'29px',
				'width'=>'63px',
			),
		);
	}
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	 
	public $layout='//layouts/article';

	
	public function actionAjaxLogin()
	{
		$returnName = Member::model()->findByAttributes(array('mname'=>trim($_POST['username'])));
		if(empty($returnName)){
			echo 'namenull';
		}
		elseif($returnName->password!=Member::model()->encrypt($_POST['password'])){
			echo 'passworderror';
		}
		elseif($this->createAction('captcha')->getVerifyCode()!=$_POST['code']){
			echo 'verifyCodeerror';
		}else{
			echo 'success';
		}
	}
	

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView()
	{
		$criteria = new CDbCriteria();
		$criteria->condition = 'id='.$_GET['gid'].' and gid='.$_GET['id'];

		$model = Article::model()->find($criteria);
		
		$this->render('view',array(
			'model'=>$model,
		));
	}



	/**
	 * Lists all models.
	 */
	public function actionIndex($id)
	{
		$criteria = new CDbCriteria();
		$criteria->order='create_time DESC';
		$criteria->condition='gid='.$id.' and display=1';
		$model = Article::model()->findAll($criteria);
		
		if($model==null)
		{
			throw new CHttpException(404,'The requested page does not exist.');
		}else{
			$this->render('index',array(
				'model'=>$model,
			));
		}
	}
	public function actionLogin(){
		
			
		if(!Yii::app()->user->isGuest){
			$this->redirect(array('site/index'));
		}
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			if($model->validate() && $model->login()){
				
				$this->redirect(Yii::app()->request->urlReferrer);
			}else{
				$returnName = Member::model()->findByAttributes(array('mname'=>trim($_POST['LoginForm']['username'])));
				if(empty($returnName)){
					echo 'namenull';
				}elseif($returnName->password!=Member::model()->encrypt($_POST['LoginForm']['password'])){
					echo 'passworderror';
				}else{
					echo 'verifyCodeerror';
				}
				
			}
		}
		
		// display the login form
		$this->render('login',array('model'=>$model));
	}
	public function actionList()
	{
		$criteria = new CDbCriteria();
		$criteria->order='create_time DESC';
		$criteria->condition='display=1 and gid='.$_GET['id'].' and tid='.$_GET['tid'];
		$count = Article::model()->count($criteria);
		
		$pager = new CPagination($count);
		$pager->pageSize=8;//每页显示几条数据
		$pager->applyLimit($criteria);
		$models = Article::model()->findAll($criteria);
		if($models==null)
			throw new CHttpException(404,'The requested page does not exist.');
		$this->render('list',array('models'=>$models,'pages'=>$pager));
	}
	

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Article::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
}
