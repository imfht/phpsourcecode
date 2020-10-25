<?php

class FileController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
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
				'actions'=>array('update','upload','download','alert'),
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
	public function actionAlert()
	{
		//$model=$this->loadModel($id);
		$type=array('Apples', 'Bananas');
		$type[2]='Oranges';
		//array_push($type,'Oranges');
		$this->render('alert',array(
			'name'=>'水果销售情况',
			'type'=>$type,
		));
	}
	public function actionDownload($id)
	{
		$model=$this->loadModel($id);
		// if(@$file=fopen($model->url,'r')){
		// Header("Content-type:application/octet-stream");
		// //Header("Accept-Ranges:bytes");
		// //Header("Accept-Length".filesize($model->url));
		// Header("Content-Disposition:attachment;filename=".$model->name);
		// //fread($file,filesize($model->url));
		// //fclose($file);
		// }
		// while(!@feof($file))
		// {
			// echo fread($file,1024);
		// }
		$ua=$_SERVER["HTTP_USER_AGENT"];
		$url=iconv("utf-8","gbk",$model->url);
		$iename=urlencode($model->name);
		$iename=str_replace("+","%20",$iename);
		$file = fopen($url,"r"); //打开文件
		Header("Content_type:application/octet-stream");
		Header("Accept-Ranges:bytes");
		Header("Accept-Length:".filesize($url));
		if(preg_match("/MSIE/",$ua))
		{
			Header('Content-Disposition:attachment;filename="'.$iename.'"');
		}
		else if(preg_match("/Firefox/",$ua))
		{
			Header('Content-Disposition:attachment;filename*="utf8\'\''.$model->name.'"');
		}
		else
		{
			Header('Content-Disposition:attachment;filename="'.$model->name.'"');
		}
		//$name=iconv("utf-8","gbk",$model->name);
		//Header("Content-Disposition:attachment;filename=".mb_convert_encoding($model->name,'GB2312','UTF-8'));
		//Header('Content-Disposition:attachment;filename*="utf8\'\'' . $model->name. '"');
		//Header('Content-Disposition:attachment;filename="'.$model->name.'"');
		echo fread($file,filesize($url));
		fclose($file);
		exit();
	}
	public function actionUpload()
	{
		$model=new File;
		//$this->performAjaxValidation($model);
		if(isset($_POST['File']))
		{
			$model->attributes=$_POST['File'];
			$file=CUploadedFile::getInstance($model,'url');
			$model->name=$file->name;
			$model->type=$file->extensionName;
			$model->size=($file->size)/1024/1024;
			$model->url='C:\wamp\www\mysite\upload\\'.$model->name;
			$url=iconv("utf-8","gbk",$model->url);
			$model->author_id=Yii::app()->user->id;
			if($model->save())
			{
				$file->saveAs(iconv("utf-8","gbk",$url));
				Yii::app()->user->setFlash('success','您已经成功上传文件 <strong>'.$model->name.'</strong> 到服务器，谢谢。');
				$this->refresh();
			}
		}
		Yii::app()->user->setFlash('info','您可以把一些'.'<strong>培训的资料</strong>'.'或者'.'<strong>好的资料</strong>'.'上传到服务器分享给大家，谢谢。');
		$this->render('upload',array(
			'model'=>$model,
		));
	}
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new File;

		// Uncomment the following line if AJAX validation is needed
		//$this->performAjaxValidation($model);

		if(isset($_POST['File']))
		{
			$model->attributes=$_POST['File'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
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

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['File']))
		{
			$model->attributes=$_POST['File'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
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
		$file=$this->loadModel($id);
		$path=iconv("utf-8","gbk",$file->url);
		if(!unlink($path))
		{
			echo '删除文件失败！';
		}
		else
		{
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		// $dataProvider=new CActiveDataProvider('File');
		// $this->render('index',array(
			// 'dataProvider'=>$dataProvider,
		// ));
		//$dataProvider=new CActiveDataProvider('File');
		$model=new File('search');
		$this->render('index',array(
			'dataProvider'=>$model->search(),
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new File('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['File']))
			$model->attributes=$_GET['File'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return File the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=File::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param File $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='file-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
