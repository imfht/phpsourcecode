  <div id="game_01">
 <h1>充值记录</h1>
<p> 您的K点余额是：<strong>0</strong> K点</p>
<form method="get">
<strong>帐户明细</strong>
<select name="time" style="width:100px; display:inline;">
<option value="day">今天</option>
<option selected="selected" value="week">最近一周</option>
<option value="onemonth">最近一个月</option>
<option value="threemonth">最近三个月</option>
<option value="sixmonth">最近六个月</option>
</select>
<input type="submit" value="查询" />
</form>
 <table width="760" border="0">
 <thead>
 <tr>
 <th>订单号</th><th>游戏名称</th><th>充值金额</th><th>充值时间</th><th>是否充值成功</th>
 </tr>
 </thead>
 <tbody>
 


<?php foreach ($model as $model):?>
 <tr>
<td><?php echo $model->order_number;?></td><td><?php $gamesname=Games::model()->getGamesName($model->gid);echo $gamesname[0];?> <?php if($model->gid_server_id){$gamesserverid=Games::model()->getGamesServerValue($model->gid,$model->gid_server_id);if($gamesserverid<10){echo "00".$gamesserverid;}elseif($gamesserverid>9 && $gamesserverid<100){ echo "0".$gamesserverid;}else{ echo $gamesserverid;}}?>区</td><td><?php echo $model->price;?>元</td><td><?php echo date('Y-m-d H:i',$model->pay_time);?> </td><td><?php if($model->pay==1){echo "是";}else{echo "否";};?></td>
 </tr>
 <?php endforeach;?>
 </tbody>
 </table>
 <div class="page">


 <a class="page_y" href="<?php if($_GET['time']){echo $timevalue="?time=".$_GET['time']."&";}else{echo $timevalue="?";}?>page=<?php if($_GET['page']<2){echo "1";}else{echo $_GET['page']-1;}?>">上一页</a>
<?php for($i=1;$i<=$pageall;$i++){
		if($_GET['page']==$i){
			echo ' <strong>'.$i.'</strong>';
		}else{
			if($_GET['page']<1 &&$i==1){
				echo ' <strong>1</strong>';
			}else if($_GET['page']>$pageall &&$i==$pageall){
				echo ' <strong>'.$i.'</strong>';
			}else{
				echo '<a href="'.$timevalue.'page='.$i.'" class="page_new">'.$i.'</a>';
			}
			
		}
	  } ;?>
 <a  class="page_y" href="<?php if($_GET['time']){echo "?time=".$_GET['time']."&";}else{echo "?";}?>page=<?php if($_GET['page']<2){echo "1";}else{echo $_GET['page']+1;}?>"">下一页</a>
 </div>
 </div>
 

 
   <div class="mr_game">
  <h1>推荐游戏</h1>
  <ul>
   <?php 
     		$criteria = new CDbCriteria(array(
			'condition'=>'display=1 and flag=2',
			'limit'=>6,
			));	
     		$dataProvider = new CActiveDataProvider('Games',array(
     			'pagination'=>false,
     			'criteria'=>$criteria,
     		));
     		$this->widget('zii.widgets.CListView', array(
			'dataProvider'=>$dataProvider,
			'itemView'=>'_recommend',
			'summaryText'=>'',
			)); 
		?>  
  
  </ul>

  </div>