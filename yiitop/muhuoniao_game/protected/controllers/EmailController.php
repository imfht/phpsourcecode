<?php
class EmailController extends Controller{
	public $layout='//layouts/emailvalidate';
	public function actionIndex(){
		if($_POST['mname']&&$_POST['email']){
			$resetMname= Member::model()->findByAttributes(array('mname'=>$_POST['mname'],'email'=>$_POST['email']));
			if($resetMname){
				$expirationTime=time()+60*30;
				$expirationTime=  base64_encode($expirationTime);
				$mail  =Yii::app()->mailer;
				$message = "亲爱的".$_POST['mname']."，您好:<br/>如果您忘记了密码请点击如下链接，修改您的密码：<br><a href='".Yii::app()->params['returnHost']."email/email.html?mid=".$resetMname->id."&key=".$expirationTime."'>".Yii::app()->params['returnHost']."email/email.html?mid=".$resetMname->id."&key=".$expirationTime."</a><br>如果点击打不开连接请复制上边地址到浏览器的地址栏即可。";
				$mail->MsgHTML($message);
				$mail->Host = 'smtp.exmail.qq.com';
				$mail->Port = 25;     
				$mail->IsSMTP();
				$mail->SMTPAuth= true; 
				$mail->CharSet = 'UTF-8';
				$mail->Username = "918@ryvip.com";//你的用户名，或者完整邮箱地址
				$mail->Password = "1ren2yu3ruan4jian";//邮箱密码
				$mail->SetFrom('918@ryvip.com', '918游戏网');//发送的邮箱和发送人
				$mail->AddAddress($_POST['email']);
				$mail->Subject = '密码找回';
				$mail->Body =$message;
				
				if ($mail->Send()) {
					Yii::app()->session['mid']=$resetMname->id;
					header("Location:".Yii::app()->params['returnHost']);
				}else{
					header("Location:".Yii::app()->params['returnHost']);
				}
			}else{
				throw new CHttpException(400,'您的个人资料里邮箱没有填写或者邮箱与用户名不一致！');
			}
			
		}else{
			$this->render('index');
		}
				
	}
	public function actionEmail(){
		if(empty(Yii::app()->session['mid'])){
			throw new CHttpException(400,'链接已失效！');
			exit;
		}
	
		if($_POST&&($_POST['password']==$_POST['repassword'])){
			/*if(empty($_GET['mid'])){
				header("Content-Type: text/html; charset=utf-8");
				echo "<script>alert('没有此用户！');</script>";
				exit;
			}*/
            $returnValue=Member::model()->updateAll(array('password'=>  Member::model()->encrypt($_POST['password'])),"id=".Yii::app()->session['mid']);
			if($returnValue>0){
				unset(Yii::app()->session['mid']);
				header("Location:".Yii::app()->params['returnHost']);
			}else{
				echo "shibai";
			}
		}else{
			if($_GET['mid']&&$_GET['key']){
				if($_GET['mid']!=Yii::app()->session['mid']){
					header("Content-Type: text/html; charset=utf-8");
					echo "<script>alert('用户名不匹配！');</script>";
					exit;
				}
				if(base64_decode($_GET['key'])<time()){
					header("Content-Type: text/html; charset=utf-8");
					echo "<script>alert('时间超时请重新申请！');</script>";
					exit;
				}

			}else{
				header("Content-Type: text/html; charset=utf-8");
				echo "<script>alert('用户名或秘钥为空！');</script>";
				exit;
			}
		}
		
		$this->render('email');

		
	}
}


