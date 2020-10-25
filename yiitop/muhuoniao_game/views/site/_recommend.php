
<!--<li><?php echo CHtml::link($data->gname,array('','id'=>$data->id));?></li>-->
<li> 
	<img src="http://918s-game.stor.sinaapp.com/<?php echo $data->imgurl;?>" width="215" height="130"/>
    <p>
		<a href="<?php echo Yii::app()->request->baseUrl; ?>/article/index/id/<?php echo $data->id;?>"><?php echo $data->gname;?></a>
		<span><?php $gameType=Yii::app()->params['gametype'];echo $gameType[$data->type]; ?></span><br />
	</p>
    <p>
		<a href="#" style="padding-right:20px;" >
			<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/new.jpg"/>
		</a>
		<a href="<?php echo Yii::app()->request->baseUrl; ?>/article/index/id/<?php echo $data->id;?>">
			<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/come.jpg" />
		</a>
	</p>
</li>
