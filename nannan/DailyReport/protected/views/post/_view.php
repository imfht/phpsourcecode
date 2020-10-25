<div class="post">
<?php $this->beginWidget('bootstrap.widgets.TbHeroUnit',array(
	'heading'=>$data->title,
));?>
	</br>
		<?php if($data->id%2==1){
				echo '<div class="content" style="color:red;font-size:140%;font-style:normal">';
			}
			else
				echo '<div class="content" style="color:green;font-size:150%;font-style:normal">';
			echo '<p>'.CHtml::encode($data->content).'</P>';
			echo '</div>';
		?>
	<div class="author" style="text-align:right">
		发表自：<?php echo $data->post_time.'---'.$data->author->name;?>
	</div>
<?php $this->endWidget();?>
</div>