<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	public $layout = 'main';

    public $menupanel;

	public function init()
	{
		if(Yii::app()->user->isGuest&&$this->id!=='site'){
			Yii::app()->user->setFlash('actionInfo','您尚未登录系统！');
			$this->redirect(array('site/login'));
		}

//		if(!empty($_GET['menupanel']))
//		{
//			$menupanel = explode('|',$_GET['menupanel']);
//			$this->menupanel = $menupanel;
//		}else{
//			$this->menupanel = explode('|','content|short');;
//		}

        $this->menupanel = $this->menus();
	}

    protected function menus()
    {
        return array(
            'content'
        );
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
			if($this->loadModel($id)->delete()){
				Yii::app()->user->setFlash('actionInfo',Yii::app()->params['actionInfo']['deleteSuccess']);
			}else {
				Yii::app()->user->setFlash('actionInfo',Yii::app()->params['actionInfo']['deleteFail']);
			}
			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect($_POST['returnUrl']);
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 * eq:if(isset($_POST['ajax']) && $_POST['ajax']==='article-form')
	 */

	protected function performAjaxValidation($model,$form)
	{
   		if(isset($_POST['ajax']) && $_POST['ajax']===$form)
   		{
        	echo CActiveForm::validate($model);
        	Yii::app()->end();
    	}
	}
	protected function girdShowImg($data)
	{
		if(!empty($data->imgurl))
			return true;
		else
			return false;
	}
	protected function showViewUrl($type,$data){
		return str_replace('admin.php','index.php',Yii::app()->createUrl("$type/view",array('id'=>$data->id)));
	}
}