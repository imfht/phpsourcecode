<?php
/**
 * 广告管理
 * Class AdsController
 */
class AdsController extends Controller
{
    protected function menus()
    {
        return array(
            'ads',
        );
    }

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$criteria=new CDbCriteria();
//        $criteria->addCondition('status=:stauts');
//        $criteria->params[':status'] = Yii::app()->params['status']['ischecked'];

//        if(!empty($_GET['Ads']['title']))
//            $criteria->addSearchCondition('title',$_GET['Ads']['title']);
//
//        if(!empty($_GET['Ads']['author']))
//            $criteria->addSearchCondition('author',$_GET['Ads']['author']);
//
//    	if(!empty($_GET['Ads']['cid'])){
//    		$categoryList=array();
//    		$categoryList[] = $_GET['Ads']['cid'];
//			AdsCategory::model()->getAllCategoryIds($categoryList,AdsCategory::model()->findAll(), $_GET['Ads']['cid']);
//		    $criteria->addInCondition('cid',$categoryList);
//    	}

        $criteria->addNotInCondition('status', array(Yii::app()->params['status']['isdelete']));

		$dataProvider=new CActiveDataProvider('Ads',array(
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
                ),
            ),
		));
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
//			'categorys'=> AdsCategory::model()->showAllSelectCategory(AdsCategory::SHOW_ALLCATGORY),
            'model' => Ads::model(),
		));
	}

	public function actionCreate()
	{
		$model=new Ads;
//		$cid=$_GET['cid'];
//		if(!empty($cid))
//			$model->cid=$cid;
		if(isset($_POST['Ads']))
		{
			$model->attributes=$_POST['Ads'];
//			$upload = CUploadedFile::getInstance($model,'imagefile');
//			if(!empty($upload))
//			{
//				$model->imgurl = Upload::createFile($upload,'news','create');
//			}
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
//			'categorys'=>AdsCategory::model()->showAllSelectCategory(),
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
		if(!empty($_POST['Ads']))
		{
			$model->attributes=$_POST['Ads'];
//			$upload=CUploadedFile::getInstance($model,'imagefile');
//			if(!empty($upload))
//			{
//				$model->imgurl = Upload::createFile($upload,'news','update',$model->imgurl);
//			}

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
//			'categorys'=>AdsCategory::model()->showAllSelectCategory(),
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Ads::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
}
