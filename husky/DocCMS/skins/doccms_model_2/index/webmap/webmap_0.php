<?php	
if($data['isExternalLinks'])
{
	if(!$data['deep'])
	{
	?>
	<li><?php echo $data['prefix']; ?><a href="<?php echo $data['redirectUrl']; ?>" title="<?php echo $data['summary']?$data['summary']:$data['title']; ?>" target="_blank" class="jt" ><?php echo $data['title']; ?></a></li>
	<?php	
	}
	else
	{
	?>
	<li><?php echo $data['prefix']; ?><a href="<?php echo $data['redirectUrl']; ?>" title="<?php echo $data['summary']?$data['summary']:$data['title']; ?>" target="_blank" class="yl"><?php echo $data['title']; ?></a></li>
	<?php
	}
}
else
{
	if(!$data['deep'])
	{
		?>
		<li><?php echo $data['prefix']; ?><a  href="<?php echo sys_href($data['id'])?>" title="<?php echo $data['summary']?$data['summary']:$data['title']; ?>" target="_blank" class="jt" ><?php echo $data['title']; ?></a></li>
		<?php	
	}
	else
	{
		?>
		<li><?php echo $data['prefix']; ?><a href="<?php echo sys_href($data['id'])?>" title="<?php echo $data['summary']?$data['summary']:$data['title']; ?>" class="yl" target="_blank" ><?php echo $data['title']; ?></a></li>
		<?php
	}
}
?>	
