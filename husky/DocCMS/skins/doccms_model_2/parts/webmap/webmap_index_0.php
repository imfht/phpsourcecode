<?php
    // 为方便并保证您以后的快速升级 请使用SHL提供的如下全局数组
	
	// 数组定义/config/doc-global.php
	
	// 如有需要， 请去掉注释，输出数据。
	/*
	echo '<pre>';
		print_r($tag);
	echo '</pre>';
	*/
?>
<style type="text/css">
<!--
#webmap{ width:420px; height:auto; padding:25px; margin:0 auto;}
#webmap ul{ margin:0; padding:0; float:left;}
#webmap ul li{ list-style:none; line-height:22px; color:#999;}
#webmap ul li a{ text-decoration: none;letter-spacing: 1px;}
#webmap ul li a:hover{ color:#f90;}
.jt{ background:url(<?php echo $tag['path.root']?>/inc/img/webmap/arrow_r.gif) 0 2px no-repeat; color:#36f; font-weight:bold;padding-left:10px;}
.yl{ background:url(<?php echo $tag['path.root']?>/inc/img/webmap/jj.gif) 5px no-repeat; color:#39f; padding: 7px 0 0 35px;}
.el{ background:url(<?php echo $tag['path.root']?>/inc/img/webmap/tree_line1-1.gif) 20px no-repeat;color:#999; padding-left:43px;}
.ys{ color:#999;}
-->
</style>
<div id="webmap">
	<ul>	
<?php
if(!empty($tag['data.results']))
{
	foreach ($tag['data.results'] as $k=>$data)
	{
		if($data['isExternalLinks'])
		{
			if(!$data['deep'])
			{
			?>
			<li><?php echo $data['prefix']; ?><a href="<?php echo $data['redirectUrl']; ?>" title="<?php echo $data['summary']?$data['summary']:$data['title']; ?>" target="_blank" class="jt" ><?php echo $data['title']; ?></a></li>
			<?php	
			}else{
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
			<li><?php echo $data['prefix']; ?><a href="<?php echo sys_href($data['id'])?>" title="<?php echo $data['summary']?$data['summary']:$data['title']; ?>"  target="_blank" class="jt"><?php echo $data['title']; ?></a></li>
			<?php	
			}else{
			?>
			<li><?php echo $data['prefix']; ?><a href="<?php echo sys_href($data['id'])?>" title="<?php echo $data['summary']?$data['summary']:$data['title']; ?>" class="yl" target="_blank"><?php echo $data['title']; ?></a></li>
			<?php
			}	
		}
	}
}
?>	
	</ul>
</div>