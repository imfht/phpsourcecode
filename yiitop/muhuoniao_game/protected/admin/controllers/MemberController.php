<?php

class MemberController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/member';

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
		$model=new User;
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view','admin'),
				'users'=>$model->getAuthorStatus(3),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>$model->getAuthorStatus(2),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('delete','DeleteAll'),
				'users'=>$model->getAuthorStatus(2),
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
	public function actionCreate()
	{
		$model=new Member;
		if(isset($_POST['Member']))
		{
			$model->attributes=$_POST['Member'];
			$model->headimg=CUploadedFile::getInstance($model,'headimg');
			if($model->headimg){
				$newimg = 'imgpath_'.time().'_'.rand(1, 9999).'.'.$model->headimg->extensionName; //根据时间戳重命名文件名,extensionName是获取文件的扩展名
				$model->headimg->saveAs('uploads/headimg/'.$newimg);
				$model->headimg = 'uploads/headimg/'.$newimg;
			}
			if($model->save(false)){
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);
		if(isset($_POST['Member']))
		{
			//$_POST['Member']['password']=$model->encrypt($_POST['Member']['password'],$model->id);
			$model->attributes=$_POST['Member'];
			$model->headimg=CUploadedFile::getInstance($model,'headimg');
			if($model->headimg){
				$newimg = 'imgpath_'.time().'_'.rand(1, 9999).'.'.$model->headimg->extensionName; //根据时间戳重命名文件名,extensionName是获取文件的扩展名
				$model->headimg->saveAs('uploads/headimg/'.$newimg);
				$model->headimg = 'uploads/headimg/'.$newimg;
			}else {
				$model->headimg = $_POST['imgpath1'];
			}
			if($model->save()){
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$page = new CPagination();
		$page->pageSize = 18;
		$dataProvider=new CActiveDataProvider('Member',array(
			'pagination'=>$page,
		));
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
			'pages'=>$page,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Member('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Member']))
			$model->attributes=$_GET['Member'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Member::model()->findByPk((int)$id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='member-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
