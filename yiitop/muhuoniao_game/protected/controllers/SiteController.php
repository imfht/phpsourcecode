<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class SiteController extends Controller{
	public function actions()
	{
		return array(
			'assets'=>array(
                 'class'=>'SAEAssetsAction',
                 ),
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				//'page'=>array('class'=>'CViewAction',),				
				//'backColor'=>0xFFFFFF,
				'maxLength'=>4,       // 最多生成几个字符 
				'minLength'=>4,       // 最少生成几个字符 
				'height'=>'29px',
				'width'=>'63px',

			),
		);
	}
	
	public function actionIndex(){
		

		//$memberGames=MemberGames::model()->getMemberGames(Yii::app()->user->id);
		//$memberMessage=Member::model()->getMemberMessage(Yii::app()->user->id);
		$modelLogin=new LoginForm;
		/*if($memberGames){
				for($i=6;$i>0;$i--){
					if($memberGames->{gid.$i}){
						$memberGamesarr[]=  unserialize($memberGames->{gid.$i});
					}
				}
		}*/
		$this->render('index',array('modelLogin'=>$modelLogin));
	}
	public function actionRegister()
	{
		if(Yii::app()->user->id){
				$this->redirect(Yii::app()->user->returnUrl);
		}
		$model=new Member;

		// Uncomment the following line if AJAX validation is needed
		//$this->performAjaxValidation($model);

		if(isset($_POST['Member']))
		{
			
			$model->attributes=$_POST['Member'];
			if($_POST['Member']['password']&&$_POST['Member']['mname']&&$_POST['Member']['passwordrepeat']){
				$name= $model->findByAttributes(array('mname'=>$_POST['Member']['mname']));
				if($name){
					header("Content-Type: text/html; charset=utf-8");
					echo "<script language='javascript'>alert('用户名已经注册请选择其他的用户名！');  location.reload();</script>";
					exit;
				}
				if($_POST['Member']['password']!=$_POST['Member']['passwordrepeat']){
					/*throw new CHttpException(404,'两次输入的密码不一致！');
					$this->redirect(array('view','id'=>$model->id));*/
					header("Content-Type: text/html; charset=utf-8");
					echo "<script language='javascript'>alert('两次输入的密码不一致！');  location.reload();</script>";
					exit;
				}
				if($this->createAction('captcha')->getVerifyCode()!=$_POST['Member']['verifyCode']){
					header("Content-Type: text/html; charset=utf-8");
					echo "<script language='javascript'>alert('验证码输入错误！');  location.reload();</script>";
					exit;
				}
				if('agree'!=$_POST['Member']['clause']){
					header("Content-Type: text/html; charset=utf-8");
					echo "<script language='javascript'>alert('请仔细阅读条款！');  location.reload();</script>";
					exit;
				}
				
			}else{
				header("Content-Type: text/html; charset=utf-8");
				echo "<script language='javascript'>alert('请输入昵称或密码！');  location.reload();</script>";
				exit;
			}
			if($model->save(false)){
				$this->redirect(Yii::app()->user->returnUrl);
			}else{
				header("Content-Type: text/html; charset=utf-8");
				echo "<script language='javascript'>alert('真实姓名跟身份证格式不正确，姓名只能是中文，身份证是15到18位！');  location.reload();</script>";
				exit;
			}
		}
		

		$this->render('register',array(
			'model'=>$model,
		));
	}
	public function actionLogin(){	
		if(!Yii::app()->user->isGuest){
			$this->redirect(array('site/index'));
		}
		//$this->layout=false;
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
			// validate user input and redirect to the previous page if valid
			/*if($_POST['LoginForm']['verifyCode']==$this->createAction('captcha')->getVerifyCode()){
				if($model->validate() && $model->login()){
					
					//$this->redirect(Yii::app()->user->returnUrl);
					$this->redirect(Yii::app()->request->urlReferrer);
				}else{
					//var_dump($model->validate());var_dump($model->validate());var_dump($_POST['LoginForm']);echo "error";exit;
						Yii::app()->user->setFlash('actionInfo','用户名或密码错误！');
						$this->refresh();
					
				}
			}else{
					echo $_POST['LoginForm']['verifyCode']."|||".$this->createAction('captcha')->getVerifyCode();
			}*/
			if($model->validate() && $model->login()){
				
				//$this->redirect(Yii::app()->user->returnUrl);
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
	
	public function actionLogout()
	{
		Yii::app()->user->logout($identity);
		//$this->redirect(array('site/index'));
		$this->redirect(Yii::app()->request->urlReferrer);
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
	
	public function actionAjax()
	{
		if(isset($_POST['mname'])){
			$returnName = Member::model()->findByAttributes(array('mname'=>trim($_POST['mname'])));
			if(isset($returnName->mname)){
				echo 'mnamefalse';
			}
		}
		if(isset($_POST['code'])){
			if($this->createAction('captcha')->getVerifyCode()==$_POST['code']){
				echo 'codetrue';
			}else{
				echo 'codefalse';
			}
		}
	}
}
?>
