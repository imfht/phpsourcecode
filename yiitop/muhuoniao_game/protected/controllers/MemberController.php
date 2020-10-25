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
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
				//'users'=>User::model()->getAuthor(),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','order','updatePassword','updateData','email','idcard','updateHeadimg','updateEmail','emailValidateAjax','skip','ajax'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('@'),
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
		if($id!=Yii::app()->user->id){
			throw new CHttpException(404,'抱歉你找的页面不存在！');
		}else{
			$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
		}
		
	}
	
	public function actionOrder(){
		//$model=new Order;
		$criteria = new CDbCriteria();

		if($_GET['time']=="day"){
			$paytime=date("Y-m-d",time());
			$paytime=strtotime($paytime);
		}elseif($_GET['time']=="week"){
			$paytime=time()-60*60*24*7;
		}else if($_GET['time']=="onemonth"){
			$paytime=time()-60*60*24*30;
		}else if($_GET['time']=="threemonth"){
			$paytime=time()-60*60*24*90;
		}else if($_GET['time']=="sixmonth"){
			$paytime=time()-60*60*24*180;
		}else{
			$paytime=0;
		}

		$criteria->condition='pay_time>'.$paytime;
		$criteria->order='pay_time DESC';
		$count = Order::model()->count($criteria);
		
		$pager = new CPagination($count);
		$pager->pageSize=23;//每页显示几条数据
		$pager->applyLimit($criteria);
		$pageAll=ceil($count/$pager->pageSize);
		$model = Order::model()->findAll($criteria);
		/*if($model==null){
			throw new CHttpException(404,'The requested page does not exist.');
		}	*/

		$memberGames=  MemberGames::model()->findByAttributes(array('mid'=>Yii::app()->user->id));
		if($memberGames){
				for($i=1;$i<7;$i++){
					if($memberGames->{gid.$i}){
						$memberGamesarr[]=  unserialize($memberGames->{gid.$i});
					}
				}
		}
		$this->render('order',array(
			'model'=>$model,
			'memberGamesarr'=>$memberGamesarr,
			'pageall'=>$pageAll,

		));
	}
	
	public function actionEmail()
	{
		$model = $this->loadModel(Yii::app()->user->id);
		
		 if ($model->email == null)
		 {
		 	$this->redirect('updateEmail');
		 }
		
		$this->render('email',array(
			'model'=>$model,		
		));
	}
	
	public function actionEmailValidateAjax()
	{
		$resetMname= Member::model()->findByAttributes(array('id'=>Yii::app()->user->id));
		if($resetMname->email_validate==0){
			$emailValue=$resetMname->email;
			$expirationTime=time()+60*30;
			$expirationTime=  base64_encode($expirationTime);
			$mail  =Yii::app()->mailer;
			$message = "亲爱的".$resetMname->mname."，您好:<br/>请点击如下连接完成操 作：<br><a href='".Yii::app()->params['returnHost']."emailValidate/email?mid=".$resetMname->id."&key=".$expirationTime."'>".Yii::app()->params['returnHost']."emailvalidate/email.html?mid=".$resetMname->id."&key=".$expirationTime."</a><br>如果点击打不开连接请复制上边地址到浏览器的地址栏即可。";
			$mail->MsgHTML($message);
			$mail->Host = 'smtp.exmail.qq.com';
			$mail->Port = 25;     
			$mail->IsSMTP();
			$mail->SMTPAuth= true; 
			$mail->CharSet = 'UTF-8';
			$mail->Username = "918@ryvip.com";//你的用户名，或者完整邮箱地址
			$mail->Password = "1ren2yu3ruan4jian";//邮箱密码
			$mail->SetFrom('918@ryvip.com', '918游戏网');//发送的邮箱和发送人
			$mail->AddAddress($emailValue);
			$mail->Subject = '邮箱验证';
			$mail->Body =$message;
			if ($mail->Send()) {
				Yii::app()->session['ValidateMid']=$resetMname->id;
				echo 1;
			}else{
				echo 0;
			}
		}else{
			throw new CHttpException('邮箱验证','您的邮箱已经验证过了！');
		}
	}
	
	public function actionUpdateEmail()
	{
		$model = $this->loadModel(Yii::app()->user->id);
		if(isset($_POST['Member'])){
			$this->performAjaxValidation($model);
			$model->email = $_POST['Member']['email'];
			$model->email_validate = 0;
			if($model->save(true,array('email','email_validate')))
			{
				$this->redirect('email');
			}
		}
		
		$this->render('updateEmail',array(
			'model'=>$model,
		));
	}
	
	/*
	 * 头像设置
	 */
	public function actionUpdateHeadimg()
	{
		$model = $this->loadModel(Yii::app()->user->id);
		if(isset($_POST['Member'])){
			$model->headimg = CUploadedFile::getInstance($model, 'headimg');
			$this->performAjaxValidation($model,array('headimg'));
			if($model->validate(array('headimg')))
			{
				if(empty($model->headimg))
				{
					header('Content-Type: text/html; charset=utf-8;');
					echo "<script language='javascript'>alert('上传文件不能为空');window.location.href='updateHeadimg';</script>";
					exit;
				}
				list($usec, $sec) = explode(" ", microtime());
				$usec = substr($usec,2,2);
				$newFileName = 'headimg'.$sec.$usec.'.jpg';
				$uploadimage = new SaeStorage();
				$uploadimage->upload('headimg',$newFileName,$model->headimg->tempName);
				$newFile = "http://918s-headimg.stor.sinaapp.com/".$newFileName;
				$f = new SaeFetchurl();
				$img_data = $f->fetch($newFile);
				$img = new SaeImage();
				$img->setData( $img_data );
				if($_POST['w'] && $_POST['h']){
					//用户选中截图工具，按照对应坐标进行截图
					$x1 = $_POST['x1']/200;
					$w = ($_POST['x1']+$_POST['w'])/200;
					$y1 = $_POST['y1']/300;
					$h = ($_POST['y1']+$_POST['h'])/300;
					$img->crop($x1,$w,$y1,$h);
					$img->resize(100,100);
				}else{
					//用户直接提交，直接对图片进行缩放
					$img->resize(100,100);
				}
				$data = $img->exec();
				$uploadimage->write ('headimg', $newFileName, $data);
				$model->headimg = $newFileName;
				if($model->saveAttributes(array('headimg'))){
					$this->redirect(array('/member/index'));
				}
				
				
			}
		}
		
		$this->render('updateHeadimg',array(
			'model'=>$model,		
		));
	}
	
	/**
	 * 用户实名制调用
	 */
	public function actionIdcard()
	{
		$model = $this->loadModel(Yii::app()->user->id);
		if(isset($_POST['Member']))
		{
			$model->attributes = $_POST['Member'];
			$this->performAjaxValidation($model);
			if($model->save(true,array('real_name','id_card')))
			{
				$this->redirect('idcard');
			}
		}
		
		$this->render('idcard',array(
			'model'=>$model,
		));
	}
	
    /**
     * 用户修改个人资料时调用
     */
   public function actionUpdateData()
    {
        $model = $this->loadModel(Yii::app()->user->id);
        $this->performAjaxValidation($model);
        if(isset($_POST['Member'])){
        	$model->attributes = $_POST['Member'];
        	if($model->save(true,array('qq','telephone','address'))){
        		$this->redirect('skip');
        	}
        }
        $this->render('updateData',array(
            'model'=>$model,
        ));
    }

	/**
	 * 用户修改密码时请求的动作
	 */
	public function actionUpdatePassword()
	{
		$model = $this->loadModel(Yii::app()->user->id);
		if (isset($_POST['Password']))
		{
			if($_POST['Password']['old_password'] && $_POST['Password']['new_password'] && $_POST['Password']['again_password']){
				if($model->password == Member::model()->encrypt($_POST['Password']['old_password'])){
					if ($_POST['Password']['new_password']==$_POST['Password']['again_password']){
						if($_POST['Password']['old_password'] == $_POST['Password']['new_password']){
							header('Content-Type: text/html; charset=utf-8;');
							echo "<script language='javascript'>alert('新密码与旧密码不能相同');window.history.back(-1);;</script>";
							exit;
						}
                        $model->password = Member::model()->encrypt($_POST['Password']['new_password']);
						if($model->saveAttributes(array('password'))){
                            $this->redirect(array('member/skip'));
                        }else{
                            header('Content-Type: text/html; charset=utf-8;');
                            exit('密码修改失败');
                        }
					}else{
                            header('Content-Type: text/html; charset=utf-8;');
                            echo "<script language='javascript'>alert('二次输入不一致');  location.reload();</script>";
                            exit;
					}
				}else{
					header('Content-Type: text/html; charset=utf-8;');
					echo "<script language='javascript'>alert('原始密码不正确');  location.reload();</script>";
					exit;
				}
			}else{
				header('Content-Type: text/html; charset=utf-8;');
				echo "<script language='javascript'>alert('请正确输入密码');  location.reload();</script>";
				exit;
			}
		}	
			
		$this->render('updatePassword',array(
			'model'=>$model,
		));
	}
	
	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
			$memberMessage=Member::model()->getMemberMessage(Yii::app()->user->id);
			/*$memberHeadImg=$memberMessage->headimg;
			if(empty($memberHeadImg)){
				$memberHeadImg="uploads/headimg/headimg-default.jpg";
			}*/
			$memberGames=  MemberGames::model()->findByAttributes(array('mid'=>Yii::app()->user->id));
			$memberEmail=  Member::model()->findByAttributes(array('id'=>Yii::app()->user->id));
			/*if($memberGames){
				for($i=1;$i<7;$i++){
					if($memberGames->{gid.$i}){
						$memberGamesarr[]=  unserialize($memberGames->{gid.$i});
					}
				}
			}*/	
			$model = $this->loadModel(Yii::app()->user->id);
			
			if (isset($_POST['Member'])){
				$model->attributes = $_POST['Member'];
				if($model->save()){
					$this->redirect('index');
				}
			}
			$this->render('view',array(
			'model'=>$this->loadModel(Yii::app()->user->id),
			//'memberGamesarr'=>$memberGamesarr,
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
			//throw new CHttpException(404,'对不起你查找的页面不存在！');
			$this->redirect(Yii::app()->user->returnUrl);
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model,$array=null)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='member-form')
		{
			echo CActiveForm::validate($model,$array);
			Yii::app()->end();
		}
	}

	/**
	 *跳转页面 
	 */
	public function actionSkip()
	{
		$this->render('skip');
	}
	
	/**
	 *异步交互请求 
	 */
	public function actionAjax()
	{
		if(isset($_POST['password']))
		{
			$model = $this->loadModel(Yii::app()->user->id);
			$password = Member::model()->encrypt(strtolower(trim($_POST['password'])));
			if($model->password==$password){
				echo 'passwordTrue';
			}else{
				echo 'passwordFasle';				
			}
		}
	}
	
}








