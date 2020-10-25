<?php
class DailyreportController extends Controller
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
				'actions'=>array('index','open','remind'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('view','create','update','show'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete','open','remind'),
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
	public function actionView()
	{
		$model=$this->loadMyModel();
		if($model===null)
		{
			$this->render('error');
		}
		else
		{
			$this->render('view',array(
					'model'=>$model,
			));
		}
	}
	public function actionOpen()
	{
		$this->render('open');
	}
	public function actionRemind()
	{
		$this->render('remind');
	}
	
	public static function sendCountEmails()
	{
		$mail=Yii::createComponent('application.extensions.mailer.EMailer');
		date_default_timezone_set('PRC');
		if(idate('H')>7){
			echo "sorry,发送时间不合适".idate('H');
			return false;
		}
		$message=User::model()->getCounts();
		//$message="hello";
		if($message!=null)
		{
			$mail->IsSMTP();
			$mail->Host='smtp.163.com';
			// $mail->Host='smtp.qq.com';
			//$mail->Port=25;
			$mail->Port=25;
			$mail->SMTPAuth=true;
			$mail->SMTPDebug=true;
			// $mail->Username='huisheng1826@163.com';
			$mail->Username='chuangyuanribao@163.com';
			// $mail->Password="huisheng";
			$mail->Password="123abcd8866";
			// $mail->From='huisheng1826@163.com';
			$mail->From='chuangyuanribao@163.com';
			// $mail->FromName='回声';
			$mail->FromName='冯老师';
			// $mail->AddReplyTo('huisheng1826@163.com');
			$mail->AddReplyTo('chuangyuanribao@163.com');
			$criteria=new CDbCriteria;
			$criteria->select=array('email','receive_email');
			$models=User::model()->findAll($criteria);
			$mail->AddAddress('961502093@qq.com');
			$mail->AddAddress('824513174@qq.com');
			$mail->AddAddress('759010589@qq.com');
			foreach($models as $model)
			{
				if($model->receive_email==1){}
					$mail->AddAddress($model->email);
			}
			$mail->CharSet='UTF-8';
			$mail->Subject='上周日报发送情况统计';
			$mail->Body=$message;
			$mail->IsHTML(true);
			$mm=6;
			do{
				if($mail->Send())
				{
					echo "发送成功";
					$ids=array();
					$ids=User::model()->getUserIds();
					foreach($ids as $id){
						User::model()->updateMyAllCount($id);
					}
					foreach($ids as $idd){
						User::model()->restoreWeekCount($idd);
					}
					return true;
				}
				$mm=$mm-1;
			}while($mm>0);
		}
		return false;
	}
	
	public static function sendEmails()
	{
		$mail=Yii::createComponent('application.extensions.mailer.EMailer');
		date_default_timezone_set('PRC');
		if(idate('H')<21){
			echo "sorry,发送时间太早".idate('H');
			return false;
		}
		$message=Dailyreport::model()->getReports();
		if($message!=null)
		{
			$mail->IsSMTP();
			$mail->Host='smtp.163.com';
			// $mail->Host='smtp.qq.com';
			//$mail->Port=25;
			$mail->Port=25;
			$mail->SMTPAuth=true;
			$mail->SMTPDebug=true;
			// $mail->Username='huisheng1826@163.com';
			$mail->Username='chuangyuanribao@163.com';
			// $mail->Password="huisheng";
			$mail->Password="123abcd8866";
			// $mail->From='huisheng1826@163.com';
			$mail->From='chuangyuanribao@163.com';
			// $mail->FromName='回声';
			$mail->FromName='冯老师';
			// $mail->AddReplyTo('huisheng1826@163.com');
			$mail->AddReplyTo('chuangyuanribao@163.com');
			$criteria=new CDbCriteria;
			$criteria->select=array('email','receive_email');
			$models=User::model()->findAll($criteria);
			$mail->AddAddress('961502093@qq.com');
			$mail->AddAddress('824513174@qq.com');
			//$mail->AddAddress('759010589@qq.com');
			foreach($models as $model)
			{
				if($model->receive_email==1)
					$mail->AddAddress($model->email);
			}
			$mail->CharSet='UTF-8';
			$mail->Subject='日报';
			$mes=Dailyreport::model()->getNoReportNames();
			if(!empty($mes)){
				$mes.=$message;
				$mail->Body=$mes;
			}else{
				$mail->Body=$message;
			}
			$mail->IsHTML(true);
			$mm=6;
			do{
				if($mail->Send())
				{
					echo "发送成功";
					$ids=array();
					$ids=Dailyreport::model()->getNoReportList();
					foreach($ids as $id){
						User::model()->updateWeekCount($id);
					}
					return true;
				}
				$mm=$mm-1;
			}while($mm>0);
		}
		return false;
	}

	public static function sendRemindEmails()
	{
		$criteria=new CDbCriteria;
		$criteria->condition='datediff(create_time,curdate())=0';
		$dmodels=Dailyreport::model()->findAll($criteria);
		if($dmodels!=null)
		{
			$mail=Yii::createComponent('application.extensions.mailer.EMailer');
			$message='日报提醒：亲，今天您还没有发日报，请尽快在22:30之前发，谢谢。';
			$mail->IsSMTP();
			$mail->Host='smtp.163.com';
			$mail->Port=25;
			$mail->SMTPAuth=true;
			$mail->SMTPDebug=true;
			$mail->Username='huisheng1826@163.com';
			$mail->Password="huisheng";
			$mail->From='huisheng1826@163.com';
			$mail->FromName='回声';
			$mail->AddReplyTo('huisheng1826@163.com');
			$mail->CharSet='UTF-8';
			$mail->Subject='亲，赶紧发日报啊';
			$mail->Body=$message;
			$mail->IsHTML(true);
			$criteria=new CDbCriteria;
			$criteria->select=array('id','email','receive_remind');
			$models=User::model()->findAll($criteria);
			$crit=new CDbCriteria;
			$crit->condition='datediff(create_time,curdate())=0 and author_id=:id';
			$flag=0;
			foreach($models as $model)
			{
				if($model->receive_remind==1)
				{
					// $crit=new CDbCriteria;
					// $crit->condition='datediff(create_time,curdate())=0 and author_id=:id';
					$crit->params=array(':id'=>$model->id);
					$mode=Dailyreport::model()->find($crit);
					if($mode==null)
					{
						$flag=1;
						$mail->AddAddress($model->email);
					}
				}
			}
			// $mm=5;
			// do{
				if($flag==1 && $mail->send())
				{
					echo '<h2>发送成功</h2>';
					return true;
				}
				// $mm=$mm-1;
			// }while($mm>0);
			else{
				echo '<h2>当前没有人需要提醒</h2>';
				return false;
			}
		}
	}
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Dailyreport;
		Yii::app()->format->datetimeFormat='m/d h:i:s A';
		// Uncomment the following line if AJAX validation is needed
		//$this->performAjaxValidation($model);

		if(isset($_POST['Dailyreport']))
		{
			$model->attributes=$_POST['Dailyreport'];
			date_default_timezone_set('PRC');
			if(User::model()->checkUserOff(Yii::app()->user->id)==1){
				Yii::app()->user->setFlash('warning','您已经请假了，今天不能发日报，谢谢。');
				$this->render('create',array(
					'model'=>$model,
				));
				return;
			}
			if(idate('H')<18){
				Yii::app()->user->setFlash('warning','请在18:00以后发日报，谢谢。');
				$this->render('create',array(
					'model'=>$model,
				));
				return;
			}
			$content=trim($model->content);
			$arr=preg_split("/[\s*\t*]/",$content);
			$realCon=trim(join("",$arr));
			if(strlen($realCon)<130){
				$remain=130-strlen($realCon);
				$cnum=(int)($remain/3)+1;
				$mess="还需要".$cnum."个汉字或者".$remain."个英文字符。";
				Yii::app()->user->setFlash('warning',$mess);
				$this->render('create',array(
					'model'=>$model,
				));
				return;
			}
			if(strlen($realCon)>360){
				$remain=strlen($realCon)-360;
				$cnum=(int)($remain/3)+1;
				$mess="字数太多,多了".$cnum."个汉字或者".$remain."个英文字符。";
				Yii::app()->user->setFlash('warning',$mess);
				$this->render('create',array(
					'model'=>$model,
				));
				return;
			}
			if(strlen($realCon)>=130 && $model->save())
				$this->redirect(array('index'));
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
	public function actionUpdate()
	{
		$model=$this->loadMyModel();

		// Uncomment the following line if AJAX validation is needed
		//$this->performAjaxValidation($model);
		if($model===null)
		{
			$this->render('error');
		}
		else
		{
			if(isset($_POST['Dailyreport']))
			{
				$model->attributes=$_POST['Dailyreport'];
				if($model->save())
					$this->redirect(array('view','id'=>$model->id));
			}

			$this->render('update',array(
				'model'=>$model,
			));
		}
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	
	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$model=new Dailyreport('search');
		// $model->unsetAttributes();  // clear any default values
		if(isset($_GET['Dailyreport']))
			$model->attributes=$_GET['Dailyreport'];

		$this->render('index',array(
			'model'=>$model,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Dailyreport('search');
		// $model->unsetAttributes();  // clear any default values
		if(isset($_GET['Dailyreport']))
			$model->attributes=$_GET['Dailyreport'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Dailyreport the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		//$model=Dailyreport::model()->findBySql("select *from tbl_dailyreport where author_id=:id and datediff(create_time,curdate())=0",array(':id'=>Yii::app()->user->id));
		$model=Dailyreport::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	public function loadTodayModel()
	{
		$model=Dailyreport::model()->findBySql("select *from tbl_dailyreport where author_id=:id and datediff(create_time,curdate())=0",array(':id'=>Yii::app()->user->id));
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	public function actionShow()
	{
		$model=new Dailyreport('search');
		// $model->unsetAttributes();  // clear any default values;
		if(isset($_GET['Dailyreport']))
			$model->attributes=$_GET['Dailyreport'];
		$acdata=$model->searchMyinfo();
		if($acdata->getItemCount()==0)
		{
			$this->render('error');
		}
		else
		{
			$this->render('show',array(
				'model'=>$model,
			));
		}
	}
	public function loadMyModel()
	{
		$model=Dailyreport::model()->findBySql("select *from tbl_dailyreport where author_id=:id and datediff(create_time,curdate())=0 order by create_time desc limit 0,1",array(':id'=>Yii::app()->user->id));
		return $model;
	}
	/**
	 * Performs the AJAX validation.
	 * @param Dailyreport $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='dailyreport-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
