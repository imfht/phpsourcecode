<?php

class UserController extends Controller
{
    protected function menus()
    {
        return array(
            'user',
        );
    }

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$criteria=new CDbCriteria(array(
        	'order'=>'id desc',
    	));

        if(!empty($_GET['User']['username']))
            $criteria->addSearchCondition('username',$_GET['User']['username']);

        if(isset($_GET['User']['status'])) {
            $criteria->compare('status', $_GET['User']['status']);
        } else {
            $criteria->addNotInCondition('status', array(Yii::app()->params['status']['isdelete']));
        }

	    $dataProvider = new CActiveDataProvider('User',array(
			'criteria'=> $criteria,
			'pagination'=>array(
        		'pageSize'=>Yii::app()->params['girdpagesize'],
    		),
		));


		$this->render('index',array(
			'dataProvider'=> $dataProvider,
            'model' => AdminUser::model(),
//			'categorys'=> Category::model()->showAllSelectCategory(Yii::app()->params['module']['article'],Category::SHOW_ALLCATGORY),
		));
	}

	public function actionCreate()
	{
		$model = new User;

		if(isset($_POST['User']))
		{
			$model->attributes = $_POST['User'];

			if($model->save()){
				Yii::app()->user->setFlash('actionInfo',Yii::app()->params['actionInfo']['saveSuccess']);
				$this->refresh();
			}else if($model->validate()){
				Yii::app()->user->setFlash('actionInfo',Yii::app()->params['actionInfo']['saveFail']);
				$this->refresh();
			}
		}

		$this->render('create',array(
			'model' => $model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);

		if(!empty($_POST['User']))
		{
            $pwd = $model->password;
			$model->attributes = $_POST['User'];

            if ($model->password != "password") { // 如果填写了密码则重新设置
                $model->password = User::encrpyt($model->password);
            } else {
                $model->password = $pwd;
            }

			if($model->save()){
				Yii::app()->user->setFlash('actionInfo',Yii::app()->params['actionInfo']['updateSuccess']);
				$this->redirect(array('index','menupanel' => $_GET['menupanel']));
			}else if($model->validate()){
				Yii::app()->user->setFlash('actionInfo',Yii::app()->params['actionInfo']['updateFail']);
				$this->redirect(array('index','menupanel' => $_GET['menupanel']));
			}
		}
		$this->render('update',array(
			'model'=> $model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=User::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

}
