<?php $this->beginContent('//layouts/main'); ?>
<!---------------side_left---------------->
<div id="side_left">
<h2>管理管理</h2>
<ul>
<li><?php echo CHtml::link('信息列表',array('index'));?></li>
<li><?php echo CHtml::link('创建文章',array('create'));?></li>
<li><?php echo CHtml::link('管理文章',array('admin'));?></li>
</ul>
</div>

<!---------------side_left end---------------->
<?php echo $content;?>
<?php $this->endContent(); ?>