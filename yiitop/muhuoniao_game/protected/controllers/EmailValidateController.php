<?php
class EmailValidateController extends Controller{
	public $layout='//layouts/emailvalidate';
	public function actionIndex(){
		if(Yii::app()->user->id){
			$resetMname= Member::model()->findByAttributes(array('id'=>Yii::app()->user->id));
			if(empty($_POST['email'])&&empty($resetMname->email)){
					$this->render('index');
			}else{
				if($resetMname->email){
					$emailValue=$resetMname->email;
				}else{
					$emailValue=$_POST['email'];
					
				}
				if(!preg_match("/^[0-9a-zA-Z]+(?:[\_\-][a-z0-9\-]+)*@[a-zA-Z0-9]+(?:[-.][a-zA-Z0-9]+)*\.[a-zA-Z]+$/i", $emailValue)){
					header("Content-Type: text/html; charset=utf-8");
					if($resetMname->email){
						echo "邮箱不符合规则请修改邮箱链接是？";
						exit;
					}else{
						echo "<script language='javascript'>alert('邮箱不符合规则！');  location.reload();</script>";
						exit;
					}
					
				}
				if(empty($resetMname->email)){
					$returnValue=Member::model()->updateAll(array('email'=>  $_POST['email']),"id=".Yii::app()->user->id);
				}
				
				if($returnValue<0){
					header("Content-Type: text/html; charset=utf-8");
					echo "<script language='javascript'>alert('失败');  location.reload();</script>";
					exit;
				}
				if($resetMname->email_validate==0){
					$expirationTime=time()+60*30;
					$expirationTime=  base64_encode($expirationTime);
					$mail  =Yii::app()->mailer;
					$message = "亲爱的".$resetMname->mname."，您好:<br/>请点击如下连接完成操作：<br><a href='".Yii::app()->params['returnHost']."emailvalidate/email.html?mid=".$resetMname->id."&key=".$expirationTime."'>".Yii::app()->params['returnHost']."emailvalidate/email.html?mid=".$resetMname->id."&key=".$expirationTime."</a><br>如果点击打不开连接请复制上边地址到浏览器的地址栏即可。";
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
						header("Location: ".Yii::app()->params['returnHost']);
					}else{
						header("Location: ".Yii::app()->params['returnHost']);
					}
				}else{
					throw new CHttpException('邮箱验证','您的邮箱已经验证过了！');
				}
			}
			
			
			
			
		}else{
				throw new CHttpException(400,'请登陆！');
		}
				
	}
	public function actionEmail(){
		if(empty(Yii::app()->session['ValidateMid'])){
			throw new CHttpException(400,'链接已失效！');
			exit;
		}
		if(($_GET['mid']==Yii::app()->session['ValidateMid'])&&(base64_decode($_GET['key'])>time())){
			/*if(empty($_GET['mid'])){
				header("Content-Type: text/html; charset=utf-8");
				echo "<script>alert('没有此用户！');</script>";
				exit;
			}*/
                            $returnValue=Member::model()->updateAll(array('email_validate'=>1),"id=".Yii::app()->session['ValidateMid']);
			if($returnValue>0){
				unset(Yii::app()->session['ValidateMid']);
				$this->redirect(array('member/email'));
			}else{
				echo "shibai";
			}
		}else{
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


		}
		

	}
}


