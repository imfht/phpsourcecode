<?php

/**
 * Class FriendlinkController
 */
class FriendlinkController extends Controller
{
    protected function menus()
    {
        return array(
            'friendlink',
        );
    }

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$criteria=new CDbCriteria();

        if(!empty($_GET['FriendLink']['title']))
            $criteria->addSearchCondition('title',$_GET['FriendLink']['title']);


        $criteria->addNotInCondition('status', array(Yii::app()->params['status']['isdelete']));

		$dataProvider=new CActiveDataProvider('FriendLink',array(
			'criteria'=>$criteria,
			'pagination'=>array(
        		'pageSize'=>Yii::app()->params['girdpagesize'],
    		),
            'sort'=>array(
                'defaultOrder'=>array(
                    'id' => CSort::SORT_DESC,
                ),
                'attributes'=>array(
                    'id',
                    'createtime',
                    'sort',
                ),
            ),
		));
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
			'categorys'=> Category::model()->showAllSelectCategory(Category::SHOW_ALLCATGORY),
            'model' => FriendLink::model(),
		));
	}

	public function actionCreate()
	{
		$model=new FriendLink;

		if(isset($_POST['FriendLink']))
		{
			$model->attributes=$_POST['FriendLink'];
			$upload = CUploadedFile::getInstance($model,'imagefile');
			if(!empty($upload))
			{
				$model->imgurl = Upload::createFile($upload,'friendlink','create');
			}
			if($model->save()){
				Yii::app()->user->setFlash('actionInfo',Yii::app()->params['actionInfo']['saveSuccess']);
				$this->refresh();
			}else if($model->validate()){
				Yii::app()->user->setFlash('actionInfo',Yii::app()->params['actionInfo']['saveFail']);
				$this->refresh();
			}
		}
		$this->render('create',array(
			'model'=>$model,
			'categorys'=>Category::model()->showAllSelectCategory(),
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
		if(!empty($_POST['FriendLink']))
		{
			$model->attributes=$_POST['FriendLink'];
			$upload=CUploadedFile::getInstance($model,'imagefile');
			if(!empty($upload))
			{
				$model->imgurl = Upload::createFile($upload,'friendlink','update',$model->imgurl);
			}

			if($model->save()){
				Yii::app()->user->setFlash('actionInfo',Yii::app()->params['actionInfo']['updateSuccess']);
				$this->redirect(array('index','menupanel'=>$_GET['menupanel'],'cid'=>$_GET['cid'],'title'=>$_GET['title']));
			}else if($model->validate()){
				Yii::app()->user->setFlash('actionInfo',Yii::app()->params['actionInfo']['updateFail']);
				$this->redirect(array('index','menupanel'=>$_GET['menupanel'],'cid'=>$_GET['cid'],'title'=>$_GET['title']));
			}
		}
		$this->render('update',array(
			'model'=>$model,
			'categorys'=>Category::model()->showAllSelectCategory(),
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=FriendLink::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
}
