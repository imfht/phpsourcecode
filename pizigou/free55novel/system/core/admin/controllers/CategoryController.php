<?php

class CategoryController extends Controller
{
    protected function menus()
    {
        return array(
            'book',
        );
    }

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
        $criteria=new CDbCriteria(array(
            'select'=>'id,title,parentid,imgurl,isnav',
            'condition'=>"",
            'order'=>'sort desc',
        ));

        if(!empty($_GET['Category']['title']))
            $criteria->addSearchCondition('title',$_GET['Category']['title']);

        if(isset($_GET['Category']['isnav']))
            $criteria->compare('isnav', $_GET['Category']['isnav']);

        $criteria->addNotInCondition('status', array(Yii::app()->params['status']['isdelete']));

		$dataProvider=new CActiveDataProvider('Category',array(
			'criteria'=> $criteria,
			'pagination'=>array(
        		'pageSize'=>1000,
    		),	
		));
		$categoryList=array();
//		Category::model()->showAllCategory($categoryList,$dataProvider->getData());
//		$dataProvider->setData($categoryList);
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
			'category' => $categoryList,
            'model' => Category::model(),
		));
		
	}


	public function actionCreate()
	{
		$model=new Category;
		if(isset($_POST['Category']))
		{
			$model->attributes=$_POST['Category'];
            if ($model->shorttitle == "") {
                $model->shorttitle = H::getPinYin($model->title);
            }
			$upload=CUploadedFile::getInstance($model,'imagefile');
			if(!empty($upload))
			{
				$model->imgurl=Upload::createFile($upload,'category','create');
			}
			if($model->save()){
				Yii::app()->user->setFlash('actionInfo',Yii::app()->params['actionInfo']['saveSuccess']);
				$this->refresh();
			}else if($model->validate()){
				Yii::app()->user->setFlash('actionInfo',Yii::app()->params['actionInfo']['saveFail']);
				$this->refresh();
			}
		}else{
//			$model->module=$module;
		}
		$this->render('create',array(
			'model'=>$model,
			'categorys'=>Category::model()->showAllSelectCategory(Category::SHOW_TOPCATGORY),
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
		if(!empty($_POST['Category']))
		{
			$model->attributes=$_POST['Category'];
            if ($model->shorttitle == "") {
                $model->shorttitle = H::getPinYin($model->title);
            }
			$upload=CUploadedFile::getInstance($model,'imagefile');
			if(!empty($upload))
			{
				$model->imgurl=Upload::createFile($upload,'category','update',$model->imgurl);
			}
			if($model->save()){
				Yii::app()->user->setFlash('actionInfo',Yii::app()->params['actionInfo']['updateSuccess']);
				$this->redirect(array('index','menupanel'=>$_GET['menupanel']));
			}else if($model->validate()){
				Yii::app()->user->setFlash('actionInfo',Yii::app()->params['actionInfo']['updateFail']);
				$this->redirect(array('index','menupanel'=>$_GET['menupanel']));
			}
		}
		$this->render('update',array(
			'model'=> $model,
			'categorys'=>Category::model()->showAllSelectCategory(Category::SHOW_TOPCATGORY),
		));
	}
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Category::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
}
