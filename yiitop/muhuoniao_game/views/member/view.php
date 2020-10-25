  <div class="main_right_mr">
  <h2>账号信息</h2>
  <p>欢迎您，<strong><?php echo $model->mname; ?></strong> <!--您的K点余额是：<strong>0</strong> K点--></p>
  <p>您上次登录时间为：<strong><?php echo date('Y-m-d H:i:s',$model->login_time);?></strong></p>
  </div>
  <div class="main_right_mr">
  <h2>联系方式</h2>
  <p>电话：<?php if($model->telephone){echo $model->telephone;}else{echo "未填写电话！";}?></p>
  <p>QQ：<?php if($model->qq){echo $model->qq;}else{echo "未填写QQ！";}?>
  <p>地址：<?php if($model->address){echo $model->address;}else{echo "未填写家庭住址！";}?> </p>
  <a href="<?php echo Yii::app()->request->baseUrl;?>/member/updateData" style="color:red;text-decoration:none;">编辑 &gt&gt&gt</a>
  </div>
  
  <div class="main_right_mr">
  <h2>邮箱认证</h2>
  <?php if($model->email_validate=="1"): ?>
  	<p>您的邮箱已将认证成功</p>
  	<p>您的邮箱为：<?php echo substr_replace($model->email,'xxxxxxx',2,-8);?></p>
  <?php else:?>
  	<p>您的注册邮箱为: <span style="color:blue;"><?php echo $model->email;?></span></p>
  	<a href="<?php echo Yii::app()->request->baseUrl;?>/member/email" style="color:red;text-decoration:none;">立即认证&gt&gt&gt</a>
  <?php endif;?>


  </div>
  
  <div class="main_right_mr" style="border:none; padding-bottom:20px;">
  <h2>防沉迷认证</h2>

  <?php if ($model->id_card):?>
  	<p>姓名：<?php echo $model->real_name;?></p>
  	<p>身份证号：<?php echo substr_replace($model->id_card,'xxxxxxxxx',3,12);?></p>
  <?php else:?>
	<p>您尚未进行防沉迷认证！</p>
	<a href="<?php echo Yii::app()->request->baseUrl;?>/member/idcard" style="color:red;text-decoration:none;">立即认证&gt&gt&gt</a>
  <?php endif;?>
  <form>
  </form>
  </div>
  <div class="mr_game">
  <h1>玩过的游戏</h1>
  <ul>
  <?php
	$memberGamesarr=  MemberGames::model()->getMemberGames(Yii::app()->user->id);
	if($memberGamesarr){
		
		foreach($memberGamesarr as $value){
			$value=unserialize($value);
			$memberGamesname=Games::model()->getGamesName($value['gid']);
			if($memberGamesname){
				$memberGamesimages=Games::model()->getGamesImage($value['gid']);
				$GetGamesServerId=Games::model()->getGamesServerValue($value['gid'],$value['serveridvalue']);
				echo "<li><a target='_blanck' href='".Yii::app()->request->baseUrl."/gameslogin/index?gametype=".$memberGamesname[1]."&serverid=".$GetGamesServerId."'><img alt='".$memberGamesname[0]."-".$GetGamesServerId."区' title='".$memberGamesname[0]."-".$GetGamesServerId."区' height='64px' width='64px' src='http://918s-game.stor.sinaapp.com/".$memberGamesimages[1]."'/><p>".$memberGamesname[0]."</p></a></li>";
			}
			
		}
	}else{
		echo '暂时没有玩过任何游戏！';
	}
  ?>

  </ul>

  </div>