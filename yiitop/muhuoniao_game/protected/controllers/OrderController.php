<?php

class OrderController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/order';

	/**
	 * @return array action filters
	 */
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
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','ajax'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionIndex()
	{
		$model=new Order;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		if(empty(Yii::app()->user->id)){
			throw new CHttpException(400,'您目前还没有登录请登录！');
			exit;
		}
		$email=  Member::model()->findByAttributes(array('id'=>Yii::app()->user->id));
		if(empty($email->email)){
			throw new CHttpException(400,'您的个人资料里邮箱没有填写！');
			exit;
		}
		if($email->email_validate==0){
			throw new CHttpException(400,'您的个人资料里邮箱没有验证！');
			//header("Location: ../member/update/".Yii::app()->user->id.".html");
			
		}
		if(isset($_POST['Order']))
		{
			$model->attributes=$_POST['Order'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	
	public function actionAjax(){
		$model=new Games;
		if($_POST['gid']){
			$gameServerId= $model->getGamesServerId($_POST['gid']);
			if($gameServerId){
				//$returnValue='<select>';
				$i=1;
				foreach ($gameServerId as $row){
					$returnValue.='<option value="'.$row.'">918双线'.$i.'区</option>';
					$i++;
				}
				echo $returnValue;
			}
		}
		
		
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Order::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='order-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
