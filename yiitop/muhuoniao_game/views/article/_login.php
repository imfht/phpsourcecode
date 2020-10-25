<!--登陆开始-->
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl;?>/js/jquery.valiArticleLogin.js"></script>
 <?php
	if(empty(Yii::app()->user->id)){
		$modelLogin=new LoginForm;
		$form=$this->beginWidget('CActiveForm', array(
		'id'=>'login-form',
		'action'=>Yii::app()->request->baseUrl.'/article/login',
		'enableClientValidation'=>true,
		'clientOptions'=>array(
			'validateOnSubmit'=>true,
		),
		'htmlOptions'=>array('enctype'=>'multipart/form-data','onSubmit'=>'return change()'),
		)); 
		echo '<div id="login" style="display:block;">
			  <table width="270" border="0">
				<tr>
				  <td>帐号</td>
				  <td>'.$form->textField($modelLogin,'username').'</td>
				</tr>
				<tr>
				  <td>密码</td>
				  <td>'.$form->passwordField($modelLogin,'password').'</td>
				</tr>
				<tr>
				  <td>验证码</td>
				  <td>';
				  echo CHtml::activeTextField($modelLogin,'verifyCode',array('size'=>10,'maxlength'=>10,'autocomplete'=>'on'));
			$this->widget('CCaptcha',array('showRefreshButton'=>array('style'=>'padding-left:-20px;'),'clickableImage'=>true,'imageOptions'=>array('alt'=>'点击换图','title'=>'点击换图','style'=>'cursor:pointer;')));
            
				echo '</td>
				</tr>
				<tr>
				  <td height="30">&nbsp;</td>
				  <td>'.CHtml::activeCheckBox($modelLogin,'rememberMe').$form->label($modelLogin,'rememberMe').'<a href="'.Yii::app()->request->baseUrl.'/email/" style="padding-left:30px;  color:#0033FF;">忘记密码</a></td>
				</tr>
				<tr>
				  <td height="48">&nbsp;</td>
				  <td><input type="image" src="'.Yii::app()->baseUrl.'/images/star05.jpg" /></td>
				</tr>
			  </table>
			<div id="login_right">
			  <p>*918账号可直接登入</p>
			  <a href="'.Yii::app()->request->baseUrl.'/register/"><img src="'.Yii::app()->baseUrl.'/images/instantly .jpg"  /></a> 
			  </div>
		  </div>';
		
		$this->endWidget();
	}else{
		echo '<div id="login_after" style="display:block;">
				<div id="login_after_left">
					<a href="'.Yii::app()->request->baseUrl.'/member/">'.Yii::app()->user->name.'</a>
					<p><a href="#">[修改密码]</a><a href="'.Yii::app()->request->baseUrl.'/site/logout/">[更换账号]</a></p>

					<p class="old">已登陆过的服务器：</p>
					<ul>';
						$memberGamesarr=MemberGames::model()->getMemberGames(Yii::app()->user->id);
						if($memberGamesarr){
							foreach($memberGamesarr as $value){
							$Gamesname=Games::model()->getGamesName($_GET['id']);
							$value=unserialize($value);
								if($value['gid']==$_GET['id']){
									$GetGamesServerId=Games::model()->getGamesServerValue($value['gid'],$value['serveridvalue']);
									echo '<li><a href="'.Yii::app()->request->baseUrl.'/gameslogin/index?gid='.$_GET['id'].'&gametype='.$Gamesname[1].'&serverid='.$GetGamesServerId.'">918游戏'.$GetGamesServerId.'区</a></li>';
								}

							}
						}
					echo '</ul>
				</div>

				<div id="login_after_right">
					<div class="login_after_01">
						<p>新区开放：</p>
						<ul>';
						$GamesServerId=Games::model()->getGamesServerId($_GET['id']);
						$Gamesname=Games::model()->getGamesName($_GET['id']);
						if($GamesServerId){
							echo '<li><a href="'.Yii::app()->request->baseUrl.'/gameslogin/index?gid='.$_GET['id'].'&gametype='.$Gamesname[1].'&serverid='.count($GamesServerId).'">918游戏'.count($GamesServerId).'区</a></li>';
						}
							
						echo '</ul>
					</div>
					<div class="login_after_02">
						<p>所有服务器：</p>
						<ul class="ul_scroll">';
						
						if($GamesServerId){
							for($i=count($GamesServerId);$i>0;$i--){
								$Gamesname=Games::model()->getGamesName($_GET['id']);
								echo '<li><a href="'.Yii::app()->request->baseUrl.'/gameslogin/index?gid='.$_GET['id'].'&gametype='.$Gamesname[1].'&serverid='.$i.'">918游戏'.$i.'区</a></li>';

							}

						}else{
							echo '暂时没有玩过任何游戏！';
						}
						echo '</ul>
					</div>
				</div>
			</div>';
	}
 ?>           


<!--登陆结束-->