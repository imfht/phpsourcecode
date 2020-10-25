<!---------------main---------------->

<div class="main">
<h1>欢迎登陆保定仁域网络科技有限公司后台管理系统</h1>

<p>您的用户名：<strong><?php echo Yii::app()->user->name;?></strong>&nbsp;&nbsp;&nbsp;&nbsp;您的管理权限：<strong><?php echo User::model()-> getStatusName(Yii::app()->user->id);?></strong> </p>
<table width="800">
<caption>统计信息</caption>
<tbody>
<tr class="ood">
<th class="w250">今日发表文章</th>
<td><strong><?php if($model['dayarticle']){ echo $model['dayarticle']; }else{ echo 0;}?></strong>篇</td> 
<th>今日更新文章</th><td><strong><?php if($model['uparticle']){echo $model['uparticle']; }else{ echo 0;} ?></strong>篇</td> 
<th>网站共有文章</th><td><strong><?php if($model['articletotal']){echo$model['articletotal']; }else{ echo 0;} ?></strong>篇</td>
</tr>
<tr class="ever"><th>
今日
<select id="games" onchange="game()">

<?php 
	$game=Games::model()->getGamesAllShow();
	print_r($game);
	foreach ($game as $key=>$value) {
		if($_GET['game']==$key){
			echo '<option value="'.$key.'" selected="selected">'.$value.'</option>';
		}else{
			echo '<option value="'.$key.'">'.$value.'</option>';
		}
		
	}
?>
</select>
<script type="text/javascript">
function game(){
	var gamevalue=$('#games').val();
	location.href="?game="+gamevalue;
}
function gamepay(){
	var gamevalue=$('#gamespay').val();
	location.href="?game="+gamevalue;
}
</script>

发表文章</th><td><strong><?php if($model['daygamearticle']){ echo $model['daygamearticle']; }else{ echo 0;}?></strong>篇</td>
<th> 今日更新文章</th><td><strong><?php if($model['upgamearticle']){ echo $model['upgamearticle']; }else{ echo 0;}?></strong>篇</td>  <th><span>游戏</span>共有文章</th><td><strong><?php if($model['gamearticle']){ echo $model['gamearticle']; }else{ echo 0;}?></strong>篇</td>
</tr>

<tr class="ood">
  <th>今日成交订单</th><td><strong><?php if($model['dayorder']){ echo $model['dayorder']; }else{ echo 0;}?></strong>笔</td>
  <th> 今日成交总额</th><td><strong><?php if($model['dayorderprice']){ echo $model['dayorderprice']; }else{ echo 0;}?></strong>元</td><th>  成交总额</th><td><strong><?php if($model['ordertotalprice']){ echo $model['ordertotalprice']; }else{ echo 0;}?></strong>元</td>
</tr>
<tr class="ever"><th>
今日
<select id="gamespay" onchange="gamepay()">

<?php 
	$game=Games::model()->getGamesAllShow();
	print_r($game);
	foreach ($game as $key=>$value) {
		if($_GET['game']==$key){
			echo '<option value="'.$key.'" selected="selected">'.$value.'</option>';
		}else{
			echo '<option value="'.$key.'">'.$value.'</option>';
		}
		
	}
?>
</select>
成交订单</th><td><strong><?php if($model['dayordergame']){ echo $model['dayordergame']; }else{ echo 0;}?></strong>笔</td>
<th>今日成交总额</th><td><strong><?php if($model['dayordergameprice']){ echo $model['dayordergameprice']; }else{ echo 0;}?></strong>元</td>
<th> <span>游戏</span>成交总额</th><td><strong><?php if($model['ordergameprice']){ echo $model['ordergameprice']; }else{ echo 0;}?></strong>元</td>
</tr>
<tr class="ood">
<th>共有注册会员</th><td><strong><?php if($model['membertotal']){ echo $model['membertotal']; }else{ echo 0;}?></strong>名</td><td colspan="4"></td>
</tr>
</tbody>
</table>
</div>


<!---------------main end---------------->